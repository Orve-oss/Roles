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
            ['name' => 'manage-admins', 'guard_name' => 'web'],
            ['name' => 'manage-webs', 'guard_name' => 'web'],
            ['name' => 'manage-clients', 'guard_name' => 'web'],
            ['name' => 'manage-users', 'guard_name' => 'web'],
            ['name' => 'manage-services', 'guard_name' => 'web'],
            ['name' => 'manage-priorites', 'guard_name' => 'web'],
            ['name' => 'manage-types', 'guard_name' => 'web'],
            ['name' => 'manage-tickets', 'guard_name' => 'web']
        ];

        //Create roles
        $admin = Role::create(['name' => 'Admin', 'guard_name'=>'web']);
        $client = Role::create(['name' => 'Client', 'guard_name' => 'web']);
        $agent = Role::create(['name' => 'Agent', 'guard_name' => 'web']);

        //Create permissions
        foreach ($permissions as $key => $permission) {
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
            ]);
        }

        //Assign role
        $admin->givePermissionTo('manage-clients');
        $admin->givePermissionTo('manage-admins');
        $admin->givePermissionTo('manage-tickets');
        $admin->givePermissionTo('manage-users');
        $admin->givePermissionTo('manage-services');
        $admin->givePermissionTo('manage-priorites');
        $admin->givePermissionTo('manage-types');
        $client->givePermissionTo('manage-tickets');
        $agent->givePermissionTo('manage-tickets');
    }
}
