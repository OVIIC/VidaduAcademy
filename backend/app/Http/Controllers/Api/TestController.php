<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Create a test purchase and enrollment (only for development)
     */
    public function createTestPurchase(Request $request): JsonResponse
    {
        if (config('app.env') !== 'local') {
            return response()->json(['error' => 'Test endpoints only available in local environment'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);

        // Check if user already has this course
        if ($user->hasPurchased($course)) {
            return response()->json(['message' => 'User already has access to this course'], 200);
        }

        // Create test purchase
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'stripe_session_id' => 'cs_test_' . uniqid(),
            'stripe_payment_intent_id' => 'pi_test_' . uniqid(),
            'amount' => $course->price,
            'currency' => 'eur',
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        // Create enrollment
        if (!$user->isEnrolledIn($course)) {
            $user->enrollments()->create([
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test purchase and enrollment created',
            'purchase' => $purchase,
            'course' => $course,
            'user' => $user->only(['id', 'name', 'email'])
        ]);
    }

    /**
     * Get user's enrollments
     */
    public function getUserEnrollments($userId): JsonResponse
    {
        if (config('app.env') !== 'local') {
            return response()->json(['error' => 'Test endpoints only available in local environment'], 403);
        }

        $user = User::findOrFail($userId);
        $enrollments = $user->enrollments()->with('course')->get();
        $purchases = $user->purchases()->with('course')->get(); // Remove status filter to see all

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'enrollments' => $enrollments,
            'purchases' => $purchases,
            'has_purchased_check' => [
                'course_1' => $user->hasPurchased(\App\Models\Course::find(1)),
                'course_2' => $user->hasPurchased(\App\Models\Course::find(2)),
            ],
        ]);
    }

    /**
     * List all users and courses for testing
     */
    public function getTestData(): JsonResponse
    {
        if (config('app.env') !== 'local') {
            return response()->json(['error' => 'Test endpoints only available in local environment'], 403);
        }

        $users = User::select('id', 'name', 'email')->get();
        $courses = Course::select('id', 'title', 'slug', 'price')->get();

        return response()->json([
            'users' => $users,
            'courses' => $courses,
        ]);
    }
}
