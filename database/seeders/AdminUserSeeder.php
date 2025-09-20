<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ruhul Ameen',
            'email' => 'ameensunny242@gmail.com',
            'password' => Hash::make('ruhulAmeen!!@242'),
            'phone' => '1234567890',
            'role' => 'admin',
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);
    }
}
