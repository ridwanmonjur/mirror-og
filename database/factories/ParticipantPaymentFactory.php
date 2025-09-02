<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ParticipantPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ParticipantPayment>
 */
final class ParticipantPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ParticipantPayment::class;

    public function definition(): array
    {
        return [];
    }

}
