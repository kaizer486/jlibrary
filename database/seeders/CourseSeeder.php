<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get category IDs
        $aiCategory = DB::table('categories')->where('slug', 'ai')->first();
        $webCategory = DB::table('categories')->where('slug', 'web-dev')->first();
        $businessCategory = DB::table('categories')->where('slug', 'business')->first();

        $courses = [
            [
                'category_id' => $aiCategory->id,
                'title' => 'AI Fundamentals',
                'slug' => 'ai-fundamentals',
                'description' => 'Learn the core concepts of Artificial Intelligence, including machine learning, neural networks, and deep learning.',
                'image' => null,
                'level' => 'beginner',
                'duration' => 240,
                'total_lessons' => 12,
                'is_featured' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $aiCategory->id,
                'title' => 'Introduction to AI Systems',
                'slug' => 'intro-to-ai-systems',
                'description' => 'Understand how AI systems work, from data collection to model deployment.',
                'image' => null,
                'level' => 'beginner',
                'duration' => 180,
                'total_lessons' => 8,
                'is_featured' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $webCategory->id,
                'title' => 'Full Stack Web Development',
                'slug' => 'full-stack-web-dev',
                'description' => 'Master HTML, CSS, JavaScript, React, and Node.js to build complete web applications.',
                'image' => null,
                'level' => 'intermediate',
                'duration' => 480,
                'total_lessons' => 24,
                'is_featured' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $businessCategory->id,
                'title' => 'Digital Marketing Basics',
                'slug' => 'digital-marketing-basics',
                'description' => 'Learn SEO, social media marketing, email campaigns, and analytics.',
                'image' => null,
                'level' => 'beginner',
                'duration' => 120,
                'total_lessons' => 6,
                'is_featured' => false,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $businessCategory->id,
                'title' => 'Business Analytics Essentials',
                'slug' => 'business-analytics-essentials',
                'description' => 'Master data analysis, visualization, and business intelligence tools.',
                'image' => null,
                'level' => 'intermediate',
                'duration' => 150,
                'total_lessons' => 8,
                'is_featured' => false,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('courses')->insert($courses);
    }
}