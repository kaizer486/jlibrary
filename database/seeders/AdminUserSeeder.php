<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'full_name' => 'Josiah Nashon',
            'email' => 'admin@jlibrary.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'wallet_balance' => 0,
        ]);

        User::create([
            'full_name' => 'Demo User',
            'email' => 'user@jlibrary.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'wallet_balance' => 100, // Demo balance
        ]);
    }
}