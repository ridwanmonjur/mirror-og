<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotifcationsUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\NotifcationsUser>
 */
final class NotifcationsUserFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = NotifcationsUser::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'type' => fake()->word,
            'icon_type' => fake()->optional()->word,
            'img_src' => fake()->optional()->word,
            'html' => fake()->text,
            'link' => fake()->optional()->url,
            'is_read' => fake()->randomNumber(1),
        ];
    }
}
