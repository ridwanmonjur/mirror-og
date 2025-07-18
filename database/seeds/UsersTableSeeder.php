<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'mjrrdn@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'email_verified_at' => now(),
            ]
        );

        // Create organizer user
        User::firstOrCreate(
            ['email' => 'mjrrdnasm@gmail.com'],
            [
                'name' => 'Organizer User',
                'password' => Hash::make('password'),
                'role' => 'ORGANIZER',
                'email_verified_at' => now(),
            ]
        );

        // Create participant user
        User::firstOrCreate(
            ['email' => 'mjrrdn2@gmail.com'],
            [
                'name' => 'Participant User',
                'password' => Hash::make('password'),
                'role' => 'PARTICIPANT',
                'email_verified_at' => now(),
            ]
        );

        // Create additional test users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'PARTICIPANT',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'role' => 'PARTICIPANT',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'role' => 'ORGANIZER',
            ],
            [
                'name' => 'Alice Williams',
                'email' => 'alice@example.com',
                'role' => 'PARTICIPANT',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'role' => 'PARTICIPANT',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
