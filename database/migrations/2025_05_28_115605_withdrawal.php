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
        if (!Schema::hasTable('withdrawals')) {

            Schema::create('withdrawals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('withdrawal', 10, 2);
                $table->string('status')->default('pending')->enum(['pending', 'approved', 'rejected', 'completed']);
                $table->timestamp('requested_at')->nullable();
                $table->index(['user_id']);
            });

        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
