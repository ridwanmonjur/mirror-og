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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable();
            $table->string('backgroundBanner')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('region')->nullable();
            $table->dropColumn('updated_at');
            $table->boolean('isEdited')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isEdited');
            $table->dropColumn('nickname');
            $table->dropColumn('backgroundBanner');
            $table->dropColumn('bio');
            $table->dropColumn('age');
            $table->dropColumn('region');
            $table->timestamp('updated_at')->nullable();
        });
    }
};
