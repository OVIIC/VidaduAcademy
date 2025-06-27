<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    /**
     * Get user's enrolled courses
     */
    public function getUserCourses(User $user): JsonResponse
    {
        $enrollments = $user->enrollments()
            ->with(['course', 'lastAccessedLesson'])
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Enroll user in a course
     */
    public function enrollUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrolled_at' => 'nullable|date',
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'enrolled_at' => $validated['enrolled_at'] ?? now(),
            'progress_percentage' => 0,
        ]);

        $enrollment->load(['user', 'course']);

        return response()->json($enrollment, 201);
    }

    /**
     * Remove user enrollment from a course
     */
    public function unenrollUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $enrollment = Enrollment::where([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
        ])->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        $enrollment->delete();

        return response()->json(['message' => 'User unenrolled successfully']);
    }

    /**
     * Update enrollment progress
     */
    public function updateProgress(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'last_accessed_lesson_id' => 'nullable|exists:lessons,id',
            'completed_at' => 'nullable|date',
        ]);

        $enrollment->update($validated);

        return response()->json($enrollment);
    }

    /**
     * Get course enrollments (for instructors/admins)
     */
    public function getCourseEnrollments(Course $course): JsonResponse
    {
        $enrollments = $course->enrollments()
            ->with(['user'])
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Get current user's enrolled courses
     */
    public function getMyEnrolledCourses(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $enrollments = $user->enrollments()
            ->with(['course.instructor'])
            ->orderBy('enrolled_at', 'desc')
            ->get();

        $courses = $enrollments->map(function ($enrollment) {
            $course = $enrollment->course;
            $course->enrollment_data = [
                'enrolled_at' => $enrollment->enrolled_at,
                'progress_percentage' => $enrollment->progress_percentage,
                'completed_at' => $enrollment->completed_at,
                'last_accessed_at' => $enrollment->last_accessed_at,
            ];
            return $course;
        });

        return response()->json([
            'data' => $courses
        ]);
    }

    /**
     * Enroll authenticated user in a course (self-enrollment)
     */
    public function enrollSelf(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();
        $courseId = $validated['course_id'];

        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where([
            'user_id' => $user->id,
            'course_id' => $courseId,
        ])->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'User is already enrolled in this course',
                'enrollment' => $existingEnrollment->load(['course'])
            ], 200);
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        $enrollment->load(['user', 'course']);

        return response()->json($enrollment, 201);
    }
}
