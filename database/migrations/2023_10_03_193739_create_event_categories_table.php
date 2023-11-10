<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('gameTitle')->nullable();
            $table->string('gameIcon')->nullable();
            $table->string('eventType')->nullable();
            $table->string('eventDefinitions')->nullable();
            $table->string('eventTier')->nullable();
            $table->string('tierIcon')->nullable();
            $table->foreignId('user_id')->constrained(
            table: 'users', indexName: 'event_categories_user_id_foreign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('event_categories', function (Blueprint $table) {
        //     $table->dropForeign(['event_id']);
        // });
        Schema::dropIfExists('event_categories');
    }
};
