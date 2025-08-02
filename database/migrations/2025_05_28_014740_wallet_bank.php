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

        if (Schema::hasTable('user_wallet')) {

            Schema::table('user_wallet', function (Blueprint $table) {
                // Add new Malaysian bank fields
                if (! Schema::hasColumn('user_wallet', 'account_number')) {
                    $table->string('account_number', 20)->nullable()->after('bank_name');
                }

                if (! Schema::hasColumn('user_wallet', 'account_holder_name')) {
                    $table->string('account_holder_name', 100)->nullable()->after('account_number');
                }

                if (! Schema::hasColumn('user_wallet', 'bank_details_updated_at')) {
                    $table->timestamp('bank_details_updated_at')->nullable()->after('last_payout_at');
                }

                // Modify existing bank_name field to accommodate longer bank names
                $table->string('bank_name', 100)->nullable()->change();

                // Remove Stripe-related columns if they exist
                if (Schema::hasColumn('user_wallet', 'stripe_customer_id')) {
                    $table->dropColumn('stripe_customer_id');
                }

                if (Schema::hasColumn('user_wallet', 'payment_method_id')) {
                    $table->dropColumn('payment_method_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_wallet')) {

            Schema::table('user_wallet', function (Blueprint $table) {
                // Remove the new columns
                if (
                    Schema::hasColumn('user_wallet', 'account_number')
                    && Schema::hasColumn('user_wallet', 'account_holder_name')
                    && Schema::hasColumn('user_wallet', 'bank_details_updated_at')
                ) {
                    $table->dropColumn([
                        'account_number',
                        'account_holder_name',
                        'bank_details_updated_at',
                    ]);
                }

                // Restore Stripe columns if needed
                if (! Schema::hasColumn('user_wallet', 'stripe_customer_id')) {
                    $table->string('stripe_customer_id')->nullable();
                }

                if (! Schema::hasColumn('user_wallet', 'payment_method_id')) {
                    $table->string('payment_method_id')->nullable();
                }

            });
        }
    }
};
