<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\QuizQuestion;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
       public function run(): void
    {
      $quiz1 = Quiz::find(1);
        $quiz2 = Quiz::find(2);

                // Questions for Quiz 1
        QuizQuestion::create([
            'quiz_id' => $quiz1->quiz_id,
            'question' => 'What does the acronym SDLC stand for?',
            'options' => json_encode([
                'Software Development Life Cycle',
                'System Design and Logic Control',
                'Software Design and Layout Creation',
                'System Development and Launch Cycle',
            ]),
            'correct_answer' => '0',//index 0 = Software Development Life Cycle
            'Marks' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->quiz_id,
            'question' => 'Which design pattern does Laravel use?',
            'options' => json_encode([
                'Singleton',
                'Model View Controller',
                'Observer',
                'Factory',
            ]),
            'correct_answer' => '1',//index 1 = Model View Controller
            'Marks' => 2,
        ]);
            
QuizQuestion::create([
            'quiz_id' => $quiz1->quiz_id,
            'question' => 'What is the purpose of a Software Design Document?',
            'options' => json_encode([
                'To test the software',
                'To deploy the software',
                'To describe the architecture and design of a system',
                'To market the software',
            ]),
            'correct_answer' => '2',//index 2 = To describe the architecture and design of a system
            'Marks' => 2,
        ]);
            

        // Questions for Quiz 2
        QuizQuestion::create([
            'quiz_id' => $quiz2->quiz_id,
            'question' => 'What does SQL stand for?',
            'options' => json_encode([
                'Structured Query Language',
                'Simple Query Logic',
                'System Query Language',
                'Structured Question List',
            ]),
            'correct_answer' => '0',//index 0 = Structured Query Language
            'Marks' => 2,
        ]);

         QuizQuestion::create([
            'quiz_id' => $quiz2->quiz_id,
            'question' => 'Which of the following is a NoSQL database?',
            'options' => json_encode([
                'MySQL',
                'PostgreSQL',
                'MongoDB',
                'MariaDB',
            ]),
            'correct_answer' => '2',//index 2 = MongoDB
            'Marks' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->quiz_id,
            'question' => 'What is a foreign key?',
            'options' => json_encode([
                'A key used to encrypt data',
                'A field that links two tables together',
                'A primary identifier for a table',
                'A key used for authentication',
            ]),
            'correct_answer' => '1',//index 1 = A field that links two tables together
            'Marks' => 2,
        ]);
        }
}

      
