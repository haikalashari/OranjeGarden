<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create specific delivery user
        User::factory()->create([
            'name' => 'Delivery User',
            'email' => 'delivery@example.com',
            'password' => Hash::make('password123'),
            'role' => 'delivery',
        ]);

        // Create random users
        User::factory()
            ->count(8)
            ->create();
    }
}
