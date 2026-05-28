<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MakeSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Find user with email admin@jlibrary.com and upgrade to super_admin
        $user = User::where('email', 'admin@jlibrary.com')->first();
        
        if ($user) {
            $user->role = 'super_admin';
            $user->save();
            $this->command->info('User ' . $user->email . ' upgraded to Super Admin!');
        } else {
            $this->command->error('User with email admin@jlibrary.com not found!');
        }
    }
}