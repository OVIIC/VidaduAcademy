<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_lesson()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['status' => 'published']);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'status' => 'published',
            'slug' => 'introduction'
        ]);

        // Manually enroll the user
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/learning/course/{$course->slug}/lesson/{$lesson->slug}/progress", [
                'completed' => true,
                'watch_time_seconds' => 120
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'progress' => [
                    'completed' => true,
                    'watch_time_seconds' => 120
                ]
            ]);
        
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed' => 1,
            'watch_time_seconds' => 120
        ]);
        
        // Assert course progress updated to 100% (since 1/1 lesson completed)
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percentage' => 100
        ]);
    }

    public function test_cannot_update_progress_without_enrollment()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['status' => 'published']);
        $lesson = Lesson::factory()->create(['course_id' => $course->id, 'status' => 'published']);

        $response = $this->actingAs($user)
            ->postJson("/api/learning/course/{$course->slug}/lesson/{$lesson->slug}/progress", [
                'completed' => true
            ]);

        $response->assertStatus(403);
    }
}
