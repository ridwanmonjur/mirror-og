<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->text('data');
                $table->morphs('notifiable');
                $table->string('type');
                $table->string('image')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->nullableMorphs('object');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('notifications');
    }
};
