<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(5)->create();

        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => "test@gmail.com",
            'password' => Hash::make('123456')
        ])->assignRole('admin');
        
        User::create([
            'name' => 'Incident Manager',
            'email' => "prueba@gmail.com",
            'password' => Hash::make('123456')
        ])->assignRole('incident_manager');
    }
}
