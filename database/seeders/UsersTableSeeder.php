<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'           => 'Admin User',
                'password'       => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id'        => $adminRole->id,
            ]
        );

        // Create Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name'           => 'Store Manager',
                'password'       => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id'        => $adminRole->id,
            ]
        );

        // Create regular users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
                'role_id' => $userRole->id,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
                'role_id' => $userRole->id,
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
                'role_id' => $userRole->id,
            ],
            [
                'name' => 'Alice Williams',
                'email' => 'alice@example.com',
                'password' => bcrypt('password'),
                'role_id' => $userRole->id,
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'password' => bcrypt('password'),
                'role_id' => $userRole->id,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'remember_token' => str_random(60),
                ])
            );
        }
    }
}
