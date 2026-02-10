<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Services\EnrollmentService;
use App\Models\Course;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private StripeService $stripeService,
        private EnrollmentService $enrollmentService
    ) {}

    public function createCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = Auth::user();

        // Check if user already has access to this course
        if ($user->hasPurchased($course)) {
            return response()->json([
                'error' => 'You already have access to this course'
            ], 422);
        }

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:3001');
            
            $sessionUrl = $this->stripeService->createCheckoutSession(
                $course,
                $user,
                $request->get('success_url', $frontendUrl . '/payment/success?session_id={CHECKOUT_SESSION_ID}'),
                $request->get('cancel_url', $frontendUrl . '/course/' . $course->slug)
            );

            return response()->json(['checkout_url' => $sessionUrl]);
        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Unable to create checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        try {
            $this->stripeService->handleWebhook($request);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Webhook processing failed'
            ], 400);
        }
    }

    public function checkCoursePurchaseStatus($courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        $hasPurchased = $user->hasPurchased($course);
        $isEnrolled = $user->isEnrolledIn($course);

        return response()->json([
            'has_purchased' => $hasPurchased,
            'is_enrolled' => $isEnrolled,
            'course_id' => $course->id,
            'course_title' => $course->title,
        ]);
    }

    public function purchaseHistory(): JsonResponse
    {
        $purchases = Auth::user()
            ->purchases()
            ->with('course')
            ->where('status', 'completed')
            ->orderBy('purchased_at', 'desc')
            ->paginate(10);

        return response()->json($purchases);
    }

    public function simulatePurchase(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = Auth::user();
        $course = Course::findOrFail($request->course_id);

        try {
            // Create a purchase record
            $purchase = \App\Models\Purchase::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'stripe_session_id' => 'sim_' . uniqid(),
                'stripe_payment_intent_id' => 'pi_sim_' . uniqid(),
                'amount' => $course->price,
                'currency' => 'EUR',
                'status' => 'pending', // Pending initially, enrollment service will complete it
                'purchased_at' => now(),
            ]);

            // Enroll the user securely
            $this->enrollmentService->enrollUser($user, $course, $purchase);

            return response()->json([
                'message' => 'Purchase simulated successfully',
                'purchase' => $purchase->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Simulation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        $sessionId = $request->get('session_id');
        
        Log::info('Payment verification started', ['session_id' => $sessionId]);
        
        if (!$sessionId) {
            Log::error('Payment verification failed: No session ID provided');
            return response()->json(['error' => 'Session ID required'], 400);
        }

        try {
            // Set Stripe API key
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            // Retrieve session from Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            
            Log::info('Stripe session retrieved', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
                'status' => $session->status
            ]);
            
            if ($session->payment_status === 'paid') {
                // Find or create purchase record
                $purchase = Purchase::where('stripe_session_id', $sessionId)->first();
                
                if (!$purchase) {
                    // Create purchase from Stripe session metadata
                    $userId = $session->metadata->user_id ?? null;
                    $courseId = $session->metadata->course_id ?? null;
                    
                    if (!$userId || !$courseId) {
                        return response()->json(['error' => 'Invalid session metadata'], 400);
                    }
                    
                    $user = \App\Models\User::find($userId);
                    $course = Course::find($courseId);
                    
                    if (!$user || !$course) {
                        return response()->json(['error' => 'User or course not found'], 404);
                    }
                    
                    // Create purchase record
                    $purchase = Purchase::create([
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'stripe_session_id' => $sessionId,
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'amount' => ($session->amount_total ?? 0) / 100,
                        'currency' => $session->currency ?? 'eur',
                        'status' => 'pending', // Service will mark as completed
                        'purchased_at' => now(),
                    ]);
                } else {
                    $purchase->stripe_payment_intent_id = $session->payment_intent;
                    $purchase->save();
                    
                    // User and Course already available via relations
                    $user = $purchase->user;
                    $course = $purchase->course;
                }
                
                // Ensure user is enrolled securely
                $this->enrollmentService->enrollUser($user, $course, $purchase);
                
                Log::info('Payment verification completed successfully', [
                    'session_id' => $sessionId,
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'purchase_id' => $purchase->id
                ]);
                
                return response()->json([
                    'success' => true,
                    'course' => $course,
                    'payment' => [
                        'session_id' => $sessionId,
                        'payment_intent' => $session->payment_intent,
                        'amount' => ($session->amount_total ?? 0) / 100,
                        'currency' => $session->currency,
                        'status' => 'completed'
                    ]
                ]);
            } else {
                Log::warning('Payment not completed', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status
                ]);
                
                return response()->json([
                    'error' => 'Payment not completed',
                    'payment_status' => $session->payment_status
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error("Payment verification failed", [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Payment verification failed'], 500);
        }
    }
}
