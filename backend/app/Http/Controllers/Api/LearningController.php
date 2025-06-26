<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public function enrolledCourses(): JsonResponse
    {
        $enrollments = Auth::user()
            ->enrollments()
            ->with(['course.category', 'course.instructor'])
            ->orderBy('enrolled_at', 'desc')
            ->paginate(12);

        return response()->json($enrollments);
    }

    public function courseContent(string $courseSlug): JsonResponse
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $user = Auth::user();

        // Check if user has access to this course
        if (!$user->hasPurchased($course) && !$user->isEnrolledIn($course)) {
            return response()->json([
                'error' => 'You do not have access to this course'
            ], 403);
        }

        $enrollment = $user->getProgressForCourse($course);
        $lessons = $course->lessons()
            ->where('status', 'published')
            ->orderBy('order')
            ->get()
            ->map(function ($lesson) use ($user) {
                $progress = $user->lessonProgress()
                    ->where('lesson_id', $lesson->id)
                    ->first();

                $lesson->user_progress = $progress ? [
                    'completed' => $progress->completed,
                    'watch_time_seconds' => $progress->watch_time_seconds,
                    'progress_percentage' => $progress->progress_percentage,
                ] : null;

                return $lesson;
            });

        return response()->json([
            'course' => $course,
            'enrollment' => $enrollment,
            'lessons' => $lessons,
        ]);
    }

    public function watchLesson(string $courseSlug, string $lessonSlug): JsonResponse
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = $course->lessons()
            ->where('slug', $lessonSlug)
            ->where('status', 'published')
            ->firstOrFail();

        $user = Auth::user();

        // Check access
        if (!$lesson->is_free && !$user->hasPurchased($course) && !$user->isEnrolledIn($course)) {
            return response()->json([
                'error' => 'You do not have access to this lesson'
            ], 403);
        }

        $enrollment = $user->getProgressForCourse($course);
        if (!$enrollment && $user->hasPurchased($course)) {
            // Create enrollment if user purchased but not enrolled
            $enrollment = $user->enrollments()->create([
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);
        }

        // Get or create lesson progress
        $progress = $user->lessonProgress()
            ->firstOrCreate(
                [
                    'lesson_id' => $lesson->id,
                ],
                [
                    'enrollment_id' => $enrollment?->id,
                    'watch_time_seconds' => 0,
                    'completed' => false,
                ]
            );

        return response()->json([
            'lesson' => $lesson,
            'progress' => $progress,
            'enrollment' => $enrollment,
        ]);
    }

    public function updateProgress(Request $request, string $courseSlug, string $lessonSlug): JsonResponse
    {
        $request->validate([
            'watch_time_seconds' => 'required|integer|min:0',
            'completed' => 'sometimes|boolean',
        ]);

        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = $course->lessons()
            ->where('slug', $lessonSlug)
            ->firstOrFail();

        $user = Auth::user();
        $enrollment = $user->getProgressForCourse($course);

        if (!$enrollment) {
            return response()->json([
                'error' => 'You are not enrolled in this course'
            ], 403);
        }

        $progress = $user->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->first();

        if (!$progress) {
            return response()->json([
                'error' => 'Lesson progress not found'
            ], 404);
        }

        $progress->update([
            'watch_time_seconds' => max($progress->watch_time_seconds, $request->watch_time_seconds),
            'completed' => $request->get('completed', $progress->completed),
            'completed_at' => $request->get('completed') ? now() : $progress->completed_at,
        ]);

        // Update enrollment progress
        $this->updateEnrollmentProgress($enrollment);

        return response()->json([
            'progress' => $progress->fresh(),
            'enrollment' => $enrollment->fresh(),
        ]);
    }

    private function updateEnrollmentProgress($enrollment): void
    {
        $totalLessons = $enrollment->course->lessons()->where('status', 'published')->count();
        $completedLessons = $enrollment->course->lessons()
            ->whereHas('progress', function ($query) use ($enrollment) {
                $query->where('user_id', $enrollment->user_id)
                    ->where('completed', true);
            })
            ->count();

        $progressPercentage = $totalLessons > 0 ? intval(($completedLessons / $totalLessons) * 100) : 0;

        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'completed_at' => $progressPercentage === 100 ? now() : null,
        ]);
    }
}
