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
        
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->text('data');
            $table->morphs('notifiable'); 
            $table->string('type');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
