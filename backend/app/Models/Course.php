<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'price',
        'currency',
        'thumbnail',
        'status',
        'instructor_id',
        'duration_minutes',
        'difficulty_level',
        'what_you_will_learn',
        'requirements',
        'featured',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'what_you_will_learn' => 'array',
        'requirements' => 'array',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && $this->published_at?->isPast();
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->lessons()->count();
    }

    // Mutators for Filament repeater fields
    public function setWhatYouWillLearnAttribute($value)
    {
        if (is_array($value) && isset($value[0]['item'])) {
            // Convert from Filament repeater format to simple array
            $this->attributes['what_you_will_learn'] = json_encode(array_column($value, 'item'));
        } else {
            $this->attributes['what_you_will_learn'] = json_encode($value ?: []);
        }
    }

    public function getWhatYouWillLearnAttribute($value)
    {
        $decoded = json_decode($value, true) ?: [];
        // Convert to Filament repeater format if accessing from form
        if (request()->is('admin/*') && !empty($decoded) && !isset($decoded[0]['item'])) {
            return array_map(fn($item) => ['item' => $item], $decoded);
        }
        return $decoded;
    }

    public function setRequirementsAttribute($value)
    {
        if (is_array($value) && isset($value[0]['item'])) {
            // Convert from Filament repeater format to simple array
            $this->attributes['requirements'] = json_encode(array_column($value, 'item'));
        } else {
            $this->attributes['requirements'] = json_encode($value ?: []);
        }
    }

    public function getRequirementsAttribute($value)
    {
        $decoded = json_decode($value, true) ?: [];
        // Convert to Filament repeater format if accessing from form
        if (request()->is('admin/*') && !empty($decoded) && !isset($decoded[0]['item'])) {
            return array_map(fn($item) => ['item' => $item], $decoded);
        }
        return $decoded;
    }
}
