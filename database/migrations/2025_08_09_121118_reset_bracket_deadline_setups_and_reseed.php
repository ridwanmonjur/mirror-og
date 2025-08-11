<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BracketDeadlineSetup;
use Database\Seeders\BracketDeadlineSetupSeeder;
use Database\Seeders\LeagueDeadlineSetupSeeder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BracketDeadlineSetup::truncate();
        
        $bracketSeeder = new BracketDeadlineSetupSeeder();
        $bracketSeeder->run();
        
        $leagueSeeder = new LeagueDeadlineSetupSeeder();
        $leagueSeeder->run();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
