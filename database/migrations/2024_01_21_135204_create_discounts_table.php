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
        Schema::create('organizer_create_event_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('coupon')->nullable();
            $table->string('type')->nullable();
            $table->double('amount')->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->time('startTime')->nullable();
            $table->time('endTime')->nullable();
            $table->boolean('isEnforced')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('organizer_create_event_discounts');
        
        Schema::enableForeignKeyConstraints();

    }
};
