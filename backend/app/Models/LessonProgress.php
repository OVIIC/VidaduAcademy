<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'enrollment_id',
        'completed',
        'watch_time_seconds',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'watch_time_seconds' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if (!$this->lesson->video_duration) {
            return 0;
        }

        return min(100, intval(($this->watch_time_seconds / $this->lesson->video_duration) * 100));
    }
}
