<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MessageExclusion;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageExclusionSeeder extends Seeder
{
    //Running the database seeds
    public function run(): void
    {
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();

        //Noerine to Joel, excluding Jonathan from seeing the message
        $message1= Message::find(1);
        MessageExclusion::create([
            'message_id' => $message1->id,
            'excluded_user_id' => $jonathan->id,
        ]);

       // Lecturer to Noerine, excludes Joel from seeing it
       $message3 = Message::find(3);
       MessageExclusion::create([
        'message_id' => $message3->id,
        'excluded_user_id' => $joel->id,
       ]);
    }
}
