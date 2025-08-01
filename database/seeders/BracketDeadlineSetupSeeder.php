<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BracketDeadlineSetup;
use App\Models\EventTier;

class BracketDeadlineSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            32 => [
                'U' => [
                    'e1' => ['start' => 0, 'end'=> 3],
                    'e2' => ['start' => 4, 'end'=> 6],
                    'e3' => ['start' => 7, 'end'=> 8],
                    'p0' => ['start' => 9, 'end'=> 10],
                ],
                'L' => [
                    'e1' => ['start' => 4, 'end'=> 6],
                    'e2' => ['start' => 7, 'end'=> 8],
                    'e3' => ['start' => 9, 'end'=> 10],
                    'e4' => ['start' => 11, 'end'=> 12],
                    'e5' => ['start' => 13, 'end'=> 14],
                    'e6' => ['start' => 15, 'end'=> 16],
                    'p1' => ['start' => 17, 'end'=> 18],
                    'p2' => ['start' => 19, 'end'=> 20],
                ],
                'F' => [
                    'F' => ['start' => 21, 'end'=> 22],
                ],
            ],
            16 => [
                'U' => [
                    'e1' => ['start' => 0, 'end'=> 3],
                    'e2' => ['start' => 4, 'end'=> 6],
                    'e3' => ['start' => 7, 'end'=> 8],
                    'p0' => ['start' => 9, 'end'=> 10],
                ],
                'L' => [
                    'e1' => ['start' => 4, 'end'=> 6],
                    'e2' => ['start' => 7, 'end'=> 8],
                    'e3' => ['start' => 9, 'end'=> 10],
                    'e4' => ['start' => 11, 'end'=> 12],
                    'p1' => ['start' => 13, 'end'=> 14],
                    'p2' => ['start' => 15, 'end'=> 16],
                ],
                'F' => [
                    'F' => ['start' => 17, 'end'=> 18],
                ],
            ],
            8 => [
                'U' => [
                    'e1' => ['start' => 0, 'end'=> 3],
                    'e2' => ['start' => 4, 'end'=> 6],
                    'p0' => ['start' => 7, 'end'=> 8],
                ],
                'L' => [
                    'e1' => ['start' => 4, 'end'=> 6],
                    'e2' => ['start' => 7, 'end'=> 8],
                    'p1' => ['start' => 9, 'end'=> 10],
                    'p2' => ['start' => 11, 'end'=> 12],
                ],
                'F' => [
                    'F' => ['start' => 13, 'end'=> 14],
                ],
            ],
        ];

        $eventTiers = EventTier::whereIn('tierTeamSlot', array_keys($configurations))->get();

        foreach ($eventTiers as $eventTier) {
            $slotSize = $eventTier->tierTeamSlot;
            if (isset($configurations[$slotSize])) {
                BracketDeadlineSetup::updateOrCreate(
                    ['tier_id' => $eventTier->id],
                    ['deadline_config' => $configurations[$slotSize]]
                );
            }
        }

        // If we don't have any event tiers for these slot sizes yet,
        // create placeholder entries with tier_id = tierTeamSlot
        // These can be updated later when event tiers are created
        foreach ($configurations as $slotSize => $config) {
            // Check if we've already created an entry for this slot size
            $exists = false;
            foreach ($eventTiers as $eventTier) {
                if ($eventTier->tierTeamSlot == $slotSize) {
                    $exists = true;
                    break;
                }
            }

            // If not, create a placeholder
            if (! $exists) {
                BracketDeadlineSetup::updateOrCreate(
                    ['tier_id' => $slotSize], // This is just a placeholder ID
                    ['deadline_config' => $config]
                );
            }
        }
    }

    /**
     * Get all tiers and their deadline configurations
     */
    public static function getAllDeadlineConfigurations(): array
    {
        $result = [];
        $configurations = BracketDeadlineSetup::all();

        foreach ($configurations as $config) {
            $result[$config->tier_id] = $config->deadline_config;
        }

        return $result;
    }
}
