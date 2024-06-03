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
        Schema::table('users', function ($table) {
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
        Schema::table('users', function ($table) {
            $table->dropColumn('backgroundColor');
        });

        Schema::table('teams', function ($table) {
            $table->dropColumn('backgroundColor');
        });
    }
};
