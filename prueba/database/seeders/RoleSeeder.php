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
        $admin = Role::create(['name' => 'admin']);
        $incident_manager = Role::create(['name' => 'incident_manager']);


        // create permissions for incident
        Permission::create(['name' => 'create-incident'])->assignRole($admin, $incident_manager);
        Permission::create(['name' => 'edit-incident'])->assignRole($admin, $incident_manager);
        Permission::create(['name' => 'delete-incident'])->assignRole($admin);
        Permission::create(['name' => 'view-incident'])->assignRole($admin, $incident_manager);


        // create permissions for user
        Permission::create(['name' => 'create-user'])->assignRole($admin);
        Permission::create(['name' => 'edit-user'])->assignRole($admin);
        Permission::create(['name' => 'delete-user'])->assignRole($admin);
        Permission::create(['name' => 'view-user'])->assignRole($admin, $incident_manager);

        // create permissions for roles
        Permission::create(['name' => 'view-role'])->assignRole($admin);
        Permission::create(['name' => 'assign-role'])->assignRole($admin);
        Permission::create(['name' => 'get-user-role'])->assignRole($admin);

        // create permissions for permissions
        Permission::create(['name' => 'edit-permission'])->assignRole($admin);
        Permission::create(['name' => 'delete-permission'])->assignRole($admin);
        Permission::create(['name' => 'view-permission'])->assignRole($admin);
        Permission::create(['name' => 'assign-permission'])->assignRole($admin);


        


    }
}
