<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class SecurityMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_adds_security_headers_to_responses()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Check Content Security Policy header exists
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    /** @test */
    public function it_enforces_rate_limiting_on_login()
    {
        $email = 'test@example.com';
        $password = 'wrongpassword';

        // Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => $email,
                'password' => $password
            ]);

            if ($i < 5) {
                // First 5 attempts should return 401 (unauthorized)
                $response->assertStatus(401);
            } else {
                // 6th attempt should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /** @test */
    public function it_blocks_suspicious_registration_attempts()
    {
        $maliciousData = [
            'name' => '<script>alert("XSS")</script>',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $maliciousData);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Registration request blocked due to security concerns.'
        ]);
    }

    /** @test */
    public function it_blocks_sql_injection_attempts()
    {
        $maliciousData = [
            'email' => "admin'; DROP TABLE users; --",
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $maliciousData);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_allows_legitimate_requests()
    {
        $user = User::factory()->create([
            'email' => 'legitimate@example.com',
            'password' => Hash::make('validpassword123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'legitimate@example.com',
            'password' => 'validpassword123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user',
            'token'
        ]);
    }

    /** @test */
    public function it_handles_account_lockout()
    {
        $user = User::factory()->create([
            'email' => 'lockout@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        // Make 5 failed attempts to trigger lockout
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'lockout@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // Now try with correct password - should be locked out
        $response = $this->postJson('/api/login', [
            'email' => 'lockout@example.com',
            'password' => 'correctpassword'
        ]);

        $response->assertStatus(423);
        $response->assertJson([
            'message' => 'Account temporarily locked due to multiple failed login attempts. Please try again later.'
        ]);
    }

    /** @test */
    public function it_handles_csp_violation_reports()
    {
        $violationReport = [
            'csp-report' => [
                'document-uri' => 'https://example.com/page',
                'referrer' => 'https://example.com/',
                'violated-directive' => 'script-src',
                'effective-directive' => 'script-src',
                'original-policy' => "default-src 'self'; script-src 'self'",
                'blocked-uri' => 'https://malicious.com/script.js',
                'status-code' => 200
            ]
        ];

        $response = $this->postJson('/api/security/csp-violation', $violationReport);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'received']);
        
        // Verify the violation was logged
        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'csp_violation'
        ]);
    }

    /** @test */
    public function it_validates_password_change_security()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123')
        ]);

        $this->actingAs($user, 'sanctum');

        // Test with suspicious new password
        $response = $this->postJson('/api/change-password', [
            'current_password' => 'oldpassword123',
            'new_password' => '<script>alert("XSS")</script>',
            'new_password_confirmation' => '<script>alert("XSS")</script>'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_rate_limits_password_change_attempts()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword')
        ]);

        $this->actingAs($user, 'sanctum');

        // Make multiple password change attempts with wrong current password
        for ($i = 0; $i < 4; $i++) {
            $response = $this->postJson('/api/change-password', [
                'current_password' => 'wrongpassword',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123'
            ]);

            if ($i < 3) {
                $response->assertStatus(400);
            } else {
                // 4th attempt should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear rate limiters before each test
        RateLimiter::clear('login:127.0.0.1');
        RateLimiter::clear('register:127.0.0.1');
        RateLimiter::clear('password-change:1');
    }
}
