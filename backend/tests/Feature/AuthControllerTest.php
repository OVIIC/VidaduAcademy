<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SecurityLog;
use App\Models\FailedLoginAttempt;
use App\Models\SuspiciousActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiters with IP-based keys
        \Illuminate\Support\Facades\RateLimiter::clear('register:127.0.0.1');
        \Illuminate\Support\Facades\RateLimiter::clear('login:127.0.0.1');
        \Illuminate\Support\Facades\RateLimiter::clear('password_change:127.0.0.1');
    }

    /** @test */
    public function it_registers_a_new_user_successfully()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe'
        ]);


    }

    /** @test */
    public function it_prevents_registration_with_weak_passwords()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_logs_in_user_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('validpassword123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'validpassword123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user',
            'token'
        ]);

        // Check that a successful login was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'authentication'
        ]);
    }

    /** @test */
    public function it_rejects_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials'
        ]);

        // Check that the failed login was logged
        $this->assertDatabaseHas('failed_login_attempts', [
            'email' => 'user@example.com'
        ]);
    }

    /** @test */
    public function it_implements_account_lockout_after_multiple_failures()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        $user = User::factory()->create([
            'email' => 'lockout@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        // Make 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'lockout@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // Try to login with correct password - should be locked
        $response = $this->postJson('/api/auth/login', [
            'email' => 'lockout@example.com',
            'password' => 'correctpassword'
        ]);

        $response->assertStatus(423);
        $response->assertJson([
            'message' => 'Account is temporarily locked due to multiple failed login attempts.'
        ]);


    }

    /** @test */
    public function it_allows_password_change_with_valid_credentials()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123')
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'oldpassword123',
            'password' => 'NewPass456!',
            'password_confirmation' => 'NewPass456!'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Password changed successfully'
        ]);

        // Verify password was actually changed
        $user->refresh();
        $this->assertTrue(Hash::check('NewPass456!', $user->password));


    }

    /** @test */
    public function it_rejects_password_change_with_incorrect_current_password()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        $user = User::factory()->create([
            'password' => Hash::make('correctpassword')
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Current password is incorrect'
        ]);


    }

    /** @test */
    public function it_logs_out_user_successfully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logout successful'
        ]);

        // Check that logout was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'authentication'
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_protected_routes()
    {
        // Try to change password without authentication
        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'old',
            'password' => 'new',
            'password_confirmation' => 'new'
        ]);

        $response->assertStatus(401);

        // Try to logout without authentication
        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_registration_input()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        // Test missing required fields
        $response = $this->postJson('/api/auth/register', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email']);

        // Test invalid email format
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test password confirmation mismatch
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_prevents_duplicate_email_registration()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_handles_csp_violation_reports()
    {
        $violationData = [
            'csp-report' => [
                'document-uri' => 'https://example.com',
                'violated-directive' => 'script-src',
                'blocked-uri' => 'https://malicious.com/script.js'
            ]
        ];

        $response = $this->postJson('/api/security/violations', $violationData);

        $response->assertStatus(201);
        $response->assertJson(['status' => 'logged']);

        // Verify CSP violation was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'csp_violation'
        ]);
    }

    /** @test */
    public function it_detects_and_blocks_suspicious_activities()
    {
        $this->withoutMiddleware([\App\Http\Middleware\CustomRateLimiter::class]);

        // Test XSS attempt in registration
        $response = $this->postJson('/api/auth/register', [
            'name' => '<script>alert("XSS")</script>',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(403);

        // Verify suspicious activity was logged
        $this->assertDatabaseHas('suspicious_activities', [
            'type' => 'suspicious_registration',
            'blocked' => true
        ]);
    }
}
