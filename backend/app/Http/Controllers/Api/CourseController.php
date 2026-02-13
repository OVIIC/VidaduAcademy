<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request): JsonResponse
    {
        // Create cache key based on request parameters
        $cacheKey = 'courses:list:' . md5(serialize($request->all()));
        
        $courses = $this->cacheService->rememberCoursesList($cacheKey, function () use ($request) {
            $query = Course::select([
                'id', 'instructor_id', 'category_id', 'title', 'slug', 'short_description', 
                'price', 'currency', 'thumbnail', 'difficulty_level', 
                'duration_minutes', 'created_at'
            ])
            ->with(['instructor:id,name,email,subscribers_count', 'category:id,name,slug'])
            ->where('status', 'published');

            // Optimize search with full-text search if available
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                
                // Use full-text search if MySQL
                if (config('database.default') === 'mysql') {
                    $query->whereRaw("MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$search]);
                } else {
                    // Fallback to LIKE search
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'LIKE', "%{$search}%")
                          ->orWhere('description', 'LIKE', "%{$search}%");
                    });
                }
            }

            // Filter by difficulty
            if ($request->has('difficulty') && !empty($request->difficulty)) {
                $difficulty = (array) $request->difficulty;
                $query->whereIn('difficulty_level', $difficulty);
            }

            // Filter by category
            if ($request->has('category') && !empty($request->category)) {
                $categories = (array) $request->category;
                $query->whereHas('category', function ($q) use ($categories) {
                    $q->whereIn('slug', $categories)
                      ->orWhereIn('id', $categories);
                });
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Optimized ordering
            $orderBy = $request->get('sort_by', 'created_at');
            $orderDirection = $request->get('sort_order', 'desc');
            
            $allowedSorts = ['created_at', 'title', 'price', 'difficulty_level'];
            if (!in_array($orderBy, $allowedSorts)) {
                $orderBy = 'created_at';
            }

            // Validate direction
            $orderDirection = strtolower($orderDirection) === 'asc' ? 'asc' : 'desc';

            if ($orderBy === 'price') {
                return $query->orderBy('price', $orderDirection)->paginate(12);
            }

            return $query->orderBy($orderBy, $orderDirection)->paginate(12);
        });

        return response()->json($courses);
    }

    public function featured(): JsonResponse
    {
        $courses = $this->cacheService->rememberFeatured(function () {
            return Course::select([
                'id', 'instructor_id', 'title', 'slug', 'short_description', 
                'price', 'currency', 'thumbnail', 'difficulty_level', 
                'duration_minutes', 'created_at'
            ])
            ->with(['instructor:id,name,email,subscribers_count'])
            ->where('status', 'published')
            ->where('featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        });

        return response()->json($courses);
    }

    public function show(string $slug): JsonResponse
    {
        $cacheKey = 'course:slug:' . $slug;
        
        $course = $this->cacheService->remember($cacheKey, function () use ($slug) {
            return Course::select([
                'id', 'instructor_id', 'title', 'slug', 'description', 'short_description',
                'price', 'currency', 'thumbnail', 'difficulty_level', 'duration_minutes',
                'what_you_will_learn', 'requirements', 'status', 'created_at', 'updated_at'
            ])
            ->with([
                'instructor:id,name,email,avatar,bio,subscribers_count',
                'category:id,name,slug',
                'lessons' => function ($query) {
                    $query->select(['id', 'course_id', 'title', 'slug', 'duration_minutes', 'order', 'is_preview'])
                          ->where('status', 'published')
                          ->orderBy('order');
                }
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        }, 1800); // 30 minutes

        // Add computed fields
        $course->lessons_count = $course->lessons->count();
        $course->total_duration = $course->lessons->sum('duration_minutes');

        return response()->json($course);
    }

    public function byInstructor(User $instructor): JsonResponse
    {
        $cacheKey = 'courses:instructor:' . $instructor->id;
        
        $courses = $this->cacheService->remember($cacheKey, function () use ($instructor) {
            return Course::select([
                'id', 'instructor_id', 'title', 'slug', 'short_description',
                'price', 'currency', 'thumbnail', 'difficulty_level', 
                'duration_minutes', 'created_at'
            ])
            ->where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        }, 900); // 15 minutes

        return response()->json([
            'instructor' => $instructor->only(['id', 'name', 'email', 'avatar', 'bio', 'subscribers_count']),
            'courses' => $courses
        ]);
    }

    /**
     * Get course statistics for admin/instructor
     */
    public function stats(Request $request): JsonResponse
    {
        $cacheKey = 'course_stats:' . ($request->user()->id ?? 'admin');
        
        $stats = $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = Course::query();
            
            // Filter by instructor if not admin
            if ($request->user() && !$request->user()->hasRole('admin')) {
                $query->where('instructor_id', $request->user()->id);
            }

            return [
                'total_courses' => $query->count(),
                'published_courses' => $query->where('status', 'published')->count(),
                'draft_courses' => $query->where('status', 'draft')->count(),
                'featured_courses' => $query->where('featured', true)->count(),
                'total_enrollments' => DB::table('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->when($request->user() && !$request->user()->hasRole('admin'), function ($q) use ($request) {
                        return $q->where('courses.instructor_id', $request->user()->id);
                    })
                    ->count()
            ];
        }, 900); // 15 minutes cache

        return response()->json($stats);
    }

    /**
     * Search courses with advanced filters
     */
    public function search(Request $request): JsonResponse
    {
        $cacheKey = 'course_search:' . md5(json_encode($request->all()));
        
        $results = $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = Course::select([
                'id', 'title', 'slug', 'short_description', 'thumbnail', 
                'price', 'currency', 'difficulty_level', 'duration_minutes'
            ])
            ->where('status', 'published');

            // Advanced search
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                
                if (config('database.default') === 'mysql') {
                    $query->whereRaw("MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$searchTerm])
                          ->orWhere('title', 'LIKE', "%{$searchTerm}%");
                } else {
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('title', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('short_description', 'LIKE', "%{$searchTerm}%");
                    });
                }
            }

            // Filters
            if ($request->filled('difficulty')) {
                $query->whereIn('difficulty_level', (array) $request->difficulty);
            }

            if ($request->filled('price_range')) {
                $priceRange = $request->price_range;
                if ($priceRange === 'free') {
                    $query->where('price', 0);
                } elseif ($priceRange === 'paid') {
                    $query->where('price', '>', 0);
                }
            }

            if ($request->filled('duration')) {
                $duration = $request->duration;
                switch ($duration) {
                    case 'short':
                        $query->where('duration_minutes', '<=', 60);
                        break;
                    case 'medium':
                        $query->whereBetween('duration_minutes', [61, 180]);
                        break;
                    case 'long':
                        $query->where('duration_minutes', '>', 180);
                        break;
                }
            }

            return $query->orderBy('created_at', 'desc')
                        ->limit(20)
                        ->get();
        }, 600); // 10 minutes cache

        return response()->json($results);
    }
}
