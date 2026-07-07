<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    
    public function run(): void
    {
        $noerine = User::where('email', 'noerine@mindshare.com')->first();
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();
        $admin = User::where('email', 'admin@mindshare.com')->first();

        $group1 = Group::find(1); // Software Engineering Year 2
        $group2 = Group::find(2); // Web Development
        $group3 = Group::find(3); // Database Systems

        $group1->members()->attach($noerine->id, ['role' => 'Moderator', 'joined_at' => now()]);
        $group1->members()->attach($jonathan->id, ['role' => 'Member', 'joined_at' => now()]);
        $group1->members()->attach($joel->id, ['role' => 'Member', 'joined_at' => now()]);
        $group2->members()->attach($noerine->id, ['role' => 'Member', 'joined_at' => now()]);
        $group2->members()->attach($jonathan->id, ['role' => 'Moderator', 'joined_at' => now()]);
        $group3->members()->attach($joel->id, ['role' => 'Member', 'joined_at' => now()]);
        $group3->members()->attach($admin->id, ['role' => 'Moderator', 'joined_at' => now()]);
    }
}
