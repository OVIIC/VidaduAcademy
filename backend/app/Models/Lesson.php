<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'content',
        'video_url',
        'video_duration',
        'order',
        'is_free',
        'status',
        'transcript',
        'resources',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'video_duration' => 'integer',
        'resources' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = intval($this->video_duration / 60);
        $seconds = $this->video_duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    // Mutators for Filament repeater fields
    public function setResourcesAttribute($value)
    {
        if (is_array($value) && isset($value[0]['title'])) {
            // Resources are already in correct format
            $this->attributes['resources'] = json_encode($value);
        } else {
            $this->attributes['resources'] = json_encode($value ?: []);
        }
    }

    public function getResourcesAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }
}
