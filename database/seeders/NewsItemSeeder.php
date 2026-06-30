<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NewsItem;
use Carbon\Carbon;

class NewsItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsItem::create([
            'title' => '50 New Books Added This Week',
            'content' => 'We have added 50 new titles to our digital library covering various subjects including technology, business, and literature.',
            'category' => 'Books',
            'link' => '/library',
            'is_featured' => true,
            'published_at' => Carbon::now()->subDays(1),
            'order' => 1,
            'is_active' => true
        ]);

        NewsItem::create([
            'title' => 'Virtual Book Club Launching June 1st',
            'content' => 'Join our first virtual book club session where we will discuss "Digital Transformation in Education" with authors and educators.',
            'category' => 'Events',
            'link' => '/community',
            'is_featured' => false,
            'published_at' => Carbon::now()->subDays(3),
            'order' => 2,
            'is_active' => true
        ]);

        NewsItem::create([
            'title' => 'Digital Literacy Certificate Program Launched',
            'content' => 'Enroll in our new Digital Literacy certificate program and gain essential skills for the digital age. Limited spots available.',
            'category' => 'Certificates',
            'link' => '/certificates',
            'is_featured' => true,
            'published_at' => Carbon::now()->subDays(5),
            'order' => 3,
            'is_active' => true
        ]);

        NewsItem::create([
            'title' => 'AI-Powered Book Recommendations Now Available',
            'content' => 'Our new AI recommendation engine suggests books based on your reading history and preferences. Discover your next favorite book!',
            'category' => 'Announcements',
            'link' => '/library',
            'is_featured' => false,
            'published_at' => Carbon::now()->subDays(7),
            'order' => 4,
            'is_active' => true
        ]);

        NewsItem::create([
            'title' => 'Author Spotlight: Dr. Sarah Chen',
            'content' => 'This month we feature Dr. Sarah Chen, author of "Digital Education Revolution". Read her insights on the future of learning.',
            'category' => 'Authors',
            'link' => '/community',
            'is_featured' => false,
            'published_at' => Carbon::now()->subDays(10),
            'order' => 5,
            'is_active' => true
        ]);

        NewsItem::create([
            'title' => 'Mobile App Coming Soon - Stay Tuned!',
            'content' => 'We are excited to announce that our mobile app is in development. Access JLIBRARY anytime, anywhere on your phone.',
            'category' => 'Announcements',
            'link' => null,
            'is_featured' => false,
            'published_at' => Carbon::now()->subDays(14),
            'order' => 6,
            'is_active' => true
        ]);
    }
}