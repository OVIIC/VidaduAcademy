<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock StripeService to avoid real API calls
        $this->mock(StripeService::class, function ($mock) {
            $mock->shouldReceive('createCheckoutSession')
                ->andReturn('https://checkout.stripe.com/test-session');
        });
    }

    public function test_authenticated_user_can_initiate_checkout()
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create([
            'price' => 99.00
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/payments/checkout', [
                'course_id' => $course->id
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['checkout_url']);
    }

    public function test_user_cannot_purchase_owned_course()
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();

        // Create completed purchase
        \App\Models\Purchase::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'currency' => 'EUR',
            'status' => 'completed',
            'stripe_session_id' => 'sess_test',
            'stripe_payment_intent_id' => 'pi_test'
        ]);

        // Enroll user manually
        $user->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/payments/checkout', [
                'course_id' => $course->id
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'You already have access to this course'
            ]);
    }

    public function test_unauthenticated_user_cannot_initiate_checkout()
    {
        $course = Course::factory()->published()->create();

        $response = $this->postJson('/api/payments/checkout', [
            'course_id' => $course->id
        ]);

        $response->assertStatus(401);
    }
}
