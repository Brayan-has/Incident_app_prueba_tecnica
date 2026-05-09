<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(500)->create();

        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => "test@gmail.com",
            'password' => Hash::make('123456')
        ]);
        
    }
}
