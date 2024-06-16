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
        Schema::table('user_profile', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profile', 'id')) {
                $table->id();
            }

            if (!Schema::hasColumn('user_profile', 'user_id')) {
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profile', function (Blueprint $table) {
            if (Schema::hasColumn('user_profile', 'id')) {
                $table->dropColumn('id');
            }
            
            if (Schema::hasColumn('user_profile', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
