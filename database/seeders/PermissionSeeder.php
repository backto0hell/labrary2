<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $entities = ['user', 'role', 'permission'];

        foreach ($entities as $entity) {
            $permissions = [
                ['name' => 'get-list-' . $entity, 'code' => 'get-list-' . $entity, 'description' => 'Get list of ' . $entity, 'created_by' => 1],
                ['name' => 'read-' . $entity, 'code' => 'read-' . $entity, 'description' => 'Read ' . $entity, 'created_by' => 1],
                ['name' => 'create-' . $entity, 'code' => 'create-' . $entity, 'description' => 'Create ' . $entity, 'created_by' => 1],
                ['name' => 'update-' . $entity, 'code' => 'update-' . $entity, 'description' => 'Update ' . $entity, 'created_by' => 1],
                ['name' => 'delete-' . $entity, 'code' => 'delete-' . $entity, 'description' => 'Delete ' . $entity, 'created_by' => 1],
                ['name' => 'restore-' . $entity, 'code' => 'restore-' . $entity, 'description' => 'Restore ' . $entity, 'created_by' => 1],
            ];

            foreach ($permissions as $permission) {
                Permission::create($permission);
            }
        }
    }
}
