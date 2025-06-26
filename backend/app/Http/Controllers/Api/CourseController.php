<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['category', 'instructor'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'popular') {
            $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $courses = $query->paginate($request->get('per_page', 12));

        return response()->json($courses);
    }

    public function show(string $slug): JsonResponse
    {
        $course = Course::with([
            'category',
            'instructor',
            'lessons' => function ($query) {
                $query->where('status', 'published')->orderBy('order');
            }
        ])
        ->where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

        return response()->json($course);
    }

    public function featured(): JsonResponse
    {
        $courses = Course::with(['category', 'instructor'])
            ->where('status', 'published')
            ->where('featured', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($courses);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::where('active', true)
            ->withCount(['courses' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function byInstructor(int $instructorId): JsonResponse
    {
        $courses = Course::with(['category'])
            ->where('instructor_id', $instructorId)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($courses);
    }
}
