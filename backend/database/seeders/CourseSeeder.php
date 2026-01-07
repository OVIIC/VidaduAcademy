<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('is_instructor', true)->get();

        if ($instructors->isEmpty()) {
            return;
        }

        $courses = [
            // YouTube Growth Category
            [
                'title' => 'YouTube Channel Growth Masterclass',
                'slug' => 'youtube-channel-growth-masterclass',
                'description' => 'Learn proven strategies to grow your YouTube channel from 0 to 100K subscribers.',
                'short_description' => 'Master YouTube growth strategies used by top creators.',
                'thumbnail' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=450&fit=crop',
                'price' => 199.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 480,
                'what_you_will_learn' => ['YouTube algorithm optimization', 'Content strategy', 'Growth hacking'],
                'requirements' => ['Basic computer skills'],
            ],
            [
                'title' => 'Advanced YouTube Analytics',
                'slug' => 'advanced-youtube-analytics',
                'description' => 'Deep dive into YouTube Studio analytics to double your views.',
                'short_description' => 'Unlock the power of data for your YouTube channel.',
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=450&fit=crop',
                'price' => 149.99,
                'status' => 'published',
                'difficulty_level' => 'advanced',
                'featured' => true,
                'duration_minutes' => 320,
                'what_you_will_learn' => ['Reading CPM/RPM', 'Retention analysis', 'Click-through rate optimization'],
                'requirements' => ['Existing channel'],
            ],
            [
                'title' => 'Viral Thumbnail Design',
                'slug' => 'viral-thumbnail-design',
                'description' => 'Create thumbnails that get clicks. The secret psychology behind high CTR.',
                'short_description' => 'Design thumbnails that impossible to ignore.',
                'thumbnail' => 'https://images.unsplash.com/photo-1626785774573-4b799314346d?w=800&h=450&fit=crop',
                'price' => 89.99,
                'status' => 'published',
                'difficulty_level' => 'intermediate',
                'featured' => true,
                'duration_minutes' => 180,
                'what_you_will_learn' => ['Color theory', 'Typography for CTR', 'Photoshop basics'],
                'requirements' => ['Photoshop or Canva'],
            ],
             [
                'title' => 'YouTube Shorts Domination',
                'slug' => 'youtube-shorts-domination',
                'description' => 'Explode your channel growth using short-form content.',
                'short_description' => 'Master the art of viral YouTube Shorts.',
                'thumbnail' => 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?w=800&h=450&fit=crop',
                'price' => 79.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 120,
                'what_you_will_learn' => ['Shorts algorithm', 'Hooking the audience', 'Viral production'],
                'requirements' => ['Smartphone'],
            ],

            // Editing Category
            [
                'title' => 'Video Editing Pro: From Beginner to Expert',
                'slug' => 'video-editing-pro-beginner-to-expert',
                'description' => 'Complete video editing course covering everything from basic cuts to advanced effects.',
                'short_description' => 'Master video editing with industry-standard techniques.',
                'thumbnail' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&h=450&fit=crop',
                'price' => 299.99,
                'status' => 'published',
                'difficulty_level' => 'intermediate',
                'featured' => true,
                'duration_minutes' => 720,
                'what_you_will_learn' => ['Timeline mastery', 'Color grading', 'Sound design'],
                'requirements' => ['Editing software'],
            ],
            [
                'title' => 'DaVinci Resolve Color Grading',
                'slug' => 'davinci-resolve-color-grading',
                'description' => 'Cinematic color grading secrets for filmmakers.',
                'short_description' => 'Make your videos look like Hollywood movies.',
                'thumbnail' => 'https://images.unsplash.com/photo-1535016120720-40c6874c3b1c?w=800&h=450&fit=crop',
                'price' => 199.99,
                'status' => 'published',
                'difficulty_level' => 'advanced',
                'featured' => true,
                'duration_minutes' => 450,
                'what_you_will_learn' => ['Nodes workflow', 'Color spaces', 'Look development'],
                'requirements' => ['DaVinci Resolve'],
            ],
            [
                'title' => 'Adobe Premiere Pro Fast Track',
                'slug' => 'adobe-premiere-pro-fast-track',
                'description' => 'Get up to speed with Premiere Pro in less than a week.',
                'short_description' => 'Quickest way to learn Adobe Premiere Pro.',
                'thumbnail' => 'https://images.unsplash.com/photo-1626785774625-ddcddc3445e9?w=800&h=450&fit=crop',
                'price' => 129.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 300,
                'what_you_will_learn' => ['Interface basics', 'Cutting', 'Exporting'],
                'requirements' => ['Adobe Premiere Pro'],
            ],
            [
                'title' => 'CapCut Masterclass for Mobile',
                'slug' => 'capcut-masterclass-mobile',
                'description' => 'Edit amazing videos directly on your phone.',
                'short_description' => 'Professional editing on your smartphone.',
                'thumbnail' => 'https://images.unsplash.com/photo-1595111000674-729eb24d86ce?w=800&h=450&fit=crop',
                'price' => 49.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 90,
                'what_you_will_learn' => ['Mobile workflow', 'Effects and transitions', 'Social media formats'],
                'requirements' => ['Smartphone'],
            ],

            // Monetization Category
            [
                'title' => 'YouTube Monetization Blueprint',
                'slug' => 'youtube-monetization-blueprint',
                'description' => 'Turn your YouTube channel into a profitable business with multiple revenue streams.',
                'short_description' => 'Learn to monetize your YouTube channel effectively.',
                'thumbnail' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=450&fit=crop',
                'price' => 149.99,
                'status' => 'published',
                'difficulty_level' => 'intermediate',
                'featured' => true,
                'duration_minutes' => 360,
                'what_you_will_learn' => ['AdSense', 'Sponsorships', 'Merch'],
                'requirements' => ['None'],
            ],
            [
                'title' => 'Sponsorship Mastery',
                'slug' => 'sponsorship-mastery',
                'description' => 'How to land 5-figure brand deals even with a small channel.',
                'short_description' => 'Get paid by brands to create content.',
                'thumbnail' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&h=450&fit=crop',
                'price' => 249.99,
                'status' => 'published',
                'difficulty_level' => 'advanced',
                'featured' => true,
                'duration_minutes' => 240,
                'what_you_will_learn' => ['Media kits', 'Negotiation', 'Contract reading'],
                'requirements' => ['Active channel'],
            ],
            [
                'title' => 'Affiliate Marketing for Creators',
                'slug' => 'affiliate-marketing-creators',
                'description' => 'Generate passive income recommending products you love.',
                'short_description' => 'Passive income through affiliate links.',
                'thumbnail' => 'https://images.unsplash.com/photo-1579621970795-87facc2f976d?w=800&h=450&fit=crop',
                'price' => 89.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 180,
                'what_you_will_learn' => ['Amazon Associates', 'Link tracking', 'Conversion optimization'],
                'requirements' => ['None'],
            ],

            // Strategy Category
            [
                'title' => 'Content Strategy for 2025',
                'slug' => 'content-strategy-2025',
                'description' => 'Future-proof your content strategy with AI and new trends.',
                'short_description' => 'Stay ahead of the competition in 2025.',
                'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=450&fit=crop',
                'price' => 159.99,
                'status' => 'published',
                'difficulty_level' => 'intermediate',
                'featured' => true,
                'duration_minutes' => 300,
                'what_you_will_learn' => ['AI tools', 'Platform trends', 'Audience psychology'],
                'requirements' => ['None'],
            ],
            [
                'title' => 'The Ultimate Success Plan',
                'slug' => 'ultimate-success-plan',
                'description' => 'A step-by-step roadmap to becoming a full-time content creator.',
                'short_description' => 'Your roadmap to full-time creation.',
                'thumbnail' => 'https://images.unsplash.com/photo-1434626881859-194d67b2b86f?w=800&h=450&fit=crop',
                'price' => 199.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 400,
                'what_you_will_learn' => ['Goal setting', 'Time management', 'Burnout prevention'],
                'requirements' => ['Dedication'],
            ],
             [
                'title' => 'Niche Analysis & Selection',
                'slug' => 'niche-analysis-selection',
                'description' => 'Find the perfect profitable niche for your personality.',
                'short_description' => 'Stop guessing, start growing in the right niche.',
                'thumbnail' => 'https://images.unsplash.com/photo-1553484771-371af2727894?w=800&h=450&fit=crop',
                'price' => 69.99,
                'status' => 'published',
                'difficulty_level' => 'beginner',
                'featured' => true,
                'duration_minutes' => 120,
                'what_you_will_learn' => ['Market research', 'Competition analysis', 'Blue ocean strategy'],
                'requirements' => ['None'],
            ],
        ];

        foreach ($courses as $index => $courseData) {
            $course = Course::updateOrCreate(
                ['slug' => $courseData['slug']], // Check specific slug
                [
                    ...$courseData,
                    'instructor_id' => $instructors[$index % $instructors->count()]->id,
                ]
            );
        }
    }
}
