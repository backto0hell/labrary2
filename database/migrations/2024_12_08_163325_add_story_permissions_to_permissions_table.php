<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddStoryPermissionsToPermissionsTable extends Migration
{
    public function up()
    {
        // Добавление новых разрешений
        DB::table('permissions')->insert([
            [
                'name' => 'get-story-user',
                'code' => 'get-story-user',
                'description' => 'Get story of user',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'get-story-role',
                'code' => 'get-story-role',
                'description' => 'Get story of role',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'get-story-permission',
                'code' => 'get-story-permission',
                'description' => 'Get story of permission',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down()
    {
        DB::table('permissions')->whereIn('code', [
            'get-story-user',
            'get-story-role',
            'get-story-permission'
        ])->delete();
    }
}
