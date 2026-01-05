<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(3, 6));
        $slug = Str::slug($title);

        return [
            'instructor_id' => User::factory(), // Will create a user if not provided
            'title' => $title,
            'slug' => $slug,
            'short_description' => fake()->paragraph(2),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 19, 199),
            'currency' => 'EUR',
            'thumbnail' => fake()->imageUrl(640, 480, 'education'),
            'status' => fake()->randomElement(['published', 'draft', 'archived']),
            'difficulty_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'duration_minutes' => fake()->numberBetween(60, 600),
            'featured' => fake()->boolean(20),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
