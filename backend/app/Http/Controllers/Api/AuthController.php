<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\SecurityService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected AuditService $auditService;
    protected SecurityService $securityService;

    public function __construct(AuditService $auditService, SecurityService $securityService)
    {
        $this->auditService = $auditService;
        $this->securityService = $securityService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        // Check for suspicious activity
        if ($this->securityService->detectSuspiciousActivity(
            $request->getContent(), 
            $request->userAgent(), 
            $request->ip()
        )) {
            $this->auditService->logSuspiciousActivity(
                'suspicious_registration', 
                $request, 
                $request->getContent(), 
                6, 
                true
            );
            
            return response()->json([
                'message' => 'Registration request blocked due to security concerns.'
            ], 403);
        }

        // Rate limit registration attempts
        $key = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->auditService->logSuspiciousActivity(
                'registration_rate_limit', 
                $request, 
                null, 
                4, 
                true
            );
            
            return response()->json([
                'message' => 'Too many registration attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        try {
            $validatedData = $this->securityService->validateUserProfileData([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $request->validate([
                'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            ]);

            // Check if user already exists
            if (User::where('email', $validatedData['email'])->exists()) {
                RateLimiter::hit($key, 300); // 5 minutes decay
                
                $this->auditService->logSecurityEvent(
                    'authentication',
                    'registration_attempt_existing_email',
                    null,
                    'warning',
                    ['email' => $validatedData['email']]
                );
                
                return response()->json([
                    'message' => 'A user with this email already exists.'
                ], 422);
            }

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            // Log successful registration
            $this->auditService->logAuthEvent('registration_success', $user->id, [
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'message' => 'Registration successful',
                'user' => $user->only(['id', 'name', 'email']),
                'token' => $token
            ], 201);

        } catch (ValidationException $e) {
            RateLimiter::hit($key, 300);
            
            $this->auditService->logSecurityEvent(
                'authentication',
                'registration_validation_failed',
                null,
                'warning',
                ['errors' => $e->errors(), 'ip' => $request->ip()]
            );
            
            throw $e;
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $email = $request->email;
        $ip = $request->ip();

        // Check if account is locked
        if ($this->auditService->isAccountLocked($email, $ip)) {
            return response()->json([
                'message' => 'Account is temporarily locked due to multiple failed login attempts.'
            ], 423);
        }

        // Check for suspicious activity
        if ($this->securityService->detectSuspiciousActivity(
            $request->getContent(), 
            $request->userAgent(), 
            $ip
        )) {
            $this->auditService->logSuspiciousActivity(
                'suspicious_login', 
                $request, 
                $request->getContent(), 
                7, 
                true
            );
            
            return response()->json([
                'message' => 'Login request blocked due to security concerns.'
            ], 403);
        }

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log failed login attempt
            $isLocked = $this->auditService->logFailedLogin($email, $request);
            
            $message = 'Invalid credentials';
            if ($isLocked) {
                $message = 'Account has been temporarily locked due to multiple failed attempts.';
            }
            
            return response()->json(['message' => $message], 401);
        }

        // Check if user account is active
        if (!$user->is_active) {
            $this->auditService->logAuthEvent('login_inactive_account', $user->id, [
                'email' => $email,
                'ip' => $ip
            ]);
            
            return response()->json([
                'message' => 'Account is deactivated. Please contact support.'
            ], 403);
        }

        // Clear failed login attempts on successful login
        $this->auditService->clearFailedLogins($email, $ip);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log successful login
        $this->auditService->logAuthEvent('login_success', $user->id, [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->only(['id', 'name', 'email', 'is_instructor']),
            'token' => $token
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user) {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();
            
            // Log logout
            $this->auditService->logAuthEvent('logout', $user->id, [
                'ip' => $request->ip()
            ]);
        }

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'user' => $user->only([
                'id', 'name', 'email', 'phone', 'location', 'bio', 
                'avatar', 'website', 'youtube_channel', 'subscribers_count',
                'content_niche', 'goals', 'is_instructor', 'is_active'
            ])
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        // Ensure the user is authenticated
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Detect suspicious activity before validation
        if ($this->securityService->detectSuspiciousActivity(
            $request->getContent(),
            $request->userAgent(),
            $request->ip()
        )) {
            // Log without assuming a user ID
            $this->auditService->logSuspiciousActivity(
                'suspicious_password_change',
                $request,
                $request->getContent(),
                9
            );
            return response()->json([
                'message' => 'Password change request blocked due to security concerns.'
            ], 403);
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:current_password'
            ],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            $this->auditService->logSecurityEvent(
                'authentication',
                'password_change_failed_verification',
                $user->id,
                'warning',
                ['ip' => $request->ip()]
            );
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Revoke all existing tokens except current
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        // Log password change
        $this->auditService->logSecurityEvent(
            'authentication',
            'password_changed',
            $user->id,
            'info',
            ['ip' => $request->ip()]
        );

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Revoke all tokens (logout from all devices)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Log logout from all devices
        $this->auditService->logSecurityEvent(
            'authentication',
            'logout_all_devices',
            $user->id,
            'info',
            ['ip' => $request->ip()]
        );

        return response()->json([
            'message' => 'Logged out from all devices successfully'
        ]);
    }
}
