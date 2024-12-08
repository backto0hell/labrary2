<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Создание ролей
        $roles = [
            ['name' => 'Admin', 'code' => 'admin', 'description' => 'Administrator role', 'created_by' => 1],
            ['name' => 'User', 'code' => 'user', 'description' => 'User role', 'created_by' => 1],
            ['name' => 'Guest', 'code' => 'guest', 'description' => 'Guest role', 'created_by' => 1],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['code' => $role['code']], $role);
        }

        // Создание разрешений
        $permissions = Permission::all();

        // Связки для роли "Admin"
        $adminRole = Role::where('code', 'admin')->first();
        $adminRole->permissions()->sync($permissions->pluck('id')->mapWithKeys(function ($id) {
            return [$id => ['created_by' => 1]];
        })->toArray());

        // Связки для роли "User"
        $userRole = Role::where('code', 'user')->first();
        $userPermissions = Permission::whereIn('code', ['get-list-user', 'read-user', 'update-user'])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id')->mapWithKeys(function ($id) {
            return [$id => ['created_by' => 1]];
        })->toArray());

        // Связки для роли "Guest"
        $guestRole = Role::where('code', 'guest')->first();
        $guestPermissions = Permission::whereIn('code', ['get-list-user'])->get();
        $guestRole->permissions()->sync($guestPermissions->pluck('id')->mapWithKeys(function ($id) {
            return [$id => ['created_by' => 1]];
        })->toArray());
    }
}
