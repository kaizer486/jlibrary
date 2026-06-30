<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeroSlide;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeroSlide::create([
            'title' => 'Empowering Digital Learning Through Innovation',
            'subtitle' => 'Your all-in-one digital library platform. Read thousands of books, connect with a global community, earn recognized certificates, and sell your own work.',
            'image' => 'images/hero/slide1.jpg',
            'cta_text' => 'Get Started Free',
            'cta_url' => '/register',
            'order' => 1,
            'is_active' => true
        ]);

        HeroSlide::create([
            'title' => 'Join a Global Learning Community',
            'subtitle' => 'Connect with learners and educators from around the world. Share knowledge, discuss ideas, and grow together.',
            'image' => 'images/hero/slide2.jpg',
            'cta_text' => 'Join Community',
            'cta_url' => '/community',
            'order' => 2,
            'is_active' => true
        ]);

        HeroSlide::create([
            'title' => 'Earn Recognized Certificates',
            'subtitle' => 'Validate your skills and knowledge with our certificate programs. Showcase your achievements to employers and peers.',
            'image' => 'images/hero/slide3.jpg',
            'cta_text' => 'Explore Certificates',
            'cta_url' => '/certificates',
            'order' => 3,
            'is_active' => true
        ]);

        HeroSlide::create([
            'title' => 'Sell Your Books & Courses',
            'subtitle' => 'Publish and sell your work directly to the community. Reach thousands of learners and earn from your knowledge.',
            'image' => 'images/hero/slide4.jpg',
            'cta_text' => 'Start Selling',
            'cta_url' => '/marketplace',
            'order' => 4,
            'is_active' => true
        ]);
    }
}