<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\EventTier;
use App\Services\BracketDataService;

return new class extends Migration
{
    public function up()
    {
        DB::table('brackets_setup')->truncate();
        
        $eventTiers = EventTier::all();
        $bracketDataService = new BracketDataService();
        
        foreach ($eventTiers as $tier) {
            $teamCount = $tier->tierTeamSlot;
            
            $bracketData = $bracketDataService->produceBrackets(
                $teamCount,
                false,
                null,
                [],
                'all'
            );
            
            foreach ($bracketData as $stageName => $stageData) {
                foreach ($stageData as $innerStageName => $matches) {
                    foreach ($matches as $index => $match) {
                        if (isset($match['team1_position']) && isset($match['team2_position'])) {
                            DB::table('brackets_setup')->insert([
                                'event_tier_id' => $tier->id,
                                'team1_position' => $match['team1_position'],
                                'team2_position' => $match['team2_position'],
                                'stage_name' => $stageName,
                                'inner_stage_name' => $innerStageName,
                                'order' => $index,
                                'winner_next_position' => $match['winner_next_position'] ?? null,
                                'loser_next_position' => $match['loser_next_position'] ?? null,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        DB::table('brackets_setup')->truncate();
    }
};