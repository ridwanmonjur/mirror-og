<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizers', function ($table) {
            $table->string('backgroundBanner')->nullable();
            $table->string('backgroundColor')->nullable();
        });

        Schema::table('participants', function ($table) {
            $table->string('backgroundColor')->nullable();
        });

        Schema::table('teams', function ($table) {
            $table->string('backgroundColor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizers', function ($table) {
            $table->dropColumn(['backgroundColor', 'backgroundBanner']);
        });

        Schema::table('participants', function ($table) {
            $table->dropColumn('backgroundColor');
        });

        Schema::table('teams', function ($table) {
            $table->dropColumn('backgroundColor');
        });
    }
};
