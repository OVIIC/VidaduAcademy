<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['instructor'])
            ->where('status', 'published');

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json($courses);
    }

    public function featured(): JsonResponse
    {
        $courses = Course::with(['instructor'])
            ->where('status', 'published')
            ->where('featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($courses);
    }

    public function show(string $slug): JsonResponse
    {
        $course = Course::with(['instructor', 'lessons' => function($query) {
            $query->where('status', 'published')->orderBy('order');
        }])
        ->where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

        return response()->json($course);
    }

    public function byInstructor(User $instructor): JsonResponse
    {
        $courses = Course::where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'instructor' => $instructor,
            'courses' => $courses
        ]);
    }
}
