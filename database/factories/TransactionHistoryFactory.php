<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TransactionHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TransactionHistory>
 */
final class TransactionHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionHistory::class;

    public function definition(): array
    {
        return [];
    }

}
