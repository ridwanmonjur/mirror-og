<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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
    protected $model = EventDetail::class;

    public static function deleteRelatedTables() {
  
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables
        \App\Models\EventDetail::query()->delete();
        \App\Models\PaymentTransaction::query()->delete();
        \App\Models\EventType::query()->delete();
        \App\Models\EventTier::query()->delete();
        \App\Models\EventCategory::query()->delete();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
    * Define the model's default state.
    *
    * @return array
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
            'payment_transaction_id' => \App\Models\PaymentTransaction::factory(),
            'willNotify' => fake()->randomNumber(1),
        ];
    }
}
