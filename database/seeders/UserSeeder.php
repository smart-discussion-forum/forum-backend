<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mindshare.com',
            'password' =>Hash::make('password'),
            'role' => RoleEnum::Admin,
            'status' =>StatusEnum::Active,
        ]);

        User::create([
            'name' => 'Dr. Gloria',
            'email' => 'lecturer@mindshare.com',
            'password' => Hash::make('password'),
            'role' => RoleEnum::Lecturer,
            'status' => StatusEnum::Active,
        ]);

        User::create([
            'name' => 'Noerine Namuganza',
            'email' => 'noerine@mindshare.com',
            'password' => Hash::make('password'),
            'role' => RoleEnum::Student,
            'status' => StatusEnum::Active,
        ]);

        User::create([
            'name' => 'Jonathan Isiko',
            'email' => 'jonathan@mindshare.com',
            'password' => Hash::make('password'),
            'role' => RoleEnum::Student,
            'status' => StatusEnum::Active,
        ]);

        User::create([
            'name' => 'Joel Agaba',
            'email' => 'joel@mindshare.com',
            'password' => Hash::make('password'),
            'role' => RoleEnum::Student,
            'status' => StatusEnum::Active,
        ]);
    }
}
