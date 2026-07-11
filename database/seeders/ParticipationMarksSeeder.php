<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ParticipationMark;
use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Seeder;

class ParticipationMarksSeeder extends Seeder
{

    public function run(): void
    {
        $noerine = User::where('email', 'noerine@mindshare.com')->first();
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();

        $group1 = Group::find(1);
        $group2 = Group::find(2);
        $group3 = Group::find(3);

       ParticipationMark::create([
        'user_id' =>$noerine->id,
        'group_id' =>$group1->id,
        'score' =>85.50,
       ]);

       ParticipationMark::create([
            'user_id' => $noerine->id,
            'group_id' => $group2->id,
            'score' => 78.00,
        ]);

        ParticipationMark::create([
            'user_id' => $jonathan->id,
            'group_id' => $group1->id,
            'score' => 72.00,
        ]);

        ParticipationMark::create([
            'user_id' => $jonathan->id,
            'group_id' => $group2->id,
            'score' => 90.00,
        ]);


        ParticipationMark::create([
            'user_id' => $joel->id,
            'group_id' => $group1->id,
            'score' => 65.50,
        ]);

        ParticipationMark::create([
            'user_id' => $joel->id,
            'group_id' =>$group3->id,
            'score' => 88.00,
        ]);

    }
}
