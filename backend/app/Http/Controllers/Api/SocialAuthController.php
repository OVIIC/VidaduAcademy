<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)
            ->stateless()
            ->redirectUrl(config("services.$provider.redirect"))
            ->redirect();
    }

    /**
     * Obtain the user information from the provider.
     */
    public function handleProviderCallback($provider)
    {
        error_log("SocialAuthController: Handling callback for $provider");
        try {
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->redirectUrl(config("services.$provider.redirect"))
                ->user();
            error_log("SocialAuthController: User retrieved from provider. Email: " . $socialUser->getEmail());
        } catch (\Exception $e) {
            error_log("SocialAuthController: Exception during Socialite user retrieval: " . $e->getMessage());
            return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/login?error=Unable to authenticate');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            error_log("SocialAuthController: Found existing user: " . $user->id);
            // Update existing user
            $user->update([
                'google_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(), // Trust Google verification
            ]);
        } else {
            error_log("SocialAuthController: Creating new user");
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'google_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => \Illuminate\Support\Facades\Hash::make(Str::random(32)), // Generate random password for social users
                'email_verified_at' => now(),
            ]);
            
            // Assign default role
            $user->assignRole('student');
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Generate a short, random OTP
        $otp = Str::random(40);
        error_log("SocialAuthController: Generated OTP: $otp");
        
        // Store the token in cache for 1 minute
        // We use the OTP as the key to retrieve the token later
        \Illuminate\Support\Facades\Cache::put('social_otp_' . $otp, $token, now()->addMinute());

        // Redirect to frontend with OTP
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $redirectUrl = $frontendUrl . '/auth/callback/' . $provider . '?otp=' . $otp;
        error_log("SocialAuthController: Redirecting to: $redirectUrl");
        
        return redirect($redirectUrl);
    }

    /**
     * Exchange OTP for the actual auth token.
     */
    public function exchangeToken(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $otp = $request->otp;
        $cacheKey = 'social_otp_' . $otp;

        $token = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$token) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        // Remove the OTP from cache (one-time use)
        \Illuminate\Support\Facades\Cache::forget($cacheKey);

        // Return the token and user info
        // We can also return the user here if needed, but the frontend fetches it separately usually
        return response()->json([
            'token' => $token,
            'message' => 'Token exchanged successfully'
        ]);
    }
}
