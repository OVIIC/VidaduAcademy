<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'course_id' => Course::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'content' => $this->faker->text,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Placeholder
            'video_duration' => $this->faker->numberBetween(60, 3600),
            'order' => $this->faker->numberBetween(1, 100),
            'is_free' => false,
            'status' => 'published',
            'transcript' => null,
            'resources' => [],
        ];
    }
}
