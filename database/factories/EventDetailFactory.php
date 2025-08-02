<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventCategory;
use App\Models\EventDetail;
use App\Models\EventTier;
use App\Models\EventType;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @extends Factory<\App\Models\EventDetail>
 */
final class EventDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\EventDetail::class;

    public static function deleteRelatedTables()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables
        \App\Models\EventDetail::query()->delete();
        \App\Models\RecordStripe::query()->delete();
        \App\Models\EventType::query()->delete();
        \App\Models\EventTier::query()->delete();
        \App\Models\EventCategory::query()->delete();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'eventDefinitions' => fake()->optional()->word,
            'eventName' => fake()->optional()->word,
            'startDate' => fake()->optional()->date(),
            'endDate' => fake()->optional()->date(),
            'startTime' => fake()->optional()->time(),
            'endTime' => fake()->optional()->time(),
            'eventDescription' => fake()->optional()->text,
            'eventBanner' => fake()->optional()->word,
            'eventTags' => fake()->optional()->word,
            'status' => fake()->optional()->word,
            'venue' => fake()->optional()->word,
            'sub_action_public_date' => fake()->optional()->word,
            'sub_action_public_time' => fake()->optional()->word,
            'sub_action_private' => fake()->optional()->word,
            'user_id' => \App\Models\User::factory(),
            'event_type_id' => \App\Models\EventType::factory(),
            'event_tier_id' => \App\Models\EventTier::factory(),
            'event_category_id' => \App\Models\EventCategory::factory(),
            'payment_transaction_id' => \App\Models\OrganizerPayment::factory(),
            'willNotify' => fake()->randomNumber(1),
        ];
    }

    /**
     * Run the database seeds.
     */
    public function seed($eventIndex, $options = [
        'eventTier' => 'Dolphin',
        'eventName' => 'Test Brackets',
        'eventType' => 'Tournament'
    ])
    {
        // dd($options);
        $user = User::updateOrCreate([
            'email' => 'org1@driftwood.gg',
        ], [
            'name' => 'Org1',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'remember_token' => \Illuminate\Support\Str::random(10),
            'status' => null,
            'role' => 'ORGANIZER',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Organizer::updateOrCreate([
            'user_id' => $user->id,
        ],
            [
                'companyName' => 'Company X',
                'companyDescription' => 'Company X Desc',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $eventCategory = EventCategory::where('gameTitle', 'Dota 2')->first();
        if (! $eventCategory) {
            $eventCategory = EventCategory::create([
                'gameTitle' => 'Dota 2',
                'gameIcon' => 'images/event_details/dota2.png',
                'eventDefinitions' => 'Dota 2 is a 2013 multiplayer online battle arena video game by Valve. The game is a sequel to Defense of the Ancients, a community-created mod for Blizzard Entertainment\'s Warcraft III: Reign of Chaos.',
                'user_id' => $user->id,
            ]);
        }

        $eventTiers = [
            [
                'eventTier' => 'Starfish',
                'tierIcon' => 'images/event_details/starfish.png',
                'tierTeamSlot' => '8',
                'tierPrizePool' => '5000',
                'tierEntryFee' => '10',
                'user_id' => $user->id,
            ],
            [
                'eventTier' => 'Turtle',
                'tierIcon' => 'images/event_details/turtle.png',
                'tierTeamSlot' => '16',
                'tierPrizePool' => '10000',
                'tierEntryFee' => '20',
                'user_id' => $user->id,
            ],
            [
                'eventTier' => 'Dolphin',
                'tierIcon' => 'images/event_details/dolphin.png',
                'tierTeamSlot' => '32',
                'tierPrizePool' => '15000',
                'tierEntryFee' => '30',
                'user_id' => $user->id,
            ],
        ];

        foreach ($eventTiers as $tierData) {
            $existingTier = EventTier::where('eventTier', $tierData['eventTier'])->first();
            if (! $existingTier) {
                EventTier::create($tierData);
            }
        }

        $eventTypes = [
            [
                'eventType' => 'Tournament',
                'eventDefinitions' => 'Competitive gaming event with multiple rounds and elimination',
            ],
            [
                'eventType' => 'League',
                'eventDefinitions' => 'Regular season format with standings and playoffs',
            ],
        ];

        foreach ($eventTypes as $typeData) {
            $existingType = EventType::where('eventType', $typeData['eventType'])->first();
            if (! $existingType) {
                EventType::create($typeData);
            }
        }

        Log::info($eventTypes);

        return $this->createSampleEvents($user, $options['eventTier'], $options['eventType'], $eventIndex,
            $options['eventName']);
    }

    private function createSampleEvents($user, $eventTier, $eventType, $eventIndex, $eventName)
    {
        $category = EventCategory::where('gameTitle', 'Dota 2')->first();
        $tier = EventTier::where('eventTier', $eventTier)->first();
        $type = EventType::where('eventType', $eventType)->first();
        // dd($type);
        if (! $category || ! $eventTier || ! $eventType) {
            return;
        }

        for ($i = 0; $i < 2; $i++) {

            $startDate = fake()->dateTimeBetween('now', '+2 days')->format('Y-m-d');
            $endDate = date('Y-m-d', strtotime($startDate.' +2 days'));

            $paymentTransaction = \App\Models\OrganizerPayment::factory()->create();

            $event = EventDetail::updateOrCreate([
                'eventName' => $eventName,
            ], [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'startTime' => fake()->time('H:i:s'),
                'endTime' => fake()->time('H:i:s'),
                'eventDescription' => 'Join us for this exciting event!',
                'eventBanner' => 'images/event_details/banner1.png',
                'eventTags' => $category->gameTitle.',esports,gaming,competition',
                'status' => 'SCHEDULED',
                'sub_action_private' => 'public',
                'venue' => 'MY',
                'user_id' => $user->id,
                'event_type_id' => $type->id,
                'event_tier_id' => $tier->id,
                'event_category_id' => $category->id,
                'payment_transaction_id' => $paymentTransaction->id,
                'willNotify' => fake()->numberBetween(0, 1),
            ]);

            $event->createRegistrationTask();
            $event->createStatusUpdateTask();
            $event->createDeadlinesTask();
        }

        Log::info($user);

        return [
            'organizer' => $user,
            'event' => $event,
        ];
    }
}
