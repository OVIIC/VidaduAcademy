<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentService
{
    /**
     * Enroll a user in a course safely using transactions and locking.
     *
     * @param User $user
     * @param Course $course
     * @param Purchase|null $purchase Optional purchase record linking the enrollment
     * @return Enrollment
     * @throws \Exception
     */
    public function enrollUser(User $user, Course $course, ?Purchase $purchase = null): Enrollment
    {
        return DB::transaction(function () use ($user, $course, $purchase) {
            // Lock the user row to prevent concurrent enrollments for the same user
            // This acts as a coarse-grained lock for user-related actions
            DB::table('users')->where('id', $user->id)->lockForUpdate()->get();

            // Check if already enrolled to avoid duplicates
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                Log::info("User {$user->id} already enrolled in Course {$course->id}, skipping creation.");
                return $existingEnrollment;
            }

            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]);

            // If a purchase allows it, we could link it here if the schema supported it,
            // or just ensure the purchase status is final if passed.
            if ($purchase && $purchase->status !== 'completed') {
                $purchase->update([
                    'status' => 'completed',
                    'purchased_at' => now(),
                ]);
            }

            Log::info("User {$user->id} successfully enrolled in Course {$course->id}. Enrollment ID: {$enrollment->id}");

            return $enrollment;
        });
    }

    /**
     * Unenroll a user from a course safely.
     *
     * @param User $user
     * @param Course $course
     * @return bool
     */
    public function unenrollUser(User $user, Course $course): bool
    {
        return DB::transaction(function () use ($user, $course) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if (!$enrollment) {
                return false;
            }

            // Refund logic is handled by Enrollment model observer (boot method),
            // which updates relevant Purchases to 'refunded'.
            
            $enrollment->delete();
            
            Log::info("User {$user->id} unenrolled from Course {$course->id}");

            return true;
        });
    }
}
