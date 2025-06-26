<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            // Create lessons based on course content
            $lessons = $this->getLessonsForCourse($course->slug);
            
            foreach ($lessons as $index => $lessonData) {
                Lesson::create([
                    'title' => $lessonData['title'],
                    'slug' => Str::slug($lessonData['title']),
                    'description' => $lessonData['description'],
                    'video_url' => $lessonData['video_url'],
                    'video_duration' => $lessonData['duration_minutes'] * 60, // Convert minutes to seconds
                    'is_free' => $lessonData['is_free'],
                    'status' => 'published',
                    'course_id' => $course->id,
                    'order' => $index + 1,
                ]);
            }
        }
    }

    private function getLessonsForCourse(string $courseSlug): array
    {
        switch ($courseSlug) {
            case 'youtube-channel-growth-masterclass':
                return [
                    [
                        'title' => 'Introduction to YouTube Growth',
                        'description' => 'Overview of the YouTube platform and growth fundamentals.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 15,
                        'is_free' => true,
                    ],
                    [
                        'title' => 'Understanding the YouTube Algorithm',
                        'description' => 'Deep dive into how YouTube\'s algorithm works and how to optimize for it.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 25,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Content Strategy and Planning',
                        'description' => 'Learn how to plan and create engaging content consistently.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 30,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Thumbnail Design Mastery',
                        'description' => 'Create compelling thumbnails that increase click-through rates.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 20,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Growing Your Subscriber Base',
                        'description' => 'Proven strategies to convert viewers into loyal subscribers.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 35,
                        'is_free' => false,
                    ],
                ];

            case 'video-editing-pro-beginner-to-expert':
                return [
                    [
                        'title' => 'Getting Started with Video Editing',
                        'description' => 'Introduction to video editing software and basic concepts.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 20,
                        'is_free' => true,
                    ],
                    [
                        'title' => 'Basic Cuts and Transitions',
                        'description' => 'Learn fundamental editing techniques and smooth transitions.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 30,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Color Grading Fundamentals',
                        'description' => 'Master color correction and grading for professional looks.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 40,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Audio Editing and Mixing',
                        'description' => 'Perfect your audio for professional-quality videos.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 35,
                        'is_free' => false,
                    ],
                ];

            case 'youtube-monetization-blueprint':
                return [
                    [
                        'title' => 'Monetization Overview',
                        'description' => 'Understanding different ways to monetize your YouTube channel.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 15,
                        'is_free' => true,
                    ],
                    [
                        'title' => 'AdSense Optimization',
                        'description' => 'Maximize your ad revenue with strategic placement and optimization.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 25,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Affiliate Marketing Strategies',
                        'description' => 'Build passive income through strategic affiliate partnerships.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 30,
                        'is_free' => false,
                    ],
                ];

            case 'content-creation-for-beginners':
                return [
                    [
                        'title' => 'Content Creation Basics',
                        'description' => 'Introduction to content creation and platform overview.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 18,
                        'is_free' => true,
                    ],
                    [
                        'title' => 'Planning Your Content Strategy',
                        'description' => 'Learn how to plan engaging content that resonates with your audience.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 22,
                        'is_free' => false,
                    ],
                    [
                        'title' => 'Basic Filming Techniques',
                        'description' => 'Essential filming skills for creating professional-looking content.',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 28,
                        'is_free' => false,
                    ],
                ];

            default:
                return [];
        }
    }
}
