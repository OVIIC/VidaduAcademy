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
                    'content' => $lessonData['content'] ?? '',
                    'video_url' => $lessonData['video_url'],
                    'video_duration' => $lessonData['duration_minutes'] * 60, // Convert minutes to seconds
                    'is_free' => $lessonData['is_free'],
                    'status' => 'published',
                    'course_id' => $course->id,
                    'order' => $index + 1,
                    'transcript' => $lessonData['transcript'] ?? '',
                    'resources' => $lessonData['resources'] ?? [],
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
                        'content' => '<h3>Vitajte v kurze YouTube Growth Masterclass!</h3><p>V tejto úvodnej lekcii sa naučíte základy rastu na YouTube platforme. Prejdeme si:</p><ul><li>Ako funguje YouTube algoritmus</li><li>Kľúčové metriky pre úspech</li><li>Základné princípy tvorby obsahu</li></ul><p>Táto lekcia je bezplatná, aby ste mohli vyskúšať kvalitu nášho obsahu.</p>',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 15,
                        'is_free' => true,
                        'transcript' => 'Vitajte v kurze YouTube Growth Masterclass. Volám sa Adam a budem vašim inštruktorom...',
                        'resources' => [
                            [
                                'title' => 'YouTube Creator Handbook',
                                'url' => 'https://creatoracademy.youtube.com',
                                'type' => 'pdf',
                                'description' => 'Oficiálna príručka pre tvorcu obsahu'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Understanding the YouTube Algorithm',
                        'description' => 'Deep dive into how YouTube\'s algorithm works and how to optimize for it.',
                        'content' => '<h3>Pochopenie YouTube algoritmu</h3><p>YouTube algoritmus je komplexný systém, ktorý rozhoduje o tom, ktoré videá sa zobrazujú používateľom. V tejto lekcii sa naučíte:</p><h4>Kľúčové faktory algoritmu:</h4><ul><li><strong>Čas sledovania</strong> - najdôležitejšia metrika</li><li><strong>CTR</strong> - click-through rate</li><li><strong>Engagement</strong> - lajky, komentáre, zdieľania</li><li><strong>Konzistentnosť</strong> - pravidelné nahrávanie</li></ul><p>Praktické tipy na optimalizáciu vašich videí pre algoritmus.</p>',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 25,
                        'is_free' => false,
                        'transcript' => 'YouTube algoritmus je srdce celej platformy. Rozhoduje o tom, ktoré videá uvidia miliény používateľov...',
                        'resources' => [
                            [
                                'title' => 'Algorithm Optimization Checklist',
                                'url' => 'https://example.com/checklist.pdf',
                                'type' => 'pdf',
                                'description' => 'Kontrolný zoznam pre optimalizáciu videí'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Content Strategy and Planning',
                        'description' => 'Learn how to plan and create engaging content consistently.',
                        'content' => '<h3>Stratégia a plánovanie obsahu</h3><p>Úspešný YouTube kanál potrebuje premyslenú stratégiu obsahu. V tejto lekcii sa naučíte:</p><h4>Plánovanie obsahu</h4><ul><li>Identifikácia cieľovej skupiny</li><li>Výskum kľúčových slov</li><li>Tvorba obsahového kalendára</li><li>Analýza konkurencie</li></ul><h4>Typy obsahu</h4><ul><li>Vzdelávacie videá</li><li>Zábavný obsah</li><li>Tutoriály a návody</li><li>Vlogy a lifestyle obsah</li></ul><p><strong>Praktické cvičenie:</strong> Vytvorenie vášho prvého obsahového plánu na 30 dní.</p>',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 30,
                        'is_free' => false,
                        'resources' => [
                            [
                                'title' => 'Content Calendar Template',
                                'url' => 'https://example.com/template.xlsx',
                                'type' => 'document',
                                'description' => 'Šablóna pre obsahový kalendár'
                            ]
                        ]
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
                        'content' => '<h3>Začíname s video editovaním</h3><p>Video editing je umenie spájania a úpravy video materiálu. V tejto úvodnej lekcii sa naučíte:</p><h4>Výber softvéru</h4><ul><li><strong>Adobe Premiere Pro</strong> - profesionálny nástroj</li><li><strong>Final Cut Pro</strong> - pre Mac používateľov</li><li><strong>DaVinci Resolve</strong> - bezplatná alternatíva</li><li><strong>CapCut</strong> - pre začiatočníkov</li></ul><h4>Základné pojmy</h4><ul><li>Timeline - časová os</li><li>Clips - video klipy</li><li>Transitions - prechody</li><li>Rendering - export videa</li></ul><p>Táto lekcia je bezplatná, aby ste mohli zistiť, či je tento kurz pre vás vhodný.</p>',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => 20,
                        'is_free' => true,
                        'resources' => [
                            [
                                'title' => 'Software Comparison Guide',
                                'url' => 'https://example.com/software-guide.pdf',
                                'type' => 'pdf',
                                'description' => 'Porovnanie video editing softvéru'
                            ]
                        ]
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
