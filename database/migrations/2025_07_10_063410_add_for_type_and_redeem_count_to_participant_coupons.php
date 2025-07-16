<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participant_coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('participant_coupons', 'for_type')) {
                $table->enum('for_type', ['organizer', 'participant'])->default('participant');
            }
            if (!Schema::hasColumn('participant_coupons', 'redeemable_count')) {
                $table->integer('redeemable_count')->default(10);
            }
        });

        if (Schema::hasTable('participant_coupons') && !Schema::hasTable('system_coupons')) {
            Schema::rename('participant_coupons', 'system_coupons');
        }

        $eventCreateCoupons = DB::table('event_create_coupon')->get();

        foreach ($eventCreateCoupons as $coupon) {
            DB::table('system_coupons')->insert([
                'code' => $coupon->coupon,
                'amount' => $coupon->amount,
                'description' => $coupon->name,
                'is_active' => $coupon->isEnforced ? 1 : 0,
                'is_public' => 1, // Default to public
                'expires_at' => $coupon->endDate && $coupon->endTime ? 
                    $coupon->endDate . ' ' . $coupon->endTime : 
                    ($coupon->endDate ? $coupon->endDate . ' 23:59:59' : null),
                'for_type' => 'organizer',
                'redeemable_count' => 0,
                'discount_type' => $coupon->type ?? 'percent',
            ]);
        }

      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_coupons', function (Blueprint $table) {
            if (Schema::hasColumn('participant_coupons', 'for_type')) {
                $table->dropColumn('for_type');
            }
            if (Schema::hasColumn('participant_coupons', 'redeemable_count')) {
                $table->dropColumn('redeemable_count');
            }
        });

        if (!Schema::hasTable('participant_coupons') && Schema::hasTable('system_coupons')) {
            Schema::rename('system_coupons', 'participant_coupons');
        }

        if (Schema::hasTable('system_coupons')) {
            DB::table('system_coupons')
                ->where('for_type', 'organizer')
                ->delete();
        }

      
    }
};
