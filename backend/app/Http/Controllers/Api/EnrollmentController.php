<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {}

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
        if (!$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrolled_at' => 'nullable|date',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $course = Course::findOrFail($validated['course_id']);

        try {
            $enrollment = $this->enrollmentService->enrollUser($user, $course);
            
            // If date is overridden (unlikely in service usage but kept for compatibility if needed), 
            // update it manually after creation, or expand service to support it. 
            // For now, service uses now().
            if (isset($validated['enrolled_at'])) {
                $enrollment->update(['enrolled_at' => $validated['enrolled_at']]);
            }

            $enrollment->load(['user', 'course']);
            return response()->json($enrollment, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Enrollment failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove user enrollment from a course
     */
    public function unenrollUser(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $course = Course::findOrFail($validated['course_id']);

        try {
            $success = $this->enrollmentService->unenrollUser($user, $course);
            
            if (!$success) {
                return response()->json(['message' => 'Enrollment not found'], 404);
            }

            return response()->json(['message' => 'User unenrolled successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unenrollment failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update enrollment progress
     */
    public function updateProgress(Request $request, Enrollment $enrollment): JsonResponse
    {
        // Allow admins to update any progress, or the user who owns the enrollment
        if (!$request->user()->hasRole('admin') && $request->user()->id !== $enrollment->user_id) {
             return response()->json(['message' => 'Unauthorized.'], 403);
        }

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
        $course = Course::findOrFail($courseId);

        // Security Check: Prevent free enrollment in paid courses
        if ($course->price > 0) {
            // Check if user has a completed purchase for this course
            $hasPurchased = $user->purchases()
                ->where('course_id', $course->id)
                ->where('status', 'completed')
                ->exists();

            if (!$hasPurchased) {
                return response()->json([
                    'message' => 'This is a paid course. You must purchase it first.'
                ], 403);
            }
        }

        try {
            $enrollment = $this->enrollmentService->enrollUser($user, $course);
            $enrollment->load(['user', 'course']);

            return response()->json($enrollment, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Enrollment failed: ' . $e->getMessage()], 500);
        }
    }
}
