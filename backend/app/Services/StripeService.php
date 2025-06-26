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
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Course $course, User $user, string $successUrl, string $cancelUrl): string
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'client_reference_id' => $user->id,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($course->currency),
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
                'currency' => $course->currency,
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

        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event['data']['object']);
                break;

            default:
                Log::info('Unhandled Stripe webhook event type: ' . $event['type']);
        }
    }

    private function handleCheckoutSessionCompleted(array $session): void
    {
        $sessionId = $session['id'];
        $paymentIntentId = $session['payment_intent'];
        $userId = $session['metadata']['user_id'] ?? null;
        $courseId = $session['metadata']['course_id'] ?? null;

        if (!$userId || !$courseId) {
            Log::error('Missing metadata in checkout session: ' . $sessionId);
            return;
        }

        $purchase = Purchase::where('stripe_session_id', $sessionId)->first();

        if (!$purchase) {
            Log::error('Purchase not found for session: ' . $sessionId);
            return;
        }

        $purchase->update([
            'stripe_payment_intent_id' => $paymentIntentId,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        // Automatically enroll the user in the course
        $this->enrollUserInCourse($purchase->user, $purchase->course);

        Log::info("Course purchase completed: User {$userId} purchased Course {$courseId}");
    }

    private function handlePaymentIntentSucceeded(array $paymentIntent): void
    {
        $paymentIntentId = $paymentIntent['id'];

        $purchase = Purchase::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($purchase && $purchase->status !== 'completed') {
            $purchase->update([
                'status' => 'completed',
                'purchased_at' => now(),
            ]);

            $this->enrollUserInCourse($purchase->user, $purchase->course);
        }
    }

    private function handlePaymentIntentFailed(array $paymentIntent): void
    {
        $paymentIntentId = $paymentIntent['id'];

        $purchase = Purchase::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($purchase) {
            $purchase->update(['status' => 'failed']);
        }
    }

    private function enrollUserInCourse(User $user, Course $course): void
    {
        // Check if user is already enrolled
        if ($user->isEnrolledIn($course)) {
            return;
        }

        $user->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        Log::info("User {$user->id} enrolled in Course {$course->id}");
    }
}
