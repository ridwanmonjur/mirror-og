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
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('dispute_image_video');
        Schema::dropIfExists('messages');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('conversations')) {

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['request', 'accepted', 'blocked'])->default('request');
            $table->timestamps();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');
        });
    }

    if (!Schema::hasTable('messages')) {
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
        $table->text('text');
        $table->timestamp('expiry_date')->nullable();
        $table->timestamps();
        $table->foreignId('reply_id')->nullable()->constrained('messages')->onDelete('set null');
        $table->timestamp('read_at')->nullable();
    });
    }
    }
};
