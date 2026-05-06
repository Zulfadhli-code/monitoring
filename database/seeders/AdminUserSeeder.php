<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin'
        ]);

        // USER
        User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user'
        ]);
    }
}