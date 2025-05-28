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
        Schema::table('user_wallet', function (Blueprint $table) {
            // Add new Malaysian bank fields
            $table->string('account_number', 20)->nullable()->after('bank_name');
            $table->string('account_holder_name', 100)->nullable()->after('account_number');
            $table->timestamp('bank_details_updated_at')->nullable()->after('last_payout_at');
            
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_wallet', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn([
                'account_number',
                'account_holder_name', 
                'bank_details_updated_at'
            ]);
            
            // Restore Stripe columns if needed
            $table->string('stripe_customer_id')->nullable();
            $table->string('payment_method_id')->nullable();
            
        });
    }
};