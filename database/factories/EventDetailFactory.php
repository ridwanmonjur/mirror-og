<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'payment_transaction_id' => fake()->optional()->randomNumber(),
            'willNotify' => fake()->randomNumber(1),
        ];
    }
}
