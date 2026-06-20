<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use Carbon\Carbon;

class BooksTableSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title' => 'Mastering Laravel',
                'author' => 'Taylor Otwell',
                'description' => 'A comprehensive guide to Laravel framework. Learn from the creator himself!',
                'is_paid' => false,
                'price' => 0,
                'total_pages' => 450,
                'downloads' => 0,
                'status' => 'active',  
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Advanced PHP Programming',
                'author' => 'David Powers',
                'description' => 'Take your PHP skills to the next level with advanced concepts and best practices.',
                'is_paid' => true,
                'price' => 25000,
                'total_pages' => 380,
                'downloads' => 0,
                'status' => 'active',  // Changed from 'published'
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'JavaScript: The Good Parts',
                'author' => 'Douglas Crockford',
                'description' => 'Discover the elegant parts of JavaScript and avoid the bad parts.',
                'is_paid' => false,
                'price' => 0,
                'total_pages' => 176,
                'downloads' => 0,
                'status' => 'active',  
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Database Design for Beginners',
                'author' => 'Sarah Johnson',
                'description' => 'Learn how to design efficient and scalable databases from scratch.',
                'is_paid' => true,
                'price' => 15000,
                'total_pages' => 320,
                'downloads' => 0,
                'status' => 'active',  
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'UI/UX Design Principles',
                'author' => 'Don Norman',
                'description' => 'Master the fundamentals of user interface and user experience design.',
                'is_paid' => false,
                'price' => 0,
                'total_pages' => 280,
                'downloads' => 0,
                'status' => 'active',  
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        $this->command->info('Books seeded successfully!');
    }
}