<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('users')->delete();
        // DB::table('events')->delete();
        // DB::table('participants')->delete();
        // DB::table('organizers')->delete();
        // DB::table('event_categories')->delete();
        // DB::table('event_details')->delete();

        $organizer = DB::table('users')->insertGetId([
            'name' => 'organizer',
            'email' => 'ridwanmonjur@gmail.com',
            'password' => bcrypt('123456'),
            'country_code' => '+88',
            'mobile_no' => '01952996432',
            'role' => 'ORGANIZER',
            'status' => 'CREATED',
            'email_verified_at' => now(),
        ]);
        $participant = DB::table('users')->insertGetId([
            'name' => 'participant',
            'email' => 'mjrrdnasm@gmail.com',
            'password' => bcrypt('123456'),
            'country_code' => '+88',
            'mobile_no' => '01952996432',
            'role' => 'PARTICIPANT',
            'status' => 'CREATED',
            'email_verified_at' => now(),
        ]);
        DB::table('users')->insertGetId([
            'name' => 'admin',
            'email' => 'mjrrdn@gmail.com',
            'password' => bcrypt('123456'),
            'country_code' => '+88',
            'mobile_no' => '01952996432',
            'role' => 'ADMIN',
            'status' => 'CREATED',
            'email_verified_at' => now(),
        ]);

        DB::table('organizers')->insert([
            'user_id' => $organizer,
            'created_at' => now(),
            'updated_at' => now(),
            'companyName' => 'OPEN WATERS GAMING',
            'companyDescription' => 'Gaming company',
        ]);

        DB::table('participants')->insert([
            'user_id' => $participant,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
