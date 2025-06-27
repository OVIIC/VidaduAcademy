<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get current user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->user()->id)
            ],
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'youtube_channel' => 'nullable|url|max:255',
            'subscriber_count' => 'nullable|string|in:0-100,100-1000,1000-10000,10000-100000,100000+',
            'content_niche' => 'nullable|string|in:gaming,education,entertainment,lifestyle,technology,beauty,cooking,travel,fitness,music,other',
            'goals' => 'nullable|string|in:monetization,growth,engagement,content,analytics,branding',
            'email_notifications' => 'boolean',
            'course_reminders' => 'boolean',
            'marketing_emails' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->update($validator->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['The current password is incorrect.']
                ]
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Get user certificates/achievements
     */
    public function certificates(Request $request)
    {
        $user = $request->user();
        
        // Get completed courses as certificates
        $certificates = $user->enrollments()
            ->with('course')
            ->where('progress', 100)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'course_title' => $enrollment->course->title,
                    'course_slug' => $enrollment->course->slug,
                    'completed_at' => $enrollment->completed_at,
                    'progress' => $enrollment->progress,
                    'certificate_url' => route('api.certificate.download', $enrollment->id)
                ];
            });

        return response()->json([
            'certificates' => $certificates
        ]);
    }

    /**
     * Download certificate for completed course
     */
    public function downloadCertificate(Request $request, $enrollmentId)
    {
        $user = $request->user();
        $enrollment = $user->enrollments()
            ->with('course')
            ->where('id', $enrollmentId)
            ->where('progress', 100)
            ->firstOrFail();

        // For now, return a simple PDF or redirect to certificate generation service
        // This would typically generate a PDF certificate
        return response()->json([
            'message' => 'Certificate download functionality will be implemented',
            'enrollment' => $enrollment,
            'download_url' => '#' // Placeholder for actual certificate download
        ]);
    }

    /**
     * Get user account data export
     */
    public function exportData(Request $request)
    {
        $user = $request->user();
        
        $data = [
            'user' => $user->toArray(),
            'enrollments' => $user->enrollments()->with('course')->get()->toArray(),
            'lesson_progress' => $user->lessonProgress()->with('lesson')->get()->toArray(),
            'export_date' => now()->toISOString()
        ];

        return response()->json([
            'message' => 'User data export',
            'data' => $data
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'confirmation' => 'required|in:DELETE_ACCOUNT'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect',
                'errors' => [
                    'password' => ['The password is incorrect.']
                ]
            ], 422);
        }

        // Delete user data
        $user->enrollments()->delete();
        $user->lessonProgress()->delete();
        $user->tokens()->delete(); // Revoke all tokens
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }
}
