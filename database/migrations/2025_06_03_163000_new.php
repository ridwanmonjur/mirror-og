<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('event_tier') && ! Schema::hasColumn('event_tier', 'earlyEntryFee')) {
            Schema::table('event_tier', function ($table) {
                $table->string('earlyEntryFee')->after('tierEntryFee');
            });
        }

        if (Schema::hasTable('event_tier') && Schema::hasColumn('event_tier', 'earlyEntryFee')) {
            Schema::table('event_tier', function ($table) {
                DB::table('event_tier')->update([
                    'earlyEntryFee' => 25,
                ]);
            });
        }

        if (Schema::hasTable('event_tier_type_signup_dates')) {
            DB::table('event_tier_type_signup_dates')->update([
                'signup_open' => 800,
                'signup_close' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('event_tier') && Schema::hasColumn('event_tier', 'earlyEntryFee')) {
            Schema::table('event_tier', function ($table) {
                $table->dropColumn('earlyEntryFee');
            });
        }
    }
};
