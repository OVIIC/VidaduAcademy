<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\Purchase;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Course $course, User $user, ?string $successUrl = null, ?string $cancelUrl = null): string
    {
        // Provide default URLs if none provided
        $successUrl = $successUrl ?: config('app.frontend_url', 'http://localhost:3005') . '/payment/success';
        $cancelUrl = $cancelUrl ?: config('app.frontend_url', 'http://localhost:3005') . '/course/' . $course->slug;
        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'client_reference_id' => $user->id,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => config('services.stripe.currency'),
                        'unit_amount' => $course->price * 100, // Stripe expects amount in cents
                        'product_data' => [
                            'name' => $course->title,
                            'description' => $course->short_description,
                            'images' => $course->thumbnail ? [asset('storage/' . $course->thumbnail)] : [],
                            'metadata' => [
                                'course_id' => $course->id,
                                'course_slug' => $course->slug,
                            ],
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
        ]);

        // Create a pending purchase record
        Purchase::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'stripe_session_id' => $session->id,
                'amount' => $course->price,
                'currency' => config('services.stripe.currency'),
                'status' => 'pending',
            ]
        );

        return $session->url;
    }

    public function handleWebhook(Request $request): void
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            throw $e;
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event type: ' . $event->type);
        }
    }

    private function handleCheckoutSessionCompleted($session): void
    {
        $sessionId = $session->id;
        $paymentIntentId = $session->payment_intent;
        $userId = $session->metadata->user_id ?? null;
        $courseId = $session->metadata->course_id ?? null;

        if (!$userId || !$courseId) {
            Log::error('Missing metadata in checkout session: ' . $sessionId);
            return;
        }

        $purchase = Purchase::where('stripe_session_id', $sessionId)->first();

        if (!$purchase) {
            Log::error('Purchase not found for session: ' . $sessionId);
            return;
        }

        // Use EnrollmentService to atomically update purchase and enroll
        $purchase->stripe_payment_intent_id = $paymentIntentId;
        
        try {
            $this->enrollmentService->enrollUser($purchase->user, $purchase->course, $purchase);
            Log::info("Course purchase completed via Checkout Session: User {$userId} purchased Course {$courseId}");
        } catch (\Exception $e) {
            Log::error("Failed to enroll user after checkout session: " . $e->getMessage());
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        $paymentIntentId = $paymentIntent->id;
        $purchase = Purchase::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($purchase && $purchase->status !== 'completed') {
            try {
                $this->enrollmentService->enrollUser($purchase->user, $purchase->course, $purchase);
                Log::info("Course purchase completed via Payment Intent: User {$purchase->user_id} purchased Course {$purchase->course_id}");
            } catch (\Exception $e) {
                Log::error("Failed to enroll user after payment intent success: " . $e->getMessage());
            }
        }
    }

    private function handlePaymentIntentFailed($paymentIntent): void
    {
        $paymentIntentId = $paymentIntent->id;

        $purchase = Purchase::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($purchase) {
            $purchase->update(['status' => 'failed']);
        }
    }
}
