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

    /** @test */
    public function it_registers_a_new_user_successfully()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'securepassword123',
            'password_confirmation' => 'securepassword123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe'
        ]);

        // Check that a security log was created
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'user_registered'
        ]);
    }

    /** @test */
    public function it_prevents_registration_with_weak_passwords()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $response = $this->postJson('/api/register', $userData);

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

        $response = $this->postJson('/api/login', [
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
            'event_type' => 'login_success',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function it_rejects_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials'
        ]);

        // Check that the failed login was logged
        $this->assertDatabaseHas('failed_login_attempts', [
            'email' => 'user@example.com',
            'reason' => 'Invalid credentials'
        ]);
    }

    /** @test */
    public function it_implements_account_lockout_after_multiple_failures()
    {
        $user = User::factory()->create([
            'email' => 'lockout@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        // Make 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'lockout@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // Try to login with correct password - should be locked
        $response = $this->postJson('/api/login', [
            'email' => 'lockout@example.com',
            'password' => 'correctpassword'
        ]);

        $response->assertStatus(423);
        $response->assertJson([
            'message' => 'Account temporarily locked due to multiple failed login attempts. Please try again later.'
        ]);

        // Verify lockout was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'account_locked',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function it_allows_password_change_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123')
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => 'oldpassword123',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Password changed successfully'
        ]);

        // Verify password was actually changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));

        // Check that password change was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'password_changed',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function it_rejects_password_change_with_incorrect_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword')
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Current password is incorrect'
        ]);

        // Check that failed password change was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'password_change_failed',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function it_logs_out_user_successfully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out successfully'
        ]);

        // Check that logout was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'logout',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_protected_routes()
    {
        // Try to change password without authentication
        $response = $this->postJson('/api/change-password', [
            'current_password' => 'old',
            'new_password' => 'new',
            'new_password_confirmation' => 'new'
        ]);

        $response->assertStatus(401);

        // Try to logout without authentication
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_registration_input()
    {
        // Test missing required fields
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);

        // Test invalid email format
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test password confirmation mismatch
        $response = $this->postJson('/api/register', [
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
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
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

        $response = $this->postJson('/api/security/csp-violation', $violationData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'received']);

        // Verify CSP violation was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'csp_violation'
        ]);
    }

    /** @test */
    public function it_detects_and_blocks_suspicious_activities()
    {
        // Test XSS attempt in registration
        $response = $this->postJson('/api/register', [
            'name' => '<script>alert("XSS")</script>',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(403);

        // Verify suspicious activity was logged
        $this->assertDatabaseHas('suspicious_activities', [
            'activity_type' => 'suspicious_registration',
            'blocked' => true
        ]);
    }
}
