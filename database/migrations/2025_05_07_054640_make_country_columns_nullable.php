<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCountryColumnsNullable extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('country')->nullable()->change();
            $table->string('country_name')->nullable()->change();
            $table->string('country_flag')->nullable()->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('region')->nullable()->change();
            $table->string('region_name')->nullable()->change();
            $table->string('region_flag')->nullable()->change();
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('country')->nullable(false)->change();
            $table->string('country_name')->nullable(false)->change();
            $table->string('country_flag')->nullable(false)->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('region')->nullable(false)->change();
            $table->string('region_name')->nullable(false)->change();
            $table->string('region_flag')->nullable(false)->change();
        });
    }
}