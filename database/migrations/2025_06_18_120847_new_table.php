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
        if (!Schema::hasColumns('team_profile', ['default_category_id'])) {
            Schema::table('team_profile', function (Blueprint $table) {
                $table->unsignedBigInteger('default_category_id')->nullable()->after('team_id');
                $table->text('other_categories')->nullable()->after('default_category_id');
                $table->foreign('default_category_id')->references('id')->on('event_categories')->onDelete('set null');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('team_profile')) {
            Schema::table('team_profile', function (Blueprint $table) {
                $table->dropForeign(['default_category_id']);
                $table->dropColumn(['default_category_id', 'other_categories']);
            });
        }

    }
};