<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_instructor', false)->get();
        $courses = Course::all();

        // Create some sample enrollments
        foreach ($users as $user) {
            // Enroll each user in 1-3 random courses
            $randomCourses = $courses->random(rand(1, 3));
            
            foreach ($randomCourses as $course) {
                // Check if enrollment already exists
                if (!Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                    Enrollment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'enrolled_at' => now()->subDays(rand(1, 30)),
                        'progress_percentage' => rand(0, 100),
                        'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                    ]);
                }
            }
        }
    }
}
