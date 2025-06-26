<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'YouTube Growth',
                'slug' => 'youtube-growth',
                'description' => 'Learn strategies to grow your YouTube channel and increase subscribers.',
                'color' => '#FF0000',
                'icon' => 'youtube',
            ],
            [
                'name' => 'Content Creation',
                'slug' => 'content-creation',
                'description' => 'Master the art of creating engaging video content.',
                'color' => '#8B5CF6',
                'icon' => 'video',
            ],
            [
                'name' => 'Channel Management',
                'slug' => 'channel-management',
                'description' => 'Optimize and manage your YouTube channel effectively.',
                'color' => '#10B981',
                'icon' => 'settings',
            ],
            [
                'name' => 'Monetization',
                'slug' => 'monetization',
                'description' => 'Learn various ways to monetize your YouTube channel.',
                'color' => '#F59E0B',
                'icon' => 'currency-dollar',
            ],
            [
                'name' => 'Analytics & SEO',
                'slug' => 'analytics-seo',
                'description' => 'Understand YouTube analytics and optimize for search.',
                'color' => '#3B82F6',
                'icon' => 'chart-bar',
            ],
            [
                'name' => 'Equipment & Software',
                'slug' => 'equipment-software',
                'description' => 'Learn about video equipment and editing software.',
                'color' => '#6B7280',
                'icon' => 'camera',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
