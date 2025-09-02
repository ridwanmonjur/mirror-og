<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ActivityLogs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ActivityLogs>
 */
final class ActivityLogsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityLogs::class;

    public function definition(): array
    {
        return [];
    }

}
