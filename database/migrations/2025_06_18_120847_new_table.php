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
        if (Schema::hasTable('team_profile')) {
            Schema::table('team_profile', function (Blueprint $table) {
                if (Schema::hasColumns('team_profile', ['default_category_id'])) {
                    $table->dropForeign(['default_category_id']);
                }

                if (Schema::hasColumns('team_profile', ['default_category_id', 'all_categories'])) {
                    $table->dropColumn(['default_category_id', 'all_categories']);
                }
            });
        }

        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumns('teams', ['default_category_id'])) {
                $table->unsignedBigInteger('default_category_id')->nullable();
                $table->foreign('default_category_id')->references('id')->on('event_categories')->onDelete('set null');
            }

            if (!Schema::hasColumns('teams', ['all_categories'])) {
                $table->text('all_categories')->nullable();
            }

            if (!Schema::hasColumns('teams', ['status'])) {
                $table->enum('status', ['public', 'private'])->default('public');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('team_profile')) {
            Schema::table('team_profile', function (Blueprint $table) {
                if (Schema::hasColumns('team_profile', ['default_category_id'])) {
                    $table->dropForeign(['default_category_id']);
                }

                if (Schema::hasColumns('team_profile', ['default_category_id', 'all_categories'])) {
                    $table->dropColumn(['default_category_id', 'all_categories']);
                }
            });
        }

        if (Schema::hasTable('teams')) {
            Schema::table('teams', function (Blueprint $table) {
                if (Schema::hasColumns('teams', ['default_category_id'])) {
                    $table->dropForeign(['default_category_id']);
                }

                if (Schema::hasColumns('teams', ['default_category_id', 'all_categories'])) {
                    $table->dropColumn(['default_category_id', 'all_categories']);
                }

                if (Schema::hasColumns('teams', ['staus'])) {
                    $table->dropColumn(['status']);
                }
            });
        }


    }
};