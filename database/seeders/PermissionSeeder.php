<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            ['name' => 'get-list-user', 'code' => 'get-list-user', 'description' => 'Get list of users'],
            ['name' => 'read-user', 'code' => 'read-user', 'description' => 'Read user details'],
            ['name' => 'create-user', 'code' => 'create-user', 'description' => 'Create a new user'],
            ['name' => 'update-user', 'code' => 'update-user', 'description' => 'Update user details'],
            ['name' => 'delete-user', 'code' => 'delete-user', 'description' => 'Delete a user'],

            ['name' => 'get-list-role', 'code' => 'get-list-role', 'description' => 'Get list of roles'],
            ['name' => 'read-role', 'code' => 'read-role', 'description' => 'Read role details'],
            ['name' => 'create-role', 'code' => 'create-role', 'description' => 'Create a new role'],
            ['name' => 'update-role', 'code' => 'update-role', 'description' => 'Update role details'],
            ['name' => 'delete-role', 'code' => 'delete-role', 'description' => 'Delete a role'],

            ['name' => 'get-list-permission', 'code' => 'get-list-permission', 'description' => 'Get list of permissions'],
            ['name' => 'read-permission', 'code' => 'read-permission', 'description' => 'Read permission details'],
            ['name' => 'create-permission', 'code' => 'create-permission', 'description' => 'Create a new permission'],
            ['name' => 'update-permission', 'code' => 'update-permission', 'description' => 'Update permission details'],
            ['name' => 'delete-permission', 'code' => 'delete-permission', 'description' => 'Delete a permission'],

            ['name' => 'restore-user', 'code' => 'restore-user', 'description' => 'Restore a soft-deleted user'],
            ['name' => 'soft-delete-role', 'code' => 'soft-delete-role', 'description' => 'Soft delete a role'],
            ['name' => 'restore-role', 'code' => 'restore-role', 'description' => 'Restore a soft-deleted role'],
            ['name' => 'soft-delete-permission', 'code' => 'soft-delete-permission', 'description' => 'Soft delete a permission'],
            ['name' => 'restore-permission', 'code' => 'restore-permission', 'description' => 'Restore a soft-deleted permission'],
        ]);
    }
}
