<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('view enrollments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->can('view enrollments')) {
            // Check if the enrollment belongs to a course taught by this instructor
            $course = $enrollment->course;
            return $course && $course->instructor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('create enrollments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->can('edit enrollments')) {
            // Check if the enrollment belongs to a course taught by this instructor
            $course = $enrollment->course;
            return $course && $course->instructor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->can('delete enrollments')) {
            // Check if the enrollment belongs to a course taught by this instructor
            $course = $enrollment->course;
            return $course && $course->instructor_id === $user->id;
        }

        return false;
    }
}
