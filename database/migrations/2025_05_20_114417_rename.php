<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_discounts')) {
            Schema::rename('user_discounts', 'user_wallet');
        }

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
            
            if (!Schema::hasColumn('user_wallet', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable();
            }

            if (!Schema::hasColumn('user_wallet', 'payment_method_id')) {
                $table->string('payment_method_id')->nullable();
            }
            
            if (!Schema::hasColumn('user_wallet', 'has_bank_account')) {
                $table->boolean('has_bank_account')->default(false);
            }
            
            if (!Schema::hasColumn('user_wallet', 'bank_last4')) {
                $table->string('bank_last4')->nullable();
            }
            
            if (!Schema::hasColumn('user_wallet', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            
            
            if (!Schema::hasColumn('user_wallet', 'last_payout_at')) {
                $table->timestamp('last_payout_at')->nullable();
            }
            
            if (Schema::hasColumn('user_wallet', 'stripe_connect_id')) {
                $table->dropColumn('stripe_connect_id');
            }
            
            if (Schema::hasColumn('user_wallet', 'payouts_enabled')) {
                $table->dropColumn('payouts_enabled');
            }
            
            if (Schema::hasColumn('user_wallet', 'details_submitted')) {
                $table->dropColumn('details_submitted');
            }
            
            if (Schema::hasColumn('user_wallet', 'charges_enabled')) {
                $table->dropColumn('charges_enabled');
            }
        });

        if (!Schema::hasTable('participant_coupons')) {
            Schema::create('participant_coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasTable('user_coupons')) {
            Schema::create('user_coupons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
                $table->timestamp('redeemed_at');
                $table->timestamps();
                
                $table->unique(['user_id', 'coupon_id']);
            });
        }
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
            
            if (Schema::hasColumn('user_wallet', 'stripe_customer_id')) {
                $table->dropColumn('stripe_customer_id');
            }
            
            if (Schema::hasColumn('user_wallet', 'payment_method_id')) {
                $table->dropColumn('payment_method_id');
            }
            
            if 
            (Schema::hasColumn('user_wallet', 'has_bank_account')) {
                $table->dropColumn('has_bank_account');
            }
            
            if (Schema::hasColumn('user_wallet', 'bank_last4')) {
                $table->dropColumn('bank_last4');
            }
            
            if (Schema::hasColumn('user_wallet', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            
            if (Schema::hasColumn('user_wallet', 'last_payout_at')) {
                $table->dropColumn('last_payout_at');
            }
        });

        if (Schema::hasTable('user_wallet')) {
            Schema::rename('user_wallet', 'user_discounts');
        }

        if (Schema::hasTable('participant_coupons')) {
            Schema::drop('participant_coupons');
        }

        if (Schema::hasTable('user_coupons')) {
            Schema::drop('user_coupons');
        }
    }
};