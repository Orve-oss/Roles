<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage-admins',
            'manage-clients',
            'manage-tickets',
            'manage-users',
            'manage-services',
            'manage-priorites',
            'manage-types'
        ];

        //Create roles
        $admin = Role::create(['name' => 'Admin']);
        $client = Role::create(['name' => 'Client']);
        $agent = Role::create(['name' => 'Agent']);

        //Create permissions
        foreach ($permissions as $key => $permission) {
            Permission::create(['name'=>$permission]);
        }

        //Assign role
        $admin->givePermissionTo('manage-clients');
        $admin->givePermissionTo('manage-admins');
        $admin->givePermissionTo('manage-tickets');
        $admin->givePermissionTo('manage-users');
        $admin->givePermissionTo('manage-services');
        $admin->givePermissionTo('manages-priorites');
        $admin->givePermissionTo('manages-types');
        $client->givePermissionTo('manage-tickets');
        $agent->givePermissionTo('manage-tickets');
    }
}
