<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceCategory;

class MarketplaceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'E-books', 'slug' => 'e-books', 'icon' => '📱', 'description' => 'Digital books and publications'],
            ['name' => 'Physical Books', 'slug' => 'physical-books', 'icon' => '📖', 'description' => 'Physical books for delivery'],
            ['name' => 'Textbooks', 'slug' => 'textbooks', 'icon' => '📚', 'description' => 'Academic and educational textbooks'],
            ['name' => 'Study Guides', 'slug' => 'study-guides', 'icon' => '📝', 'description' => 'Study materials and guides'],
            ['name' => 'Research Papers', 'slug' => 'research-papers', 'icon' => '🔬', 'description' => 'Academic research papers'],
            ['name' => 'Course Materials', 'slug' => 'course-materials', 'icon' => '🎓', 'description' => 'Course notes and materials'],
            ['name' => 'Workbooks', 'slug' => 'workbooks', 'icon' => '📋', 'description' => 'Practice workbooks and exercises'],
            ['name' => 'Reference Books', 'slug' => 'reference-books', 'icon' => '📕', 'description' => 'Reference and encyclopedia books'],
        ];

        foreach ($categories as $category) {
            MarketplaceCategory::create($category);
        }
    }
}