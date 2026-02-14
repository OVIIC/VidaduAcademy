<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'category_id',
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
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
        return number_format((float) $this->price, 2);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->isPast();
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
        if (is_array($value)) {
            $decoded = $value;
        } else {
            $decoded = json_decode($value, true) ?: [];
        }
        
        // Convert to Filament repeater format ONLY if accessing from admin panel
        if (request() && request()->is('admin/*') && !empty($decoded) && is_array($decoded) && !isset($decoded[0]['item'])) {
            return array_map(fn($item) => ['item' => $item], $decoded);
        }
        
        return is_array($decoded) ? $decoded : [];
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
        if (is_array($value)) {
            $decoded = $value;
        } else {
            $decoded = json_decode($value, true) ?: [];
        }
        
        // Convert to Filament repeater format ONLY if accessing from admin panel
        if (request() && request()->is('admin/*') && !empty($decoded) && is_array($decoded) && !isset($decoded[0]['item'])) {
            return array_map(fn($item) => ['item' => $item], $decoded);
        }
        
        return is_array($decoded) ? $decoded : [];
    }

    public function getThumbnailAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (!is_string($value)) {
            return null;
        }

        // Check if it's already a complete URL
        if (filter_var($value, FILTER_VALIDATE_URL) || str_starts_with($value, 'http')) {
            return $value;
        }

        // Fix for broken Unsplash URLs (missing domain)
        if (str_starts_with($value, 'photo-')) {
            return 'https://images.unsplash.com/' . $value;
        }

        // Otherwise assume it's a local storage path
        return asset('storage/' . $value);
    }
}
