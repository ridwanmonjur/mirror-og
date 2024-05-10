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
        Schema::table('event_details', function (Blueprint $table) {
            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->unsignedBigInteger('event_tier_id')->nullable();
            $table->unsignedBigInteger('event_category_id')->nullable();
            $table->foreign('event_type_id')->references('id')->on('event_type');
            $table->foreign('event_tier_id')->references('id')->on('event_tier');
            $table->foreign('event_category_id')->references('id')->on('event_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_type_id');
            $table->dropConstrainedForeignId('event_tier_id');
            $table->dropConstrainedForeignId('event_category_id');

        });
    }
};
