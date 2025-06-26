<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@vidaduacademy.com',
            'password' => Hash::make('password'),
            'is_instructor' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample instructors
        $instructors = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@vidaduacademy.com',
                'bio' => 'YouTube growth strategist with over 1M subscribers. Helping creators build their channels for 5+ years.',
                'youtube_channel' => 'https://youtube.com/@sarahjohnson',
                'subscribers_count' => 1250000,
                'is_instructor' => true,
            ],
            [
                'name' => 'Mike Rodriguez',
                'email' => 'mike@vidaduacademy.com',
                'bio' => 'Video editing expert and content creator. Teaching creators how to make professional videos.',
                'youtube_channel' => 'https://youtube.com/@mikerodriguez',
                'subscribers_count' => 850000,
                'is_instructor' => true,
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily@vidaduacademy.com',
                'bio' => 'YouTube monetization specialist. Helped 1000+ creators turn their channels into profitable businesses.',
                'youtube_channel' => 'https://youtube.com/@emilychen',
                'subscribers_count' => 650000,
                'is_instructor' => true,
            ],
        ];

        foreach ($instructors as $instructor) {
            User::create([
                ...$instructor,
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create sample students
        User::factory(50)->create();
    }
}
