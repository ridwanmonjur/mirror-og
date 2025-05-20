<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('user_discounts', 'user_wallet');

        Schema::rename('user_discounts', 'user_wallet');


        Schema::table('user_wallet', function (Blueprint $table) {
            if (Schema::hasColumn('user_wallet', 'amount')) {
                $table->renameColumn('amount', 'usable_balance');
            }

            if (!Schema::hasColumn('user_wallet', 'current_balance')) {
                $table->decimal('current_balance', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('user_wallet', 'stripe_connect_id')) {
                $table->string('stripe_connect_id')->nullable();
            }
            
            $columnsToAdd = [
                'payouts_enabled' => 'boolean',
                'details_submitted' => 'boolean',
                'charges_enabled' => 'boolean',
                'has_bank_account' => 'boolean',
                'bank_last4' => 'string',
                'bank_name' => 'string',
                'balance' => 'decimal',
                'last_payout_at' => 'timestamp'
            ];
            
            foreach ($columnsToAdd as $column => $type) {
                if (!Schema::hasColumn('user_wallet', $column)) {
                    if ($type === 'boolean') {
                        $table->boolean($column)->default(false);
                    } elseif ($type === 'string') {
                        $table->string($column)->nullable();
                    } elseif ($type === 'decimal') {
                        $table->decimal($column, 10, 2)->default(0);
                    } elseif ($type === 'timestamp') {
                        $table->timestamp($column)->nullable();
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_wallet', function (Blueprint $table) {
            if (Schema::hasColumn('user_wallet', 'usable_balance')) {
                $table->renameColumn('usable_balance', 'amount');
            }

            if (Schema::hasColumn('user_wallet', 'current_balance')) {
                $table->dropColumn('current_balance');
            }
        });

        Schema::rename('user_wallet', 'user_discounts');
    }
};
