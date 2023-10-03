<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        DB::table('participants')->delete();
        DB::table('organizer')->delete();


        DB::table('users')->insert([
            'name' => 'ridwan',
            'email' => "ridwanmonjur@gmail.com",
            'password' => bcrypt("123456"),
            'country_code' => "+88",
            'mobile_no' => "01952996432",
            'role' => "PARTICIPANT",
            'status' => "CREATED",
        ]);
        DB::table('users')->insert([
            'name' => 'ariful',
            'email' => "abdullahnafis20@gmail.com",
            'password' => bcrypt("123456"),
            'country_code' => "+88",
            'mobile_no' => "01952996432",
            'role' => "PARTICIPANT",
            'status' => "CREATED",
        ]);
        DB::table('users')->insert([
            'name' => 'ariful',
            'email' => "arifulatwork@gmail.com",
            'password' => bcrypt("123456"),
            'country_code' => "+88",
            'mobile_no' => "01952996432",
            'role' => "PARTICIPANT",
            'status' => "CREATED",
        ]);
        // +60 11-3765 5273
        // +880 1914-141123


    }
}
