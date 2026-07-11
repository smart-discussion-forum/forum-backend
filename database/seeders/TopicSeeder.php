<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Group;
use App\Models\User;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{

    public function run(): void
    {
        $noerine = User::where('email', 'noerine@mindshare.com')->first();
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();
        $lecturer = User::where('email', 'lecturer@mindshare.com')->first();

        $group1 = Group::find(1); // Software Engineering Year 2
        $group2 = Group::find(2); // Web Development
        $group3 = Group::find(3); // Database Systems

        Topic::create([
            'group_id' => $group1->id,
            'created_by' => $noerine->id,
            'title' => 'Introduction to Software Engineering',
            'category' => 'Programming',
        ]);

        Topic::create([
            'group_id' => $group1->id,
            'created_by' => $jonathan->id,
            'title' => 'Agile vs Waterfall Methodology',
            'category' => 'General',
        ]);

        Topic::create([
            'group_id' => $group2->id,
            'created_by' => $lecturer->id,
            'title' => 'Getting Started with Laravel',
            'category' => 'Programming',
        ]);

        Topic::create([
            'group_id' => $group2->id,
            'created_by' => $joel->id,
            'title' => 'Understanding RESTful APIs',
            'category' => 'Programming',
        ]);

                Topic::create([
            'group_id' => $group3->id,
            'created_by' => $lecturer->id,
            'title' => 'Entity Relationship Diagrams Explained',
            'category' => 'Database',
        ]);

        Topic::create([
            'group_id' => $group3->id,
            'created_by' => $noerine->id,
            'title' => 'SQL vs NoSQL Databases',
            'category' => 'Database',
        ]);
    }
}
