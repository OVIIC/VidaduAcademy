<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::where('status', 'published');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

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
        $courses = Course::with(['category', 'instructor'])
            ->where('status', 'published')
            ->where('featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($courses);
    }

    public function show(string $slug): JsonResponse
    {
        $course = Course::with(['category', 'instructor', 'lessons' => function($query) {
            $query->where('status', 'published')->orderBy('order');
        }])
        ->where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

        return response()->json($course);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::withCount(['courses' => function($query) {
            $query->where('status', 'published');
        }])->get();

        return response()->json($categories);
    }

    public function byInstructor(User $instructor): JsonResponse
    {
        $courses = Course::with(['category'])
            ->where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'instructor' => $instructor,
            'courses' => $courses
        ]);
    }
}
