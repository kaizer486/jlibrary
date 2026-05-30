<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@jlibrary.com'],
            [
                'full_name' => 'Josiah Nashon',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'wallet_balance' => 0,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@jlibrary.com'],
            [
                'full_name' => 'Demo User',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'wallet_balance' => 100,
            ]
        );
    }
}