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
            $table->enum('payment_status', ['pending', 'completed', 'waived'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_events', function (Blueprint $table) {
            if (Schema::hasColumn('join_events', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
