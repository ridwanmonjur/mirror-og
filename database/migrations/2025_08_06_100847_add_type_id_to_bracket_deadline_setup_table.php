<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       
        
        $indexes = DB::select("SHOW INDEX FROM bracket_deadline_setup WHERE Column_name = 'tier_id' AND Non_unique = 0 AND Key_name != 'PRIMARY'");
        
        if (!empty($indexes)) {
            foreach ($indexes as $index) {
                try {
                    DB::statement("ALTER TABLE bracket_deadline_setup DROP INDEX `{$index->Key_name}`");
                } catch (Exception $e) {
                }
            }
        }

        Schema::table('bracket_deadline_setup', function (Blueprint $table) {
            if (Schema::hasTable('bracket_deadline_setup') && !Schema::hasColumn('bracket_deadline_setup', 'type_id')) {
                $table->unsignedBigInteger('type_id')->nullable()->after('tier_id');
                $table->foreign('type_id')
                      ->references('id')
                      ->on('event_type')
                      ->onDelete('cascade');
            }
        });

        if (Schema::hasTable('bracket_deadline_setup') && Schema::hasColumn('bracket_deadline_setup', 'type_id')) {
            DB::table('bracket_deadline_setup')
                ->whereNull('type_id')
                ->update([
                    'type_id' => DB::table('event_type')
                        ->where('eventType', 'Tournament')
                        ->value('id')
                ]);
        }


        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\LeagueDeadlineSetupSeeder',
            '--force' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bracket_deadline_setup', function (Blueprint $table) {
            if (Schema::hasTable('bracket_deadline_setup') && Schema::hasColumn('bracket_deadline_setup', 'type_id')) {
               
                
                $table->dropForeign(['type_id']);
                $table->dropColumn(['type_id']);
            }
        });
    }
};
