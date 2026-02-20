<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Administrator user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@gmail.com'),
        ]);

        // Create an HRD user
        User::create([
            'name' => 'HRD',
            'email' => 'hrd@gmail.com',
            'password' => bcrypt('hrd@gmail.com'),
        ]);

        // Create a regular user
        User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('user@gmail.com'),
        ]);
    }
}
