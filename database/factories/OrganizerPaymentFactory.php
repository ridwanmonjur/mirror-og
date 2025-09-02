<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrganizerPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\OrganizerPayment>
 */
final class OrganizerPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrganizerPayment::class;

    public function definition(): array
    {
        return [];
    }

}
