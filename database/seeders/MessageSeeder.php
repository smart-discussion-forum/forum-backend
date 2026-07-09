<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Message;
use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    
    public function run(): void
    {
        $noerine = User::where('email', 'noerine@mindshare.com')->first();
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();
        $lecturer = User::where('email', 'lecturer@mindshare.com')->first();
        $admin = User::where('email', 'admin@mindshare.com')->first(); 
    
        $group1 = Group::find(1); // Software Engineering Year 2
        $group2 = Group::find(2); // Web Development
        $group3 = Group::find(3); // Database Systems

        
        Message::create([
            'sender_id' => $noerine->id,
            'group_id' => $group1->id,
            'content' => 'Hey everyone, have you started on the Role-Based Access Control middleware yet?',
            'is_synced' => true,
        ]);

         Message::create([
            'sender_id' => $joel->id,
            'group_id' => $group1->id,
            'content' => 'Yes, just setting up my environment. Will push the Role-Based Access Control middleware by tonight.',
            'is_synced' => true,
        ]);

         Message::create([
            'sender_id' => $lecturer->id,
            'group_id' => $group1->id,
            'content' => 'Please make sure your work follows the Software Design Document specifications exactly.',
            'is_synced' => true,
        ]);

        Message::create([
            'sender_id' => $jonathan->id,
            'group_id' => $group2->id,
            'content' => 'I have started on the Blade views for the dashboard and group list screens.',
            'is_synced' => true,
        ]);
        
        Message::create([
            'sender_id' => $noerine->id,
            'group_id' => $group2->id,
            'content' => 'Great, I will have the topic and post migrations ready for you to build on top of.',
            'is_synced' => true,
        ]);

        Message::create([
            'sender_id' => $admin->id,
            'group_id' => $group3->id,
            'content' => 'Reminder to all members: follow the push and pull protocol to avoid merge conflicts.',
            'is_synced' => true,
        ]);

        Message::create([
            'sender_id' => $joel->id,
            'group_id' => $group3->id,
            'content' => 'Understood. I will create my feature branch from Noerine\'s branch as agreed.',
            'is_synced' => true,
        ]);
         
        }
}
