<?php

namespace Database\Seeders;

use App\Models\Organizer;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Timebox;
use Faker\Factory as Faker;

use function PHPUnit\Framework\isNull;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = null;
        if (isNull($userId)){
            $user = User::where('email', 'ridwanmonjur@gmail.com')->first();
            if (!$user){
                throw new Exception("User not found! Seed user class first");
            }
            $userId = $user->id;
        } 

        $faker = Faker::create();

        $eventsArray = [
            DB::table('events')->insertGetId([
                'name' => 'Event 1',
                'status' => 'UPCOMING',
                'venue' => 'SEA',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $userId,
            ]),
            DB::table('events')->insertGetId([
                'name' => 'Event 2',
                'status' => 'DRAFT',
                'venue' => 'SEA',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $userId,
            ]),
            DB::table('events')->insertGetId([
                'name' => 'Event 3',
                'status' => 'ONGOING',
                'venue' => 'SEA',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $userId,
            ]),
            DB::table('events')->insertGetId([
                'name' => 'Event 4',
                'status' => 'ENDED',
                'venue' => 'SEA',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $userId,
            ])
        ];

        DB::table('event_details')->insert([
            'startDateTime' => now()->toDateTime(),
            'endDateTime' => now()->addDays(2)->toDateTime(),
            'eventDescription' => $faker->sentence(),
            'eventTags' => 'gaming,esports,dota2',
            'eventBanner' => '/events/1.png',
            'event_id' => $eventsArray[0],
        ]);
        DB::table('event_categories')->insert([
            'gameTitle' => $faker->sentence(),
            'gameIcon' => now()->addDays(2)->toDateTime(),
            'eventType' => "Type A",
            'tierIcon' => 'gaming,esports,dota2',
            'eventTier' => "Turtle",
            'event_id' => $eventsArray[0],
        ]);

        DB::table('event_details')->insert([
            'startDateTime' => now()->toDateTime(),
            'endDateTime' => now()->addDays(2)->toDateTime(),
            'eventDescription' => $faker->sentence(),
            'eventTags' => 'gaming,esports,dota2',
            'eventBanner' => '/events/1.png',
            'event_id' => $eventsArray[1],
        ]);
        DB::table('event_categories')->insert([
            'gameTitle' => $faker->sentence(),
            'gameIcon' => now()->addDays(2)->toDateTime(),
            'eventType' => "Type A",
            'tierIcon' => 'gaming,esports,dota2',
            'eventTier' => "Mermaid",
            'event_id' => $eventsArray[1],
        ]);

        DB::table('event_details')->insert([
            'startDateTime' => now()->toDateTime(),
            'endDateTime' => now()->addDays(2)->toDateTime(),
            'eventDescription' => $faker->sentence(),
            'eventTags' => 'gaming,esports,dota2',
            'eventBanner' => '/events/1.png',
            'event_id' => $eventsArray[2],
        ]);
        DB::table('event_categories')->insert([
            'gameTitle' => $faker->sentence(),
            'gameIcon' => now()->addDays(2)->addHours(3)->toDateTime(),
            'eventType' => "Type C",
            'tierIcon' => 'gaming,esports,dota2',
            'eventTier' => "Dolphin",
            'event_id' => $eventsArray[2],
        ]);

        DB::table('event_details')->insert([
            'startDateTime' => now()->toDateTime(),
            'endDateTime' => now()->addDays(3)->addHours(4)->toDateTime(),
            'eventDescription' => $faker->sentence(),
            'eventTags' => 'gaming,esports,dota2',
            'eventBanner' => '/events/1.png',
            'event_id' => $eventsArray[3],
        ]);
        DB::table('event_categories')->insert([
            'gameTitle' => $faker->sentence(),
            'gameIcon' => now()->addDays(2)->toDateTime(),
            'eventType' => "Type C",
            'tierIcon' => 'gaming,esports,dota2',
            'eventTier' => "Starfish",
            'event_id' => $eventsArray[3],
        ]);


        // Seed event category and establish the one-to-one relationship
        // DB::table('event_categories')->insert([
        //     'event_id' => $events[0], // Use the ID of the related event
        //     'name' => 'Category 1',
        //     // Add more category data as needed
        // ]);

        // DB::table('event_details')->insert([
        //     'organizerName' => "Ocean's Gaming",
        //     'fee' => 0,
        //     'eventGroupStructure' => 'ROUND ROBIN',
        //     'totalParticipants' => 16, 
        //     'registeredParticipants' => 16, 
        //     'prize' => "Exclusive prize",
        // ]);

    }
}