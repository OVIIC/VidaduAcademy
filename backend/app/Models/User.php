<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin') || $this->can('access admin panel');
        }

        return false;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
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

    /**
     * Get courses taught by this user (if instructor)
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get published courses taught by this user
     */
    public function publishedCourses(): HasMany
    {
        return $this->taughtCourses()->where('status', 'published');
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
        // Check if user has completed purchase
        // We do NOT check for enrollment here, as the controller uses this check
        // to automatically recreate enrollment if it's missing for a paid user
        return $this->purchases()
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Cancel purchase and remove enrollment for a course
     */
    public function cancelPurchaseAndUnenroll(Course $course): bool
    {
        // Find and delete enrollment (this will trigger purchase cancellation via model event)
        $enrollment = $this->enrollments()->where('course_id', $course->id)->first();
        
        if ($enrollment) {
            $enrollment->delete(); // This will trigger the boot method
        }
        
        return true;
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
