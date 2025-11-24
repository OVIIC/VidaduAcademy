<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private StripeService $stripeService
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
            \Log::error('Stripe checkout session creation failed: ' . $e->getMessage(), [
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

        // Create a completed purchase record
        $purchase = \App\Models\Purchase::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'stripe_session_id' => 'sim_' . uniqid(),
            'stripe_payment_intent_id' => 'pi_sim_' . uniqid(),
            'amount' => $course->price,
            'currency' => 'EUR',
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        // Enroll the user
        if (!$user->isEnrolledIn($course)) {
            $user->enrollments()->create([
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]);
        }

        return response()->json([
            'message' => 'Purchase simulated successfully',
            'purchase' => $purchase
        ]);
    }
}
