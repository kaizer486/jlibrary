<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Certificate',
                'description' => 'Earn your first course certificate',
                'icon' => 'ti-certificate',
                'color' => '#fbbf24',
                'required_points' => 1,
                'type' => 'certificate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quiz Master',
                'description' => 'Score 100% on any quiz',
                'icon' => 'ti-star',
                'color' => '#ef4444',
                'required_points' => 1,
                'type' => 'quiz_master',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '7 Day Streak',
                'description' => 'Learn for 7 days in a row',
                'icon' => 'ti-flame',
                'color' => '#f97316',
                'required_points' => 7,
                'type' => 'streak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Community Leader',
                'description' => 'Join 5 community groups',
                'icon' => 'ti-users',
                'color' => '#10b981',
                'required_points' => 5,
                'type' => 'social',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('achievements')->insert($achievements);
    }
}