<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProgressSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user (Josiah)
        $user = DB::table('users')->where('email', 'admin@jlibrary.com')->first();
        
        if (!$user) {
            $user = DB::table('users')->first();
        }

        if (!$user) {
            return;
        }

        // Get courses
        $aiFundamentals = DB::table('courses')->where('slug', 'ai-fundamentals')->first();
        $introToAI = DB::table('courses')->where('slug', 'intro-to-ai-systems')->first();

        // Enroll in AI Fundamentals with 85% progress (update if exists)
        if ($aiFundamentals) {
            DB::table('user_courses')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'course_id' => $aiFundamentals->id,
                ], // Check if exists
                [
                    'progress_percent' => 85,
                    'status' => 'in_progress',
                    'started_at' => now()->subDays(14),
                    'updated_at' => now(),
                ] // Update these values
            );

            // Mark first 10 lessons as completed (update if exists)
            $lessons = DB::table('lessons')->where('course_id', $aiFundamentals->id)->take(10)->get();
            foreach ($lessons as $lesson) {
                DB::table('user_lesson_progress')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                    ],
                    [
                        'is_completed' => true,
                        'completed_at' => now()->subDays(rand(1, 13)),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // Enroll in Intro to AI with 68% progress (update if exists)
        if ($introToAI) {
            DB::table('user_courses')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'course_id' => $introToAI->id,
                ],
                [
                    'progress_percent' => 68,
                    'status' => 'in_progress',
                    'started_at' => now()->subDays(7),
                    'updated_at' => now(),
                ]
            );

            // Mark first 5 lessons as completed (update if exists)
            $lessons = DB::table('lessons')->where('course_id', $introToAI->id)->take(5)->get();
            foreach ($lessons as $lesson) {
                DB::table('user_lesson_progress')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                    ],
                    [
                        'is_completed' => true,
                        'completed_at' => now()->subDays(rand(1, 6)),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}