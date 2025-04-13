<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::table('tasks', function (Blueprint $table) {
            // Check if column exists before trying to drop it
            if (Schema::hasColumn('tasks', 'event_id')) {
                $table->dropForeign(['event_id']);
                $table->dropColumn(['event_id']);
            }

            // Check if morphs columns don't exist before adding them
            if (!Schema::hasColumn('tasks', 'taskable_id') && !Schema::hasColumn('tasks', 'taskable_type')) {
                $table->morphs('taskable');
            }
        });

        if (Schema::hasTable('bracket_deadlines')) {
            Schema::dropIfExists('bracket_deadlines');
        }
        
        Schema::create('bracket_deadlines', function (Blueprint $table) {
            $table->id();
            $table->string('stage'); // Like 'L', 'U', 'f'
            $table->string('inner_stage'); // Like 'e1', 'e2', 'p1', etc.
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('created_at');

            $table->foreignId('event_details_id')
                ->references('id')
                ->on('event_details')
                ->onDelete('cascade');
                
            $table->unique(['event_details_id', 'stage', 'inner_stage']);
        });

        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\BracketDeadlinesSeeder',
            '--force' => true
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::table('tasks', function (Blueprint $table) {
            // Check if these columns exist before trying to drop them
            if (Schema::hasColumn('tasks', 'taskable_id')) {
                $table->dropColumn('taskable_id');
            }
            
            if (Schema::hasColumn('tasks', 'taskable_type')) {
                $table->dropColumn('taskable_type');
            }

            // Add event_id if it doesn't exist
            if (!Schema::hasColumn('tasks', 'event_id')) {
                $table->unsignedBigInteger('event_id')->nullable();
                
                // Add foreign key constraint
                try {
                    $table->foreign('event_id')
                        ->references('id')
                        ->on('event_details')
                        ->onDelete('cascade');
                } catch (\Exception $e) {
                    // If constraint addition fails, log or handle the error
                    // but continue with the migration
                }
            }
        });
        
        Schema::dropIfExists('bracket_deadlines');
        
        Schema::create('bracket_deadlines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_details_id')->unique();
            $table->json('deadlines');
            
            $table->foreign('event_details_id')
                ->references('id')
                ->on('event_details')
                ->onDelete('cascade');
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};