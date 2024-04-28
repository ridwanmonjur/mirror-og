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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->enum('task_name', ['live', 'ended', 'registration_over']);
            $table->boolean('isExecuted')->default(false);
            $table->dateTime('action_time');
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('event_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
