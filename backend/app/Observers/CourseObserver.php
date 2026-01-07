<?php

namespace App\Observers;

use App\Models\Course;
use App\Services\CacheService;

class CourseObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Handle the Course "updated" event.
     */
    public function updated(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Handle the Course "restored" event.
     */
    public function restored(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Handle the Course "force deleted" event.
     */
    public function forceDeleted(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Clear course cache.
     */
    protected function clearCache(Course $course): void
    {
        $this->cacheService->forgetCourse($course->id, $course->slug);
    }
}
