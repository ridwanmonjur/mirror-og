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
            $table->string('gameTitle');
            $table->string('gameIcon');
            $table->string('eventType');
            $table->string('eventDefinitions');
            $table->string('eventTier');
            $table->string('tierIcon');
            $table->foreignId('event_id')->constrained(
                table: 'events', indexName: 'event_categories_event_id_foreign'
            )->nullable();
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
