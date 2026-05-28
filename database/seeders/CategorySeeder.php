<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Artificial Intelligence', 'slug' => 'ai', 'icon' => 'ti-robot', 'color' => '#7c3aed', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Web Development', 'slug' => 'web-dev', 'icon' => 'ti-code', 'color' => '#3b82f6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Data Science', 'slug' => 'data-science', 'icon' => 'ti-database', 'color' => '#10b981', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Business', 'slug' => 'business', 'icon' => 'ti-briefcase', 'color' => '#f59e0b', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Design', 'slug' => 'design', 'icon' => 'ti-palette', 'color' => '#ec4899', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'slug' => 'marketing', 'icon' => 'ti-chart-bar', 'color' => '#ef4444', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}