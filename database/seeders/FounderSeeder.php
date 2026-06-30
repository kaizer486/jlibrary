<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Founder;

class FounderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Founder::create([
            'name' => 'Josiah Nashon',
            'title' => 'Founder & Super Admin',
            'bio' => 'Josiah Nashon is the visionary founder and Super Admin of JLIBRARY. With a passion for technology and education, Josiah created JLIBRARY to bridge the gap between traditional learning and digital innovation.

"Education is the most powerful weapon which you can use to change the world. JLIBRARY is my contribution to making quality education accessible to everyone, everywhere." - Josiah Nashon',
            'photo' => 'images/founders/josiah-nashon.jpg',
            'email' => 'info@jlibrary.co.tz',
            'phone' => '0766408259',
            'social_links' => json_encode([
                'twitter' => 'https://x.com/JNashon20',
                'instagram' => 'https://www.instagram.com/jos_nash1',
                'tiktok' => 'https://vm.tiktok.com/ZS92RTEBEc8QV-k5LOd/',
                'facebook' => 'https://www.facebook.com/share/1YDDzy1gnJ/',
                'whatsapp' => 'https://whatsapp.com/channel/0029VaC8Tg460eBjk82Xlt0U',
                'youtube' => 'https://youtube.com/@jlibraryonlinesytem'
            ]),
            'order' => 1,
            'is_active' => true
        ]);
    }
}