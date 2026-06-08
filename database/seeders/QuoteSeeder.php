<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            // Education Quotes
            [
                'quote_text' => 'The beautiful thing about learning is that no one can take it away from you.',
                'author' => 'B.B. King',
                'category' => 'education',
                'status' => 'active'
            ],
            [
                'quote_text' => 'Education is the most powerful weapon which you can use to change the world.',
                'author' => 'Nelson Mandela',
                'category' => 'education',
                'status' => 'active'
            ],
            [
                'quote_text' => 'Live as if you were to die tomorrow. Learn as if you were to live forever.',
                'author' => 'Mahatma Gandhi',
                'category' => 'education',
                'status' => 'active'
            ],
            [
                'quote_text' => 'The capacity to learn is a gift; the ability to learn is a skill; the willingness to learn is a choice.',
                'author' => 'Brian Herbert',
                'category' => 'education',
                'status' => 'active'
            ],
            
            // Motivation Quotes
            [
                'quote_text' => 'The only limit to our realization of tomorrow is our doubts of today.',
                'author' => 'Franklin D. Roosevelt',
                'category' => 'motivation',
                'status' => 'active'
            ],
            [
                'quote_text' => 'Don\'t watch the clock; do what it does. Keep going.',
                'author' => 'Sam Levenson',
                'category' => 'motivation',
                'status' => 'active'
            ],
            [
                'quote_text' => 'The future belongs to those who believe in the beauty of their dreams.',
                'author' => 'Eleanor Roosevelt',
                'category' => 'motivation',
                'status' => 'active'
            ],
            [
                'quote_text' => 'It does not matter how slowly you go as long as you do not stop.',
                'author' => 'Confucius',
                'category' => 'motivation',
                'status' => 'active'
            ],
            
            // Wisdom Quotes
            [
                'quote_text' => 'The only true wisdom is in knowing you know nothing.',
                'author' => 'Socrates',
                'category' => 'wisdom',
                'status' => 'active'
            ],
            [
                'quote_text' => 'Knowledge speaks, but wisdom listens.',
                'author' => 'Jimi Hendrix',
                'category' => 'wisdom',
                'status' => 'active'
            ],
            [
                'quote_text' => 'The journey of a thousand miles begins with one step.',
                'author' => 'Lao Tzu',
                'category' => 'wisdom',
                'status' => 'active'
            ],
            
            // Success Quotes
            [
                'quote_text' => 'Success is not final, failure is not fatal: it is the courage to continue that counts.',
                'author' => 'Winston Churchill',
                'category' => 'success',
                'status' => 'active'
            ],
            [
                'quote_text' => 'The only way to do great work is to love what you do.',
                'author' => 'Steve Jobs',
                'category' => 'success',
                'status' => 'active'
            ],
            [
                'quote_text' => 'Your time is limited, don\'t waste it living someone else\'s life.',
                'author' => 'Steve Jobs',
                'category' => 'success',
                'status' => 'active'
            ],
            
            // Happiness Quotes
            [
                'quote_text' => 'Happiness is not something ready made. It comes from your own actions.',
                'author' => 'Dalai Lama',
                'category' => 'happiness',
                'status' => 'active'
            ],
            [
                'quote_text' => 'The purpose of our lives is to be happy.',
                'author' => 'Dalai Lama',
                'category' => 'happiness',
                'status' => 'active'
            ],
            
            // Leadership Quotes
            [
                'quote_text' => 'A leader is one who knows the way, goes the way, and shows the way.',
                'author' => 'John C. Maxwell',
                'category' => 'leadership',
                'status' => 'active'
            ],
            [
                'quote_text' => 'The greatest leader is not necessarily the one who does the greatest things. He is the one that gets the people to do the greatest things.',
                'author' => 'Ronald Reagan',
                'category' => 'leadership',
                'status' => 'active'
            ],
            
            // Creativity Quotes
            [
                'quote_text' => 'Creativity is intelligence having fun.',
                'author' => 'Albert Einstein',
                'category' => 'creativity',
                'status' => 'active'
            ],
            [
                'quote_text' => 'Imagination is more important than knowledge.',
                'author' => 'Albert Einstein',
                'category' => 'creativity',
                'status' => 'active'
            ],
            
            // Life Quotes
            [
                'quote_text' => 'Life is what happens when you\'re busy making other plans.',
                'author' => 'John Lennon',
                'category' => 'life',
                'status' => 'active'
            ],
            [
                'quote_text' => 'In the end, it\'s not the years in your life that count. It\'s the life in your years.',
                'author' => 'Abraham Lincoln',
                'category' => 'life',
                'status' => 'active'
            ],
        ];

        foreach ($quotes as $quote) {
            Quote::create($quote);
        }
    }
}