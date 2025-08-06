<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\BracketDeadlineSetup;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('matches')) {
            Schema::rename('matches', 'brackets');
        }

        if (! Schema::hasTable('bracket_deadline_setup')) {

            Schema::create('bracket_deadline_setup', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tier_id')
                    // ->unique()
                    ;
                $table->json('deadline_config');
                $table->foreign('tier_id')
                    ->references('id')
                    ->on('event_tier')
                    ->onDelete('cascade');
            });
        }

        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\BracketDeadlineSetupSeeder',
            '--force' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bracket_deadline_setup');

        if (Schema::hasTable('brackets')) {
            Schema::rename('brackets', 'matches');
        }
    }
};
