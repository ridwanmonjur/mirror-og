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
        if (!Schema::hasTable('daily_command_errors')) {

        Schema::create('daily_command_errors', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->date('error_date');
            $table->integer('error_count')->default(1);
            $table->boolean('email_sent')->default(false);
            $table->timestamps();
            
            $table->unique(['class_name', 'error_date']);
            $table->index('error_date');
            $table->index('class_name');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_command_errors');
    }
};