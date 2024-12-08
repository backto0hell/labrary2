<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Создание пользователей
        $users = [
            ['username' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password123')],
            ['username' => 'Regular User', 'email' => 'user@example.com', 'password' => bcrypt('password123')],
            ['username' => 'Guest User', 'email' => 'guest@example.com', 'password' => bcrypt('password123')],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(['email' => $userData['email']], $userData);
            Log::info('User created or updated', ['user' => $user]);

            // Назначение ролей пользователям с добавлением created_by
            if ($userData['email'] == 'admin@example.com') {
                $role = Role::where('code', 'admin')->first();
            } elseif ($userData['email'] == 'user@example.com') {
                $role = Role::where('code', 'user')->first();
            } elseif ($userData['email'] == 'guest@example.com') {
                $role = Role::where('code', 'guest')->first();
            }

            DB::table('users_and_roles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $role->id
                ],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'created_by' => 1
                ]
            );

            Log::info('User role assigned', ['user_id' => $user->id, 'role_id' => $role->id]);
        }
    }
}
