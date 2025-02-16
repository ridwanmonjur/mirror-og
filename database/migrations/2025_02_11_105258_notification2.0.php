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
        if (! Schema::hasTable('notification_counters')) {
            Schema::create('notification_counters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('social_count')->default(0);
                $table->integer('teams_count')->default(0);
                $table->integer('event_count')->default(0);
                $table->timestamps();

                $table->unique('user_id');
            });
        }

        if (! Schema::hasTable('notifications2')) {
            Schema::create('notifications2', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type');  // e.g. 'social'
                $table->string('icon_type')->nullable();  // e.g. 'friend'
                $table->string('img_src')->nullable();  // e.g. 'friend'
                $table->text('html');  // Store the HTML content
                $table->string('link')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();  // This will create both created_at and updated_at
                
                // Add indexes for common queries
                $table->index(['user_id', 'is_read']);
                $table->index(['created_at']);
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_counters');
        Schema::dropIfExists('notifications2');

    }
};
