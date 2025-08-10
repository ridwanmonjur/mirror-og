<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BracketDeadlineSetup;
use Database\Seeders\BracketDeadlineSetupSeeder;
use Database\Seeders\LeagueDeadlineSetupSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all existing BracketDeadlineSetup records
        BracketDeadlineSetup::truncate();
        
        // Run the seeders to repopulate with updated configurations
        $bracketSeeder = new BracketDeadlineSetupSeeder();
        $bracketSeeder->run();
        
        $leagueSeeder = new LeagueDeadlineSetupSeeder();
        $leagueSeeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse data truncation/seeding
        // This would require backing up the original data first
    }
};
