<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentVerificationController extends Controller
{
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
            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Retrieve session from Stripe
            $session = Session::retrieve($sessionId);
            
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
                    
                    $user = User::find($userId);
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
                        'status' => 'completed',
                        'purchased_at' => now(),
                    ]);
                } else {
                    // Update existing purchase
                    $purchase->update([
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'status' => 'completed',
                        'purchased_at' => now(),
                        'refunded_at' => null,
                    ]);
                }
                
                // Ensure user is enrolled
                $user = $purchase->user;
                $course = $purchase->course;
                
                if (!$user->isEnrolledIn($course)) {
                    $enrollment = $user->enrollments()->create([
                        'course_id' => $course->id,
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                    ]);
                    
                    Log::info('Enrollment created', [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'enrollment_id' => $enrollment->id
                    ]);
                } else {
                    Log::info('User already enrolled', [
                        'user_id' => $user->id,
                        'course_id' => $course->id
                    ]);
                }
                
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
