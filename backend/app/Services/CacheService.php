<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class CacheService
{
    protected const DEFAULT_TTL = 3600; // 1 hour
    protected const COURSE_TTL = 1800; // 30 minutes
    protected const USER_TTL = 900; // 15 minutes
    protected const FEATURED_TTL = 7200; // 2 hours

    // Cache key prefixes
    protected const COURSE_PREFIX = 'course:';
    protected const USER_PREFIX = 'user:';
    protected const FEATURED_PREFIX = 'featured:';
    protected const ENROLLMENT_PREFIX = 'enrollment:';
    protected const PURCHASE_PREFIX = 'purchase:';

    /**
     * Get cached data or execute callback and cache result
     */
    public function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Cache remember failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to direct execution
            return $callback();
        }
    }

    /**
     * Cache course data with optimized TTL
     */
    public function rememberCourse(int $courseId, callable $callback): mixed
    {
        $key = self::COURSE_PREFIX . $courseId;
        return $this->remember($key, $callback, self::COURSE_TTL);
    }

    /**
     * Cache courses list with tags for easier invalidation
     */
    public function rememberCoursesList(string $key, callable $callback): mixed
    {
        // Check if current cache driver supports tagging
        if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
            return Cache::tags(['courses', 'courses_list'])
                ->remember($key, self::COURSE_TTL, $callback);
        }
        
        // Fallback for file/database cache
        return $this->remember($key, $callback, self::COURSE_TTL);
    }

    /**
     * Cache featured courses
     */
    public function rememberFeatured(callable $callback): mixed
    {
        $key = self::FEATURED_PREFIX . 'courses';
        
        if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
            return Cache::tags(['courses', 'featured'])
                ->remember($key, self::FEATURED_TTL, $callback);
        }
        
        return $this->remember($key, $callback, self::FEATURED_TTL);
    }

    /**
     * Cache user data
     */
    public function rememberUser(int $userId, callable $callback): mixed
    {
        $key = self::USER_PREFIX . $userId;
        return $this->remember($key, $callback, self::USER_TTL);
    }

    /**
     * Cache enrollment data
     */
    public function rememberEnrollment(int $userId, int $courseId, callable $callback): mixed
    {
        $key = self::ENROLLMENT_PREFIX . $userId . ':' . $courseId;
        return $this->remember($key, $callback, self::USER_TTL);
    }

    /**
     * Cache purchase status
     */
    public function rememberPurchase(int $userId, int $courseId, callable $callback): mixed
    {
        $key = self::PURCHASE_PREFIX . $userId . ':' . $courseId;
        return $this->remember($key, $callback, self::USER_TTL);
    }

    /**
     * Invalidate course cache
     */
    public function forgetCourse(int $courseId, ?string $slug = null): void
    {
        try {
            Cache::forget(self::COURSE_PREFIX . $courseId);
            
            if ($slug) {
                Cache::forget('course:slug:' . $slug);
            }
            
            if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
                Cache::tags(['courses'])->flush();
            } else {
                // For file/database cache, manually clear related keys
                $keys = [
                    'courses:recent',
                    'courses:popular',
                    self::FEATURED_PREFIX . 'courses'
                ];
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Cache forget course failed', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Invalidate user-related caches
     */
    public function forgetUser(int $userId): void
    {
        try {
            Cache::forget(self::USER_PREFIX . $userId);
            
            // Clear user-specific patterns
            if (config('cache.default') === 'redis') {
                $patterns = [
                    self::ENROLLMENT_PREFIX . $userId . ':*',
                    self::PURCHASE_PREFIX . $userId . ':*'
                ];
                
                foreach ($patterns as $pattern) {
                    $this->deleteByPattern($pattern);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Cache forget user failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Invalidate enrollment cache
     */
    public function forgetEnrollment(int $userId, int $courseId): void
    {
        try {
            Cache::forget(self::ENROLLMENT_PREFIX . $userId . ':' . $courseId);
            Cache::forget(self::PURCHASE_PREFIX . $userId . ':' . $courseId);
        } catch (\Exception $e) {
            Log::warning('Cache forget enrollment failed', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public function warmUp(): void
    {
        try {
            // Warm up featured courses
            $this->rememberFeatured(function () {
                return \App\Models\Course::where('featured', true)
                    ->select(['id', 'title', 'slug', 'description', 'image', 'price', 'featured'])
                    ->get();
            });

            // Warm up recent courses
            $this->rememberCoursesList('courses:recent', function () {
                return \App\Models\Course::select(['id', 'title', 'slug', 'description', 'image', 'price'])
                    ->latest()
                    ->limit(10)
                    ->get();
            });

            Log::info('Cache warmed up successfully');
        } catch (\Exception $e) {
            Log::error('Cache warm up failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                $info = $redis->info();
                
                return [
                    'driver' => 'redis',
                    'memory_usage' => $info['used_memory_human'] ?? 'N/A',
                    'connected_clients' => $info['connected_clients'] ?? 'N/A',
                    'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                    'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                    'hit_rate' => $this->calculateHitRate(
                        $info['keyspace_hits'] ?? 0,
                        $info['keyspace_misses'] ?? 0
                    )
                ];
            }
            
            return [
                'driver' => config('cache.default'),
                'status' => 'active'
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete cache keys by pattern (Redis only)
     */
    protected function deleteByPattern(string $pattern): int
    {
        if (config('cache.default') !== 'redis') {
            return 0;
        }

        try {
            $redis = Redis::connection();
            $keys = $redis->keys($pattern);
            
            if (empty($keys)) {
                return 0;
            }
            
            return $redis->del($keys);
        } catch (\Exception $e) {
            Log::warning('Delete by pattern failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Calculate cache hit rate
     */
    protected function calculateHitRate(int $hits, int $misses): string
    {
        $total = $hits + $misses;
        if ($total === 0) {
            return '0%';
        }
        
        $rate = ($hits / $total) * 100;
        return number_format($rate, 2) . '%';
    }
}
