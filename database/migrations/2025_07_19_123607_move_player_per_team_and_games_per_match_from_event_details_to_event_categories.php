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
        // Add columns to event_categories table
        if (Schema::hasTable('event_categories')) {
            Schema::table('event_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('event_categories', 'player_per_team')) {
                    $table->integer('player_per_team')->default(5)->after('id');
                }
                if (!Schema::hasColumn('event_categories', 'games_per_match')) {
                    $table->integer('games_per_match')->default(3)->after('player_per_team');
                }
            });
        }

        // Remove columns from event_details table
        if (Schema::hasTable('event_details')) {
            Schema::table('event_details', function (Blueprint $table) {
                if (Schema::hasColumn('event_details', 'player_per_team')) {
                    $table->dropColumn('player_per_team');
                }
                if (Schema::hasColumn('event_details', 'games_per_match')) {
                    $table->dropColumn('games_per_match');
                }
            });
        }

        if (Schema::hasTable('systems_coupon')) {
            Schema::table('systems_coupon', function (Blueprint $table) {
                if (Schema::hasColumn('systems_coupon', 'redeemable_count')) {
                    $table->dropColumn('redeemable_count');
                }

                if (Schema::hasColumn('systems_coupon', column: 'type')) {
                    $table->dropColumn('type');
                }
            });
        }

        // Add redeemable_count column to systems_coupon table
        if (Schema::hasTable('systems_coupon')) {
            Schema::table('systems_coupon', function (Blueprint $table) {
                if (!Schema::hasColumn('systems_coupon', 'redeemable_count')) {
                    $table->integer('redeemable_count')->default(1);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add columns back to event_details table
        if (Schema::hasTable('event_details')) {
            Schema::table('event_details', function (Blueprint $table) {
                if (!Schema::hasColumn('event_details', 'player_per_team')) {
                    $table->integer('player_per_team')->default(5)->after('willNotify');
                }
                if (!Schema::hasColumn('event_details', 'games_per_match')) {
                    $table->integer('games_per_match')->default(3)->after('player_per_team');
                }
            });
        }

        // Remove columns from event_categories table
        if (Schema::hasTable('event_categories')) {
            Schema::table('event_categories', function (Blueprint $table) {
                if (Schema::hasColumn('event_categories', 'player_per_team')) {
                    $table->dropColumn('player_per_team');
                }
                if (Schema::hasColumn('event_categories', 'games_per_match')) {
                    $table->dropColumn('games_per_match');
                }
            });
        }

        // Remove redeemable_count column from systems_coupon table
        if (Schema::hasTable('systems_coupon')) {
            Schema::table('systems_coupon', function (Blueprint $table) {
                if (Schema::hasColumn('systems_coupon', 'redeemable_count')) {
                    $table->dropColumn('redeemable_count');
                }
            });
        }
    }
};
