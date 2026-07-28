<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Warning;
use App\Models\User;
use Illuminate\Database\Seeder;

class WarningSeeder extends Seeder
{
    public function run(): void
    {
       $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();

        Warning::create([
            'user_id' => $jonathan->id,
            'reason' => 'Posted irrelevant content in the Software Engineering group.',
            'Issued_at' => now(),
        ]);

        Warning::create([
            'user_id' => $joel->id,
            'reason' => 'Used inappropriate language in a discussion thread.',
            'Issued_at' => now(),
        ]);  
    }
}
