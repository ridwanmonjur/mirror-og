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
        if (! Schema::hasColumn('participant_payments', 'history_id')) {
            Schema::table('participant_payments', function (Blueprint $table) {
                $table->unsignedBigInteger('history_id')->nullable()->after('id');

                if (Schema::hasTable('user_wallet')) {
                    $table->foreign('history_id')->references('id')->on('transaction_history')->onDelete('cascade');
                }
            });
        }

        Schema::table('participant_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('participant_payments', 'register_time')) {
                $table->string('register_time')->nullable();
            }
        });

        Schema::table('participant_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('participant_payments', 'type')) {
                $table->string('type');
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
                if (! Schema::hasColumn('join_events', 'register_time')) {
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
        if (Schema::hasColumn('participant_payments', 'history_id')) {
            Schema::table('participant_payments', function (Blueprint $table) {
                if (Schema::hasTable('user_wallet')) {
                    $table->dropColumn('history_id');
                }

                if (Schema::hasColumn('participant_payments', 'register_time')) {
                    $table->dropColumn('register_time');
                }

                if (Schema::hasColumn('participant_payments', 'type')) {
                    $table->dropColumn('type');
                }
            });
        }

        if (Schema::hasTable('stripe_transactions')) {
            Schema::rename('stripe_transactions', 'all_payment_transactions');
        }

        if (Schema::hasTable('all_payment_transactions')) {
            Schema::table('all_payment_transactions', function (Blueprint $table) {
                if (! Schema::hasColumn('all_payment_transactions', 'coupon_amount')) {
                    $table->double('coupon_amount')->nullable();
                }

                if (! Schema::hasColumn('all_payment_transactions', 'released_amount')) {
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
