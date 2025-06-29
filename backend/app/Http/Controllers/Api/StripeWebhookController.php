<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            
            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id
            ]);

            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;
                    
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;
                    
                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
            }

            return response('Webhook handled', 200);
            
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
            
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
            
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Server error', 500);
        }
    }

    private function handleCheckoutSessionCompleted($session): void
    {
        Log::info('Processing checkout session completed', [
            'session_id' => $session->id,
            'payment_status' => $session->payment_status
        ]);

        if ($session->payment_status === 'paid') {
            $purchase = Purchase::where('stripe_session_id', $session->id)->first();
            
            if ($purchase) {
                // Update purchase status
                $purchase->update([
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'purchased_at' => now(),
                ]);
                
                // Create enrollment if not exists
                $enrollment = Enrollment::where('user_id', $purchase->user_id)
                    ->where('course_id', $purchase->course_id)
                    ->first();
                    
                if (!$enrollment) {
                    Enrollment::create([
                        'user_id' => $purchase->user_id,
                        'course_id' => $purchase->course_id,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);
                    
                    Log::info('Enrollment created via webhook', [
                        'user_id' => $purchase->user_id,
                        'course_id' => $purchase->course_id
                    ]);
                }
                
                Log::info('Purchase completed via webhook', [
                    'purchase_id' => $purchase->id,
                    'user_id' => $purchase->user_id,
                    'course_id' => $purchase->course_id
                ]);
            } else {
                Log::warning('Purchase not found for completed session', [
                    'session_id' => $session->id
                ]);
            }
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        Log::info('Processing payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id
        ]);

        // Additional backup processing if needed
        $purchase = Purchase::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        
        if ($purchase && $purchase->status !== 'completed') {
            $purchase->update([
                'status' => 'completed',
                'purchased_at' => now(),
            ]);
            
            Log::info('Purchase completed via payment intent webhook', [
                'purchase_id' => $purchase->id
            ]);
        }
    }
}
