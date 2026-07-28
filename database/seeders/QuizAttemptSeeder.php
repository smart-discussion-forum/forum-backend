<?php

namespace Database\Seeders;

use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $noerine = User::where('email', 'noerine@mindshare.com')->first();
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();

        $quiz1 = Quiz::find(1);
        $quiz2 = Quiz::find(2);

        QuizAttempt::create([
            'quiz_id' => $quiz1->quiz_id,
            'Student_id' => $noerine->id,
            'started_at' => now()->subHours(2),
            'submitted_at' => now()->subHours(1),
            'Score' => 6.00,
            'Auto_submitted' => false,
        ]);

        QuizAttempt::create([
            'quiz_id' => $quiz1->quiz_id,
            'Student_id' => $jonathan->id,
            'started_at' => now()->subHours(2),
            'submitted_at' => now()->subHours(1),
            'Score' => 4.00,
            'Auto_submitted' => true,
        ]);

        QuizAttempt::create([
            'quiz_id' => $quiz2->quiz_id,
            'Student_id' => $noerine->id,
            'started_at' => now()->subDay(),
            'submitted_at' => now()->subDay()->addMinutes(18),
            'Score' => 6.00,
            'Auto_submitted' => false,
        ]);

        QuizAttempt::create([
            'quiz_id' => $quiz2->quiz_id,
            'Student_id' => $joel->id,
            'started_at' => now()->subDay(),
            'submitted_at' => now()->subDay()->addMinutes(20),
            'Score' => 4.00,
            'Auto_submitted' => false,
        ]);
    }
}