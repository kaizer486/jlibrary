<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        $quiz = Quiz::create([
            'title' => 'Programming Fundamentals',
            'description' => 'Test your knowledge of basic programming concepts including variables, loops, and functions.',
            'time_limit' => 15,
            'passing_score' => 70,
            'is_active' => true,
        ]);
        
        Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'What does PHP stand for?',
            'option_a' => 'Personal Home Page',
            'option_b' => 'Pre Hypertext Processor',
            'option_c' => 'PHP: Hypertext Preprocessor',
            'option_d' => 'Professional Hosting Protocol',
            'correct_answer' => 'C',
            'points' => 1,
        ]);
        
        Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Which symbol is used to access a variable in PHP?',
            'option_a' => '&',
            'option_b' => '$',
            'option_c' => '#',
            'option_d' => '@',
            'correct_answer' => 'B',
            'points' => 1,
        ]);
        
        Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'What does SQL stand for?',
            'option_a' => 'Structured Query Language',
            'option_b' => 'Simple Query Language',
            'option_c' => 'Stylish Question Language',
            'option_d' => 'System Query Logic',
            'correct_answer' => 'A',
            'points' => 1,
        ]);
    }
}