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
        
        if (!$sessionId) {
            return response()->json(['error' => 'Session ID required'], 400);
        }

        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Retrieve session from Stripe
            $session = Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                // Find purchase by session ID
                $purchase = Purchase::where('stripe_session_id', $sessionId)->first();
                
                if (!$purchase) {
                    Log::error("Purchase not found for session: {$sessionId}");
                    return response()->json(['error' => 'Purchase not found'], 404);
                }
                
                // Update purchase status if still pending
                if ($purchase->status === 'pending') {
                    $purchase->update([
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'status' => 'completed',
                        'purchased_at' => now(),
                    ]);
                    
                    Log::info("Purchase {$purchase->id} marked as completed");
                }
                
                // Ensure user is enrolled
                $user = $purchase->user;
                $course = $purchase->course;
                
                if (!$user->isEnrolledIn($course)) {
                    $user->enrollments()->create([
                        'course_id' => $course->id,
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                    ]);
                    
                    Log::info("User {$user->id} enrolled in Course {$course->id}");
                }
                
                return response()->json([
                    'success' => true,
                    'course' => $course,
                    'payment' => [
                        'session_id' => $sessionId,
                        'payment_intent' => $session->payment_intent,
                        'amount' => $session->amount_total / 100, // Convert from cents
                        'currency' => $session->currency,
                        'status' => 'completed'
                    ]
                ]);
            } else {
                return response()->json([
                    'error' => 'Payment not completed',
                    'payment_status' => $session->payment_status
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error("Payment verification failed: {$e->getMessage()}");
            return response()->json(['error' => 'Payment verification failed'], 500);
        }
    }
}
