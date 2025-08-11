<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BracketDeadlineSetup;
use App\Models\EventTier;
use App\Models\EventType;

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
                    'e4' => ['start' => 9, 'end'=> 10],
                    'p0' => ['start' => 11, 'end'=> 12],
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
                    'W' => ['start' => 23, 'end'=> 24],
                ],
            ],
            16 => [
                'U' => [
                    'e1' => ['start' => 0, 'end'=> 3],
                    'e2' => ['start' => 4, 'end'=> 6],
                    'e3' => ['start' => 7, 'end'=> 8],
                    'e5' => ['start' => 8, 'end'=> 9],
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
                    'W' => ['start' => 19, 'end'=> 20],
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
                    'W' => ['start' => 15, 'end'=> 16],
                ],
            ],
        ];

        $eventTiers = EventTier::whereIn('tierTeamSlot', array_keys($configurations))->get();
        $tournamentType = EventType::where('eventType', 'Tournament')->first();
        
        if (!$tournamentType) {
            $this->command->error('Tournament event type not found. Please seed event types first.');
            return;
        }

        foreach ($eventTiers as $eventTier) {
            $slotSize = $eventTier->tierTeamSlot;
            if (isset($configurations[$slotSize])) {
                BracketDeadlineSetup::updateOrCreate(
                    [
                        'tier_id' => $eventTier->id,
                        'type_id' => $tournamentType->id
                    ],
                    ['deadline_config' => $configurations[$slotSize]]
                );
            }
        }

        
        foreach ($configurations as $slotSize => $config) {
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
                    [
                        'tier_id' => $slotSize, 
                        'type_id' => $tournamentType->id
                    ],
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
