<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $incident_manager = Role::firstOrCreate(['name' => 'incident_manager']);


        // create permissions for incident
        Permission::firstOrCreate(['name' => 'create-incident'])->assignRole($admin, $incident_manager);
        Permission::firstOrCreate(['name' => 'edit-incident'])->assignRole($admin, $incident_manager);
        Permission::firstOrCreate(['name' => 'delete-incident'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'view-incident'])->assignRole($admin, $incident_manager);


        // create permissions for user
        Permission::firstOrCreate(['name' => 'create-user'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'edit-user'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'delete-user'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'view-user'])->assignRole($admin, $incident_manager);

        // create permissions for roles
        Permission::firstOrCreate(['name' => 'view-role'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'assign-role'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'get-user-role'])->assignRole($admin);

        // create permissions for permissions
        Permission::firstOrCreate(['name' => 'edit-permission'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'delete-permission'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'view-permission'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'assign-permission'])->assignRole($admin);
    }
}
