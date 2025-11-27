<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AuditService;
use App\Models\SecurityLog;
use App\Models\FailedLoginAttempt;
use App\Models\SuspiciousActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditService $auditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auditService = new AuditService();
    }

    /** @test */
    public function it_logs_security_events()
    {
        $request = Request::create('/test', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Test Agent',
            'REMOTE_ADDR' => '127.0.0.1'
        ]);

        $this->auditService->logSecurityEvent(
            'test_event',
            'test_action',
            null,
            'info',
            ['test' => 'data']
        );

        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'test_event',
            'action' => 'test_action',
            'severity' => 'info',
            'ip_address' => '127.0.0.1'
        ]);
    }

    /** @test */
    public function it_logs_failed_login_attempts()
    {
        $request = Request::create('/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Test Agent',
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $this->auditService->logFailedLogin('test@example.com', $request);

        $this->assertDatabaseHas('failed_login_attempts', [
            'email' => 'test@example.com',
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Test Agent',
            'attempts' => 1
        ]);
    }

    /** @test */
    public function it_logs_suspicious_activities()
    {
        $request = Request::create('/api/test', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Suspicious Agent',
            'REMOTE_ADDR' => '10.0.0.1'
        ]);

        $this->auditService->logSuspiciousActivity(
            'xss_attempt',
            $request,
            '<script>alert("XSS")</script>',
            8,
            true
        );

        $this->assertDatabaseHas('suspicious_activities', [
            'type' => 'xss_attempt',
            'payload' => '<script>alert("XSS")</script>',
            'risk_score' => 8,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Suspicious Agent',
            'blocked' => true
        ]);
    }

    /** @test */
    public function it_gets_failed_login_count()
    {
        $request = Request::create('/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.100'
        ]);

        // Log multiple failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->auditService->logFailedLogin(
                'user@example.com',
                $request,
                'Test attempt ' . ($i + 1)
            );
        }

        $count = $this->auditService->getFailedLoginCount('user@example.com', '192.168.1.100');
        $this->assertEquals(3, $count);
    }

    /** @test */
    public function it_gets_security_analytics()
    {
        $request = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1'
        ]);

        // Create test data
        $this->auditService->logSecurityEvent('login_success', 'user_login', null, 'info');
        $this->auditService->logSecurityEvent('login_failed', 'failed_login', null, 'warning');
        $this->auditService->logFailedLogin('user1@test.com', $request);
        $this->auditService->logFailedLogin('user2@test.com', $request);
        $this->auditService->logSuspiciousActivity('xss_attempt', $request, 'XSS payload', 8, true);

        $analytics = $this->auditService->getSecurityAnalytics();

        $this->assertArrayHasKey('total_security_events', $analytics);
        $this->assertArrayHasKey('failed_login_attempts', $analytics);
        $this->assertArrayHasKey('suspicious_activities', $analytics);
        $this->assertArrayHasKey('events_by_type', $analytics);
        $this->assertArrayHasKey('top_ips', $analytics);
        
        $this->assertGreaterThanOrEqual(2, $analytics['total_security_events']);
        $this->assertEquals(2, $analytics['failed_login_attempts']);
        $this->assertEquals(1, $analytics['suspicious_activities']);
    }

    /** @test */
    public function it_cleans_up_old_logs()
    {
        $request = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1'
        ]);

        // Create old log entry using DB::table to avoid timestamp issues
        $oldLogId = \DB::table('security_logs')->insertGetId([
            'event_type' => 'old_event',
            'action' => 'old_action',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'severity' => 'info',
            'created_at' => now()->subDays(40)->toDateTimeString() // 40 days old
        ]);

        // Create recent log entry
        $this->auditService->logSecurityEvent('recent_event', 'recent_action', null, 'info');

        // Run cleanup (default 30 days)
        $cleaned = $this->auditService->cleanupOldLogs();

        $this->assertTrue($cleaned > 0);
        $this->assertDatabaseMissing('security_logs', ['id' => $oldLogId]);
        $this->assertDatabaseHas('security_logs', ['event_type' => 'recent_event']);
    }

    /** @test */
    public function it_detects_brute_force_attempts()
    {
        $request = Request::create('/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.200'
        ]);

        // Log multiple failed attempts from same IP
        for ($i = 0; $i < 6; $i++) {
            $this->auditService->logFailedLogin(
                "user{$i}@example.com",
                $request
            );
        }

        $count = $this->auditService->getFailedLoginCount(null, '192.168.1.200');
        $this->assertGreaterThanOrEqual(6, $count);
    }
}
