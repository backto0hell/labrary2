<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ReapplyMissingRecords extends Migration
{
    public function up()
    {
        $adminRole = DB::table('roles')->where('code', 'admin')->first();

        if ($adminRole) {
            $permissions = DB::table('permissions')->whereIn('code', [
                'get-story-user',
                'get-story-role',
                'get-story-permission'
            ])->pluck('id');

            foreach ($permissions as $permissionId) {
                DB::table('roles_and_permissions')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                ]);
            }
        }
    }

    public function down()
    {
        $adminRole = DB::table('roles')->where('code', 'admin')->first();

        if ($adminRole) {
            $permissions = DB::table('permissions')->whereIn('code', [
                'get-story-user',
                'get-story-role',
                'get-story-permission'
            ])->pluck('id');

            DB::table('roles_and_permissions')
                ->where('role_id', $adminRole->id)
                ->whereIn('permission_id', $permissions)
                ->delete();
        }
    }
}

