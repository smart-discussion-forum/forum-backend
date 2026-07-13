<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@mindshare.com')->first();
        $lecturer = User::where('email', 'lecturer@mindshare.com')->first();

        Group::create([
            'name' => 'Software Engineering Year 2',
            'description' => 'Discussion group for Year 2 Software Engineering students.',
            'created_by' => $admin->id,
        ]);

        Group::create([
            'name' => 'Web Development',
            'description' => 'Group for web development topics and discussions.',
            'created_by' => $lecturer->id,
        ]);

        Group::create([
            'name' => 'Database Systems',
            'description' => 'Group covering database design and SQL topics.',
            'created_by' => $lecturer->id,
        ]);
    }
}

  
