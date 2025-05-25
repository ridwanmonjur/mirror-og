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
        //organizer_create_event_discounts
        // event_create_coupon
        if (Schema::hasTable('organizer_create_event_discounts')) {
            Schema::rename('organizer_create_event_discounts', 'event_create_coupon');
        }

        Schema::table('participant_coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('participant_coupons', 'is_public')) {
               $table->boolean('is_public')->default(true);
            }

            if (!Schema::hasColumn('participant_coupons', 'expires_at')) {
                $table->timestamp('expires_at')->nullable();
             }
        });

        if (!Schema::hasTable('transaction_history')) {
            Schema::create('transaction_history', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type');
                $table->string('link')->nullable();
                $table->decimal('amount');
                $table->string('summary')->nullable();
                $table->boolean('isPositive')->default(true);
                $table->timestamp('date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        if (Schema::hasTable('event_create_coupon')) {
            Schema::rename('event_create_coupon', 'organizer_create_event_discounts');
        }

        Schema::table('participant_coupons', function (Blueprint $table) {
            if (Schema::hasColumn('participant_coupons', 'is_public')) {
               $table->dropColumn('is_public');
            }

            if (Schema::hasColumn('participant_coupons', 'expires_at')) {
                $table->dropColumn('expires_at');
             }
        });

        Schema::dropIfExists('transaction_history');
    }
};
