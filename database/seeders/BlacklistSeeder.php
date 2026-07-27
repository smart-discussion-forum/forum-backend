<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\RoleEnum;
use App\Models\Blacklist;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlacklistSeeder extends Seeder
{
        public function run(): void
    {
        $blacklistedUser = User::create([
            'name' => 'Test Blacklisted User',
            'email' => 'blacklisted@mindshare.com',
            'password' => bcrypt('password'),
            'role' => RoleEnum::Student,
            'status' => 'Blacklisted',
        ]);

         Blacklist::create([
            'User_id' => $blacklistedUser->id,
            'Reason' => 'Accumulated two warnings for repeated violation of platform rules.',
            'Blacklisted_at' => now(),
            'Expires_at' => null,
        ]);
    }
}
