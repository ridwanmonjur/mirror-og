<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BracketDeadlineSetup;
use App\Models\EventTier;
use App\Models\EventType;

class LeagueDeadlineSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $configurations = [
            32 => $this->generateLeagueConfiguration(32),
            16 => $this->generateLeagueConfiguration(16),
            8 => $this->generateLeagueConfiguration(8),
        ];

        $eventTiers = EventTier::whereIn('tierTeamSlot', array_keys($configurations))->get();
        $leagueType = EventType::where('eventType', 'League')->first();
        
        if (!$leagueType) {
            if ($this->command) {
                $this->command->error('League event type not found. Please seed event types first.');
            }
            return;
        }

        foreach ($eventTiers as $eventTier) {
            $slotSize = $eventTier->tierTeamSlot;
            if (isset($configurations[$slotSize])) {
                BracketDeadlineSetup::updateOrCreate(
                    [
                        'tier_id' => $eventTier->id,
                        'type_id' => $leagueType->id
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

            if (!$exists) {
                BracketDeadlineSetup::updateOrCreate(
                    [
                        'tier_id' => $slotSize, 
                        'type_id' => $leagueType->id
                    ],
                    ['deadline_config' => $config]
                );
            }
        }

        // Update existing BracketDeadlineSetup records where type_id is null
        $tournamentType = EventType::where('eventType', 'Tournament')->first();
        
        if ($tournamentType) {
            BracketDeadlineSetup::whereNull('type_id')
                ->update(['type_id' => $tournamentType->id]);
                
            if ($this->command) {
                $this->command->info('Updated BracketDeadlineSetup records with null type_id to Tournament type.');
            }
        } else {
            if ($this->command) {
                $this->command->error('Tournament event type not found. Cannot update null type_id records.');
            }
        }
    }

    /**
     * Generate league configuration for round-robin format
     * Based on LeagueDataService structure
     */
    private function generateLeagueConfiguration(int $teamNumber): array
    {
        $totalRounds = $teamNumber - 1; // Round-robin: each team plays every other team once
        $configuration = [];
        
      
        for ($round = 1; $round <= $totalRounds; $round++) {
            $roundKey = "R{$round}";
            
     
            $startDay = ($round - 1) * 2;
            $endDay = $round * 2;
            
            if ($round > $totalRounds * 0.8) {
                $endDay += 1;
            }
            
            $configuration[$roundKey] = [
                $roundKey => [
                    'start' => $startDay,
                    'end' => $endDay
                ]
            ];
        }
        
        return $configuration;
    }

    /**
     * Get all league deadline configurations
     */
    public static function getAllLeagueDeadlineConfigurations(): array
    {
        $result = [];
        $leagueType = EventType::where('eventType', 'League')->first();
        
        if (!$leagueType) {
            return [];
        }
        
        $configurations = BracketDeadlineSetup::where('type_id', $leagueType->id)->get();

        foreach ($configurations as $config) {
            $result[$config->tier_id] = $config->deadline_config;
        }

        return $result;
    }
}
