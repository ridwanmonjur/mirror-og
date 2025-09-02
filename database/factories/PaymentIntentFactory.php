<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PaymentIntent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PaymentIntent>
 */
final class PaymentIntentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentIntent::class;

    public function definition(): array
    {
        return [];
    }

}
