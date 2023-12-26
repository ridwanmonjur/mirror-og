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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            // $table->string('teamName')->nullable();
            $table->foreignId('team_id')->constrained(
                table: 'teams', indexName: 'teams_team_id_foreign')->nullable();
            $table->foreignId('user_id')->constrained(
                table: 'users', indexName: 'members_user_id_foreign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
