<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'avatar' => null,
            'bio' => $this->faker->optional(0.3)->paragraph(),
            'website' => $this->faker->optional(0.2)->url(),
            'youtube_channel' => $this->faker->optional(0.15)->url(),
            'subscribers_count' => $this->faker->numberBetween(100, 50000),
            'is_instructor' => $this->faker->boolean(20), // 20% chance of being instructor
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_instructor' => true,
            'bio' => $this->faker->paragraph(),
            'youtube_channel' => $this->faker->url(),
            'subscribers_count' => $this->faker->numberBetween(10000, 2000000),
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_instructor' => false,
            'youtube_channel' => null,
            'subscribers_count' => 0,
        ]);
    }
}
