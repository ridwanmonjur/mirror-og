<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('participant_payments', function (Blueprint $table) {
            // Drop foreign keys first with try-catch
            try {
                $table->dropColumn('history_id');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }
            
            try {
                $table->dropColumn('team_members_id');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }
            
            try {
                $table->dropColumn('user_id');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }
            
            // Modify columns to be nullable (using correct column type)
            if (Schema::hasColumn('participant_payments', 'team_members_id')) {
                $table->unsignedBigInteger('team_members_id')->nullable()->change();
            }
            
            if (Schema::hasColumn('participant_payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            
            // Add foreign keys back with SET NULL
            if (Schema::hasColumn('participant_payments', 'history_id')) {
                $table->foreign('history_id')
                      ->references('id')
                      ->on('transaction_history')
                      ->onDelete('set null')
                      ->onUpdate('restrict');
            }
                      
            if (Schema::hasColumn('participant_payments', 'team_members_id')) {
                $table->foreign('team_members_id')
                      ->references('id')
                      ->on('team_members')
                      ->onDelete('set null')
                      ->onUpdate('restrict');
            }
                      
            if (Schema::hasColumn('participant_payments', 'user_id')) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null')
                      ->onUpdate('restrict');
            }

            
        });

        Schema::table('user_coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('user_coupons', 'redeemable_count')) {
                $table->integer('redeemable_count')->default(0);
            }


            if (Schema::hasColumn('user_coupons', 'redeemed_at')) {
                $table->dropColumn(['redeemed_at']);
            }
        });

        Schema::table('user_coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('user_coupons', 'redeemed_at')) {
                $table->date('redeemed_at')->nullable(true);
            }
        });
    }

    public function down()
    {
        Schema::table('participant_payments', function (Blueprint $table) {
            // Drop foreign keys first
            try {
                $table->dropForeign(['history_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropForeign(['team_members_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {}
            
            // Revert column changes
            if (Schema::hasColumn('participant_payments', 'team_members_id')) {
                $table->unsignedBigInteger('team_members_id')->nullable()->change();
            }
            
            if (Schema::hasColumn('participant_payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            
            // Recreate original foreign keys with CASCADE
            if (Schema::hasColumn('participant_payments', 'history_id')) {
                $table->foreign('history_id')
                      ->references('id')
                      ->on('transaction_history')
                      ->onDelete('cascade')
                      ->onUpdate('restrict');
            }
                      
            if (Schema::hasColumn('participant_payments', 'team_members_id')) {
                $table->foreign('team_members_id')
                      ->references('id')
                      ->on('team_members')
                      ->onDelete('cascade')
                      ->onUpdate('restrict');
            }
                      
            if (Schema::hasColumn('participant_payments', 'user_id')) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade')
                      ->onUpdate('restrict');
            }
        });
        
        Schema::table('user_coupons', function (Blueprint $table) {
            if (Schema::hasColumn('user_coupons', 'redeemable_count')) {
                $table->dropColumn('redeemable_count');
            }

            if (!Schema::hasColumn('user_coupons', 'redeemed_at')) {
                $table->dropColumn(['redeemed_at']);
            }
        });

        Schema::table('user_coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('user_coupons', 'redeemed_at')) {
                $table->date('redeemed_at')->nullable(true);
            }
        });
    }
};