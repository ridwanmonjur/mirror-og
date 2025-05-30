<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('participant_payments', 'wallet_id')) {
            Schema::table('participant_payments', function (Blueprint $table) {
                $table->unsignedBigInteger('wallet_id')->nullable()->after('id');

                if (Schema::hasTable('user_wallet')) {
                    $table->foreign('wallet_id')->references('id')->on('user_wallet')->onDelete('set null');
                }

                
            });
        }

        Schema::table('participant_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('participant_payments', 'register_time')) {
                $table->string('register_time')->nullable();
            }
        });

        if (Schema::hasTable('all_payment_transactions')) {
            if (Schema::hasColumn('all_payment_transactions', 'coupon_amount')) {
                Schema::table('all_payment_transactions', function (Blueprint $table) {
                    $table->dropColumn('coupon_amount');
                });
            }

            if (Schema::hasColumn('all_payment_transactions', 'coupon_amount')) {
                Schema::table('all_payment_transactions', function (Blueprint $table) {
                    $table->dropColumn('system_discount_id');
                });
            }

            if (Schema::hasColumn('all_payment_transactions', 'released_amount')) {
                Schema::table('all_payment_transactions', function (Blueprint $table) {
                    $table->dropColumn('released_amount');
                });
            }
        }

        if (Schema::hasTable('all_payment_transactions')) {
            Schema::rename('all_payment_transactions', 'stripe_transactions');
        }

        if (Schema::hasTable('join_events')) {
            Schema::table('join_events', function (Blueprint $table) {
                if (!Schema::hasColumn('join_events', 'register_time')) {
                    $table->string('register_time')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('participant_payments', 'wallet_id')) {
            Schema::table('participant_payments', function (Blueprint $table) {
                if (Schema::hasTable('user_wallet')) {
                    $table->dropForeign(['wallet_id']);
                }

                $table->dropColumn('wallet_id');

                if (Schema::hasColumn('participant_payments', 'register_time')) {
                    $table->dropColumn('register_time');
                }
            });
        }


        if (Schema::hasTable('stripe_transactions')) {
            Schema::rename('stripe_transactions', 'all_payment_transactions');
        }

        if (Schema::hasTable('all_payment_transactions')) {
            Schema::table('all_payment_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('all_payment_transactions', 'coupon_amount')) {
                    $table->double('coupon_amount')->nullable();
                }

                if (!Schema::hasColumn('all_payment_transactions', 'released_amount')) {
                    $table->double('released_amount')->nullable();
                }
            });
        }

        if (Schema::hasTable('join_events')) {
            Schema::table('join_events', function (Blueprint $table) {
                if (Schema::hasColumn('join_events', 'register_time')) {
                    $table->dropColumn(['register_time']);
                }
            });
        }
    }
};
