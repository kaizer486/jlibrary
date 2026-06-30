<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vision, Mission, Motto
        SiteSetting::create([
            'key' => 'vision',
            'value' => 'To be the leading digital learning platform in Africa, empowering learners with accessible, quality education through technology.',
            'group' => 'content'
        ]);

        SiteSetting::create([
            'key' => 'mission',
            'value' => 'To provide innovative digital learning solutions that connect learners, educators, and knowledge seekers worldwide.',
            'group' => 'content'
        ]);

        SiteSetting::create([
            'key' => 'motto',
            'value' => 'Learn. Share. Grow.',
            'group' => 'content'
        ]);

        // Platform Message (like VC's message)
        SiteSetting::create([
            'key' => 'platform_message',
            'value' => 'Welcome to JLIBRARY, your gateway to a world of knowledge and learning. Whether you are a student, professional, or lifelong learner, our platform is designed to help you achieve your educational goals. With thousands of books, interactive communities, recognized certificates, and a vibrant marketplace, JLIBRARY is more than just a library—it is a learning ecosystem where everyone can grow together.

We believe that education should be accessible to all. Through technology and innovation, we are breaking down barriers and creating opportunities for learners everywhere. Join us on this journey to transform education and empower the next generation of thinkers, creators, and leaders.',
            'group' => 'content'
        ]);

        // Contact Information
        SiteSetting::create([
            'key' => 'contact_email',
            'value' => 'info@jlibrary.co.tz',
            'group' => 'contact'
        ]);

        SiteSetting::create([
            'key' => 'support_email',
            'value' => 'support@jlibrary.co.tz',
            'group' => 'contact'
        ]);

        SiteSetting::create([
            'key' => 'contact_phone',
            'value' => '0766408259',
            'group' => 'contact'
        ]);

        SiteSetting::create([
            'key' => 'address',
            'value' => 'Dar es Salaam, Tanzania',
            'group' => 'contact'
        ]);

        // Announcements (like CICT Digital Hub)
        SiteSetting::create([
            'key' => 'announcement_1',
            'value' => '🎉 New AI-Powered Book Recommendations Now Available! Discover books tailored to your interests.',
            'group' => 'announcements'
        ]);

        SiteSetting::create([
            'key' => 'announcement_2',
            'value' => '📚 50 New Books Added This Week. Explore our growing collection of educational resources.',
            'group' => 'announcements'
        ]);

        SiteSetting::create([
            'key' => 'announcement_3',
            'value' => '🌍 Community Book Club Launching June 1st. Join the conversation and connect with fellow readers.',
            'group' => 'announcements'
        ]);
    }
}