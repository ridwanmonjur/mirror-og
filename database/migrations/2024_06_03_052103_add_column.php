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
            $table->dropColumn('isEdited');
            $table->dropColumn('country');
        });

        Schema::table('participants', function ($table) {
            $table->dropColumn('avatar');
            $table->string('region_name');
            $table->string('region_flag');
        });

        Schema::table('teams', function ($table) {
            $table->string('country_name');
            $table->string('country_flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->boolean('isEdited');
            $table->string('country');
        });

        Schema::table('participants', function ($table) {
            $table->string('avatar')->nullable(); 
            $table->dropColumn(['region_name', 'region_flag']);
        });
    
        Schema::table('teams', function ($table) {
            $table->dropColumn(['country_name', 'country_flag']);
        });
    }
    
};
