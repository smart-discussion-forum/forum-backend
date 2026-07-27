<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::where('email', 'lecturer@mindshare.com')->first();

        Quiz::create([
            'Lecturer_id' => $lecturer->id,
            'Title' => 'Software Engineering Fundamentals Quiz',
            'Target_category' => '1',
            'Publish_time' => now()->subDays(1),
            'Duration' => 30,
            'announced_at' => now()->subDays(2),
        ]);

        Quiz::create([
            'Lecturer_id' => $lecturer->id,
            'Title' => 'Database Systems Quiz',
            'Target_category' => '3',
            'Publish_time' => now()->subMinutes(5),
            'Duration' => 20,
            'announced_at' => now()->subDay(),
        ]);
    }
}