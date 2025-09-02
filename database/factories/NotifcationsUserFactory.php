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

    public function definition(): array
    {
        return [];
    }

}
