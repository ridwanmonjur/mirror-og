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
        Schema::table('transaction_history', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_history', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_history', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_history', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
