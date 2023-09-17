<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Timebox;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('events')->insert([
        //     'name' => Str::random(10),
        //     'email' => Str::random(10).'@gmail.com',
        //     'eventDescription' => "This is a test event",
        //     'organizerName' => "Ocean's Gaming",
        //     'eventBanner' => "/events/1.png",
        //     'eventTags' => "DOTA 2",
        //     'fee' => 0,
        //     'eventTier'=> "Turtle",
        //     'eventType' => 'Type A',
        //     'eventStatus' => 'UPCOMING',
        //     'totalParticipants' => 16, 
        //     'registeredParticipants' => 8, 
        //     'region' => "SEA",
        //     'prize' => "Exclusive prize",
        //     'startDateTime' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'endDateTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
        // ]);

        
    }
}
