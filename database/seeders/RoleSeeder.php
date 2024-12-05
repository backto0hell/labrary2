<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'code' => 'admin', 'description' => 'Administrator'],
            ['name' => 'User', 'code' => 'user', 'description' => 'Regular User'],
            ['name' => 'Guest', 'code' => 'guest', 'description' => 'Guest User'],
        ]);
    }
}
