<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'bio',
        'avatar',
        'website',
        'youtube_channel',
        'subscriber_count',
        'content_niche',
        'goals',
        'email_notifications',
        'course_reminders',
        'marketing_emails',
        'email_verified_at',
        'is_instructor',
        'is_active',
        'subscribers_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_instructor' => 'boolean',
        'is_active' => 'boolean',
        'subscribers_count' => 'integer',
        'email_notifications' => 'boolean',
        'course_reminders' => 'boolean',
        'marketing_emails' => 'boolean',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function instructedCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrollments()->where('course_id', $course->id)->exists();
    }

    public function hasPurchased(Course $course): bool
    {
        return $this->purchases()
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->exists();
    }

    public function getProgressForCourse(Course $course): ?Enrollment
    {
        return $this->enrollments()->where('course_id', $course->id)->first();
    }

    public function getFormattedSubscribersAttribute(): string
    {
        if ($this->subscribers_count >= 1000000) {
            return number_format($this->subscribers_count / 1000000, 1) . 'M';
        } elseif ($this->subscribers_count >= 1000) {
            return number_format($this->subscribers_count / 1000, 1) . 'K';
        }

        return (string) $this->subscribers_count;
    }
}
