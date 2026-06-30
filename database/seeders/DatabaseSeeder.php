<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Existing seeders
            AdminUserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            LessonSeeder::class,
            AchievementSeeder::class,
            UserProgressSeeder::class,

            // New seeders for welcome page
            HeroSlideSeeder::class,
            NewsItemSeeder::class,
            FounderSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}