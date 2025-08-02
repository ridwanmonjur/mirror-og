<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('event_join_results')) {
            Schema::table('event_join_results', function (Blueprint $table) {
                if (! Schema::hasColumn('event_join_results', 'played')) {
                    $table->integer('played', false, true)->length(4)->default(0);
                }
                if (! Schema::hasColumn('event_join_results', 'won')) {
                    $table->integer('won', false, true)->length(4)->default(0);
                }
                if (! Schema::hasColumn('event_join_results', 'draw')) {
                    $table->integer('draw', false, true)->length(4)->default(0);
                }
                if (! Schema::hasColumn('event_join_results', 'points')) {
                    $table->integer('points', false, true)->length(4)->default(0);
                }
                if (! Schema::hasColumn('event_join_results', 'lost')) {
                    $table->integer('lost', false, true)->length(4)->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('event_join_results')) {
            Schema::table('event_join_results', function (Blueprint $table) {
                if (Schema::hasColumn('event_join_results', 'played')) {
                    $table->dropColumn('played');
                }
                if (Schema::hasColumn('event_join_results', 'won')) {
                    $table->dropColumn('won');
                }
                if (Schema::hasColumn('event_join_results', 'draw')) {
                    $table->dropColumn('draw');
                }
                if (Schema::hasColumn('event_join_results', 'points')) {
                    $table->dropColumn('points');
                }
                if (Schema::hasColumn('event_join_results', 'lost')) {
                    $table->dropColumn('lost');
                }
            });
        }
    }
};
