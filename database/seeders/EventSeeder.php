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
        DB::table('events')->delete();
        
        DB::table('events')->insert([
            'eventName' => Str::random(10),
            'eventDescription' => "This is a test event",
            'organizerName' => "Ocean's Gaming",
            'eventBanner' => "/events/1.png",
            'eventTags' => "DOTA 2",
            'fee' => 0,
            'eventTier'=> "Turtle",
            'eventType' => 'Type A',
            'eventStatus' => 'UPCOMING',
            'eventGroupStructure' => 'ROUND ROBIN',
            'totalParticipants' => 16, 
            'registeredParticipants' => 16, 
            'region' => "SEA",
            'prize' => "Exclusive prize",
            'startDateTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'endDateTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('events')->insert([
            'eventName' => "Casual Dota Tourney for Everyone and Anyone",
            'eventDescription' => "This is a test event",
            'organizerName' => "Open water",
            'eventBanner' => "/events/1.png",
            'eventTags' => "DOTA 2",
            'fee' => 0,
            'eventTier'=> "Turtle",
            'eventType' => 'Type A',
            'eventStatus' => 'DRAFT',
            'eventGroupStructure' => 'ROUND ROBIN',
            'totalParticipants' => 16, 
            'registeredParticipants' => 0, 
            'region' => "SEA",
            'prize' => "Exclusive prize",
            'startDateTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'endDateTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('events')->insert([
            'eventName' => "The Tryhard Trials: Dota 2",
            'eventDescription' => "This is a test event",
            'organizerName' => "Open water",
            'eventBanner' => "/events/1.png",
            'eventTags' => "DOTA 2",
            'fee' => 0,
            'eventTier'=> "Dolphin",
            'eventType' => 'Type A',
            'eventStatus' => 'ONGOING',
            'eventGroupStructure' => 'ROUND ROBIN',
            'totalParticipants' => 16, 
            'registeredParticipants' => 9, 
            'region' => "SEA",
            'prize' => "Exclusive prize",
            'startDateTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'endDateTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('events')->insert([
            'eventName' => "The Oceans Gaming Dota Collegiate League: Season 1",
            'eventDescription' => "This is a test event",
            'organizerName' => "Open water",
            'eventBanner' => "/events/1.png",
            'eventTags' => "DOTA 2",
            'fee' => 0,
            'eventTier'=> "Mermaid",
            'eventType' => 'Type A',
            'eventStatus' => 'ENDED',
            'eventGroupStructure' => 'ROUND ROBIN',
            'totalParticipants' => 16, 
            'registeredParticipants' => 7, 
            'region' => "SEA",
            'prize' => "Exclusive prize",
            'startDateTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'endDateTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('events')->insert([
            'eventName' => "The Oceans Gaming Dota Collegiate League: Season 2",
            'eventDescription' => "This is a test event",
            'organizerName' => "Open water",
            'eventBanner' => "/events/1.png",
            'eventTags' => "DOTA 2",
            'fee' => 0,
            'eventTier'=> "Starfish",
            'eventType' => 'Type A',
            'eventStatus' => 'ENDED',
            'eventGroupStructure' => 'ROUND ROBIN',
            'totalParticipants' => 16, 
            'registeredParticipants' => 7, 
            'region' => "SEA",
            'prize' => "Exclusive prize",
            'startDateTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'endDateTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        
    }
}
