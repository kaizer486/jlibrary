<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $aiFundamentals = DB::table('courses')->where('slug', 'ai-fundamentals')->first();
        $introToAI = DB::table('courses')->where('slug', 'intro-to-ai-systems')->first();

        $lessons = [];

        if ($aiFundamentals) {
            $aiLessons = [
                ['title' => 'What is Artificial Intelligence?', 'duration' => 15, 'order' => 1],
                ['title' => 'History of AI', 'duration' => 20, 'order' => 2],
                ['title' => 'Machine Learning Basics', 'duration' => 25, 'order' => 3],
                ['title' => 'Neural Networks', 'duration' => 30, 'order' => 4],
                ['title' => 'Deep Learning', 'duration' => 25, 'order' => 5],
                ['title' => 'Natural Language Processing', 'duration' => 20, 'order' => 6],
                ['title' => 'Computer Vision', 'duration' => 20, 'order' => 7],
                ['title' => 'AI Ethics and Bias', 'duration' => 15, 'order' => 8],
                ['title' => 'AI in Practice', 'duration' => 25, 'order' => 9],
                ['title' => 'Future of AI', 'duration' => 20, 'order' => 10],
                ['title' => 'AI Tools and Frameworks', 'duration' => 25, 'order' => 11],
                ['title' => 'Final Assessment', 'duration' => 30, 'order' => 12],
            ];

            foreach ($aiLessons as $lesson) {
                $lessons[] = [
                    'course_id' => $aiFundamentals->id,
                    'title' => $lesson['title'],
                    'content' => "This lesson covers: {$lesson['title']}. Detailed content will be added soon.",
                    'duration' => $lesson['duration'],
                    'order' => $lesson['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($introToAI) {
            $introLessons = [
                ['title' => 'Introduction to AI Systems', 'duration' => 15, 'order' => 1],
                ['title' => 'Data Collection and Preparation', 'duration' => 20, 'order' => 2],
                ['title' => 'Model Training', 'duration' => 25, 'order' => 3],
                ['title' => 'Model Evaluation', 'duration' => 20, 'order' => 4],
                ['title' => 'Deployment Strategies', 'duration' => 25, 'order' => 5],
                ['title' => 'Monitoring and Maintenance', 'duration' => 20, 'order' => 6],
                ['title' => 'Case Studies', 'duration' => 30, 'order' => 7],
                ['title' => 'Course Completion Quiz', 'duration' => 25, 'order' => 8],
            ];

            foreach ($introLessons as $lesson) {
                $lessons[] = [
                    'course_id' => $introToAI->id,
                    'title' => $lesson['title'],
                    'content' => "This lesson covers: {$lesson['title']}. Detailed content will be added soon.",
                    'duration' => $lesson['duration'],
                    'order' => $lesson['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('lessons')->insert($lessons);
    }
}