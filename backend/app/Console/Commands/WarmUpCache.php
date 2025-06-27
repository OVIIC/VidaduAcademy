<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use App\Models\Course;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WarmUpCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warmup 
                            {--type=all : Type of cache to warm up (all, courses, users, stats)}
                            {--force : Force cache refresh even if exists}';

    /**
     * The console command description.
     */
    protected $description = 'Warm up application cache for better performance';

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $force = $this->option('force');

        $this->info('🔥 Starting cache warmup...');

        if ($force) {
            $this->warn('🧹 Clearing existing cache...');
            cache()->flush();
        }

        try {
            switch ($type) {
                case 'courses':
                    $this->warmUpCourses();
                    break;
                case 'users':
                    $this->warmUpUsers();
                    break;
                case 'stats':
                    $this->warmUpStats();
                    break;
                case 'all':
                default:
                    $this->warmUpCourses();
                    $this->warmUpUsers();
                    $this->warmUpStats();
                    break;
            }

            $this->info('✅ Cache warmup completed successfully!');
            
            // Show cache stats
            $this->showCacheStats();
            
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Cache warmup failed: ' . $e->getMessage());
            Log::error('Cache warmup failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }

    protected function warmUpCourses(): void
    {
        $this->info('📚 Warming up courses cache...');

        // Featured courses
        $this->cacheService->rememberFeatured(function () {
            return Course::select([
                'id', 'instructor_id', 'title', 'slug', 'short_description', 
                'price', 'currency', 'thumbnail', 'difficulty_level', 
                'duration_minutes', 'created_at'
            ])
            ->with(['instructor:id,name,email,subscribers_count'])
            ->where('status', 'published')
            ->where('featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        });

        // Recent courses
        $this->cacheService->rememberCoursesList('courses:recent', function () {
            return Course::select([
                'id', 'instructor_id', 'title', 'slug', 'short_description', 
                'price', 'currency', 'thumbnail', 'difficulty_level', 
                'duration_minutes', 'created_at'
            ])
            ->with(['instructor:id,name,email,subscribers_count'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();
        });

        // Popular courses (most enrolled)
        $this->cacheService->rememberCoursesList('courses:popular', function () {
            return Course::select([
                'courses.id', 'courses.instructor_id', 'courses.title', 'courses.slug', 
                'courses.short_description', 'courses.price', 'courses.currency', 
                'courses.thumbnail', 'courses.difficulty_level', 'courses.duration_minutes'
            ])
            ->leftJoin('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->with(['instructor:id,name,email,subscribers_count'])
            ->where('courses.status', 'published')
            ->groupBy('courses.id')
            ->orderByRaw('COUNT(enrollments.id) DESC')
            ->limit(12)
            ->get();
        });

        // Top course details (most popular ones)
        $popularCourseIds = Course::leftJoin('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->where('courses.status', 'published')
            ->groupBy('courses.id')
            ->orderByRaw('COUNT(enrollments.id) DESC')
            ->limit(5)
            ->pluck('courses.id');

        foreach ($popularCourseIds as $courseId) {
            $this->cacheService->rememberCourse($courseId, function () use ($courseId) {
                return Course::select([
                    'id', 'instructor_id', 'title', 'slug', 'description', 'short_description',
                    'price', 'currency', 'thumbnail', 'difficulty_level', 'duration_minutes',
                    'what_you_will_learn', 'requirements', 'status', 'created_at', 'updated_at'
                ])
                ->with([
                    'instructor:id,name,email,avatar,bio,subscribers_count',
                    'lessons' => function ($query) {
                        $query->select(['id', 'course_id', 'title', 'slug', 'duration_minutes', 'order', 'is_preview'])
                              ->where('status', 'published')
                              ->orderBy('order');
                    }
                ])
                ->where('status', 'published')
                ->find($courseId);
            });
        }

        $this->line('  ✓ Featured courses cached');
        $this->line('  ✓ Recent courses cached');
        $this->line('  ✓ Popular courses cached');
        $this->line('  ✓ Top course details cached');
    }

    protected function warmUpUsers(): void
    {
        $this->info('👥 Warming up users cache...');

        // Top instructors
        $topInstructors = User::whereHas('taughtCourses', function ($query) {
            $query->where('status', 'published');
        })
        ->withCount(['taughtCourses as published_courses_count' => function ($query) {
            $query->where('status', 'published');
        }])
        ->orderBy('published_courses_count', 'desc')
        ->limit(10)
        ->get();

        foreach ($topInstructors as $instructor) {
            $this->cacheService->rememberUser($instructor->id, function () use ($instructor) {
                return $instructor;
            });
        }

        $this->line('  ✓ Top instructors cached');
    }

    protected function warmUpStats(): void
    {
        $this->info('📊 Warming up statistics cache...');

        // Global stats
        $this->cacheService->remember('global_stats', function () {
            return [
                'total_courses' => Course::where('status', 'published')->count(),
                'total_instructors' => User::whereHas('taughtCourses', function ($query) {
                    $query->where('status', 'published');
                })->count(),
                'total_enrollments' => \DB::table('enrollments')->count(),
                'total_students' => \DB::table('enrollments')->distinct('user_id')->count(),
            ];
        }, 3600);

        // Course categories stats if you have categories
        $this->cacheService->remember('course_difficulty_stats', function () {
            return Course::where('status', 'published')
                ->selectRaw('difficulty_level, COUNT(*) as count')
                ->groupBy('difficulty_level')
                ->pluck('count', 'difficulty_level');
        }, 3600);

        $this->line('  ✓ Global statistics cached');
        $this->line('  ✓ Course difficulty stats cached');
    }

    protected function showCacheStats(): void
    {
        $stats = $this->cacheService->getStats();
        
        $this->info('📈 Cache Statistics:');
        $this->line('  Driver: ' . $stats['driver']);
        
        if (isset($stats['memory_usage'])) {
            $this->line('  Memory Usage: ' . $stats['memory_usage']);
            $this->line('  Connected Clients: ' . $stats['connected_clients']);
            $this->line('  Hit Rate: ' . $stats['hit_rate']);
        }
    }
}
