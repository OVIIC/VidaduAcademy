<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $instructors = User::where('is_instructor', true)->get();

        $courses = [
            [
                'title' => 'YouTube Channel Growth Masterclass',
                'slug' => 'youtube-channel-growth-masterclass',
                'description' => 'Learn proven strategies to grow your YouTube channel from 0 to 100K subscribers.',
                'short_description' => 'Master YouTube growth strategies used by top creators.',
                'thumbnail' => 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=800&h=450&fit=crop',
                'price' => 199.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'duration_minutes' => 480, // 8 hours
                'what_you_will_learn' => [
                    'YouTube algorithm optimization',
                    'Content strategy development',
                    'Thumbnail design principles',
                    'Audience engagement techniques',
                    'Analytics and growth tracking'
                ],
                'requirements' => [
                    'Basic understanding of YouTube',
                    'A YouTube channel (or willingness to create one)',
                    'Computer with internet access'
                ],
            ],
            [
                'title' => 'Video Editing Pro: From Beginner to Expert',
                'slug' => 'video-editing-pro-beginner-to-expert',
                'description' => 'Complete video editing course covering everything from basic cuts to advanced effects.',
                'short_description' => 'Master video editing with industry-standard techniques.',
                'thumbnail' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&h=450&fit=crop',
                'price' => 299.99,
                'status' => 'published',
                'difficulty_level' => 'intermediate',
                'duration_minutes' => 720, // 12 hours
                'what_you_will_learn' => [
                    'Advanced editing techniques',
                    'Color grading and correction',
                    'Audio mixing and mastering',
                    'Motion graphics basics',
                    'Export optimization'
                ],
                'requirements' => [
                    'Video editing software (recommendations provided)',
                    'Computer capable of video editing',
                    'Basic computer skills'
                ],
            ],
            [
                'title' => 'YouTube Monetization Blueprint',
                'slug' => 'youtube-monetization-blueprint',
                'description' => 'Turn your YouTube channel into a profitable business with multiple revenue streams.',
                'short_description' => 'Learn to monetize your YouTube channel effectively.',
                'thumbnail' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=450&fit=crop',
                'price' => 149.99,
                'status' => 'published',
                'difficulty_level' => 'intermediate',
                'duration_minutes' => 360, // 6 hours
                'what_you_will_learn' => [
                    'AdSense optimization',
                    'Sponsored content strategies',
                    'Affiliate marketing',
                    'Product creation',
                    'Brand partnerships'
                ],
                'requirements' => [
                    'Existing YouTube channel',
                    'Basic understanding of YouTube',
                    'Willingness to implement strategies'
                ],
            ],
            [
                'title' => 'Content Creation for Beginners',
                'slug' => 'content-creation-for-beginners',
                'description' => 'Start your content creation journey with this comprehensive beginner-friendly course.',
                'short_description' => 'Perfect starting point for aspiring content creators.',
                'thumbnail' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=450&fit=crop',
                'price' => 99.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'duration_minutes' => 300, // 5 hours
                'what_you_will_learn' => [
                    'Content planning and strategy',
                    'Basic filming techniques',
                    'Equipment recommendations',
                    'Publishing and distribution',
                    'Building an audience'
                ],
                'requirements' => [
                    'Smartphone or camera',
                    'Desire to create content',
                    'Basic internet skills'
                ],
            ]
        ];

        foreach ($courses as $index => $courseData) {
            $course = Course::create([
                ...$courseData,
                'instructor_id' => $instructors[$index % $instructors->count()]->id,
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
