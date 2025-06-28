<?php

namespace App\Services;

use App\Models\SecurityLog;
use App\Models\FailedLoginAttempt;
use App\Models\SuspiciousActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuditService
{
    /**
     * Log security event
     */
    public function logSecurityEvent(
        string $eventType,
        string $action,
        ?int $userId = null,
        string $severity = 'info',
        array $metadata = [],
        ?string $resourceType = null,
        ?int $resourceId = null,
        bool $isSuspicious = false
    ): void {
        try {
            SecurityLog::create([
                'event_type' => $eventType,
                'severity' => $severity,
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => $action,
                'metadata' => $metadata,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'is_suspicious' => $isSuspicious,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log security event', [
                'event_type' => $eventType,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log user authentication events
     */
    public function logAuthEvent(string $action, ?int $userId = null, array $metadata = []): void
    {
        $this->logSecurityEvent(
            'authentication',
            $action,
            $userId,
            'info',
            array_merge($metadata, [
                'timestamp' => now()->toISOString(),
                'session_id' => session()->getId()
            ])
        );
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        $attempt = FailedLoginAttempt::firstOrCreate(
            ['email' => $email, 'ip_address' => $ip],
            [
                'user_agent' => $userAgent, 
                'attempts' => 0,
                'last_attempt' => now()
            ]
        );

        $attempt->increment('attempts');
        $attempt->update([
            'last_attempt' => now(),
            'user_agent' => $userAgent
        ]);

        // Lock account after 5 failed attempts
        if ($attempt->attempts >= 5) {
            $lockDuration = min(pow(2, $attempt->attempts - 5) * 15, 1440); // Max 24 hours
            $attempt->update(['locked_until' => now()->addMinutes($lockDuration)]);
            
            $this->logSecurityEvent(
                'authentication',
                'account_locked',
                null,
                'warning',
                [
                    'email' => $email,
                    'attempts' => $attempt->attempts,
                    'lock_duration_minutes' => $lockDuration
                ],
                null,
                null,
                true
            );

            return true; // Account is locked
        }

        $this->logSecurityEvent(
            'authentication',
            'login_failed',
            null,
            'warning',
            [
                'email' => $email,
                'attempts' => $attempt->attempts
            ]
        );

        return false; // Not locked yet
    }

    /**
     * Clear failed login attempts on successful login
     */
    public function clearFailedLogins(string $email, string $ip): void
    {
        FailedLoginAttempt::where('email', $email)
            ->where('ip_address', $ip)
            ->delete();
    }

    /**
     * Check if account is locked
     */
    public function isAccountLocked(string $email, string $ip): bool
    {
        $attempt = FailedLoginAttempt::where('email', $email)
            ->where('ip_address', $ip)
            ->first();

        if (!$attempt || !$attempt->locked_until) {
            return false;
        }

        if (now()->greaterThan($attempt->locked_until)) {
            $attempt->update(['locked_until' => null, 'attempts' => 0]);
            return false;
        }

        return true;
    }

    /**
     * Log suspicious activity
     */
    public function logSuspiciousActivity(
        string $type,
        Request $request,
        ?string $payload = null,
        int $riskScore = 1,
        bool $blocked = false
    ): void {
        try {
            SuspiciousActivity::create([
                'type' => $type,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
                'payload' => $payload,
                'endpoint' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'blocked' => $blocked,
                'risk_score' => $riskScore
            ]);

            // Log high-risk activities immediately
            if ($riskScore >= 7) {
                Log::critical('High-risk suspicious activity detected', [
                    'type' => $type,
                    'ip' => $request->ip(),
                    'user_id' => $request->user()?->id,
                    'endpoint' => $request->fullUrl(),
                    'risk_score' => $riskScore
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log suspicious activity', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log course purchase events
     */
    public function logPurchaseEvent(string $action, int $userId, int $courseId, array $metadata = []): void
    {
        $this->logSecurityEvent(
            'purchase',
            $action,
            $userId,
            'info',
            array_merge($metadata, [
                'course_id' => $courseId,
                'timestamp' => now()->toISOString()
            ]),
            'course',
            $courseId
        );
    }

    /**
     * Log enrollment events
     */
    public function logEnrollmentEvent(string $action, int $userId, int $courseId, array $metadata = []): void
    {
        $this->logSecurityEvent(
            'enrollment',
            $action,
            $userId,
            'info',
            array_merge($metadata, [
                'course_id' => $courseId,
                'timestamp' => now()->toISOString()
            ]),
            'course',
            $courseId
        );
    }

    /**
     * Log admin actions
     */
    public function logAdminAction(string $action, int $adminId, array $metadata = []): void
    {
        $this->logSecurityEvent(
            'admin',
            $action,
            $adminId,
            'warning',
            array_merge($metadata, [
                'admin_id' => $adminId,
                'timestamp' => now()->toISOString()
            ]),
            null,
            null,
            false
        );
    }

    /**
     * Get failed login count for email and/or IP
     */
    public function getFailedLoginCount(?string $email = null, ?string $ip = null, int $hours = 24): int
    {
        $query = FailedLoginAttempt::where('last_attempt', '>=', now()->subHours($hours));
        
        if ($email) {
            $query->where('email', $email);
        }
        
        if ($ip) {
            $query->where('ip_address', $ip);
        }
        
        return $query->sum('attempts');
    }

    /**
     * Get security analytics
     */
    public function getSecurityAnalytics(int $days = 7): array
    {
        $since = now()->subDays($days);
        
        return [
            'total_security_events' => SecurityLog::where('created_at', '>=', $since)->count(),
            'failed_login_attempts' => FailedLoginAttempt::where('last_attempt', '>=', $since)->sum('attempts'),
            'suspicious_activities' => SuspiciousActivity::where('created_at', '>=', $since)->count(),
            'high_risk_activities' => SuspiciousActivity::where('created_at', '>=', $since)
                ->where('risk_score', '>=', 7)->count(),
            'blocked_activities' => SuspiciousActivity::where('created_at', '>=', $since)
                ->where('blocked', true)->count(),
            'events_by_type' => SecurityLog::where('created_at', '>=', $since)
                ->groupBy('event_type')
                ->selectRaw('event_type, count(*) as count')
                ->pluck('count', 'event_type'),
            'top_ips' => SecurityLog::where('created_at', '>=', $since)
                ->groupBy('ip_address')
                ->selectRaw('ip_address, count(*) as count')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'ip_address')
        ];
    }

    /**
     * Clean up old logs
     */
    public function cleanupOldLogs(int $days = 30): int
    {
        $cutoff = now()->subDays($days);
        $cleaned = 0;
        
        $cleaned += SecurityLog::where('created_at', '<', $cutoff)->delete();
        $cleaned += FailedLoginAttempt::where('created_at', '<', $cutoff)->delete();
        $cleaned += SuspiciousActivity::where('created_at', '<', $cutoff)->delete();
        
        return $cleaned;
    }
}
