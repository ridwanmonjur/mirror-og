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
        Schema::table('join_events', function (Blueprint $table) {        
            $table->foreignId('joiner_id')
                ->constrained('users')
                ->nullable();
            
            $table->foreignId('joiner_participant_id')
                ->constrained('participants')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_events', function (Blueprint $table) {
            // $table->dropForeign(['joiner_id', 'joiner_participant_id']);
            // $table->dropColumn('joiner_id');
            // $table->dropColumn('joiner_participant_id');
        });
    }
};
