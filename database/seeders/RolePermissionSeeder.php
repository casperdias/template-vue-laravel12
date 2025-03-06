<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make Role Super Admin
        $roleSuperAdmin = \App\Models\Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Admin',
            'description' => 'Super Admin Role',
        ]);

        // Make example permissions
        $permissions = \App\Models\Permission::create([
            'name' => 'master-data',
            'display_name' => 'Example',
            'description' => 'Example Permission',
        ]);

        // Make Staff Role
        $roleStaff = \App\Models\Role::create([
            'name' => 'staff',
            'display_name' => 'Staff',
            'description' => 'Staff Role',
        ]);

        // Make SPV Role
        $roleSpv = \App\Models\Role::create([
            'name' => 'spv',
            'display_name' => 'Supervisor',
            'description' => 'Supervisor Role',
        ]);

        $roleSuperAdmin->permissions()->attach($permissions->id);
    }
}
