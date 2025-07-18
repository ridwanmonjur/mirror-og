<?php
namespace Database\Seeders;

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

       
    }
}
