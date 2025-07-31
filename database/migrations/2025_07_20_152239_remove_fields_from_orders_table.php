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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'billing_address')) {
                $table->dropColumn('billing_address');
            }
            if (Schema::hasColumn('orders', 'billing_email')) {
                $table->dropColumn('billing_email');
            }
            if (Schema::hasColumn('orders', 'billing_name')) {
                $table->dropColumn('billing_name');
            }
            if (Schema::hasColumn('orders', 'billing_phone')) {
                $table->dropColumn('billing_phone');
            }
            if (Schema::hasColumn('orders', 'billing_name_on_card')) {
                $table->dropColumn('billing_name_on_card');
            }
            if (Schema::hasColumn('orders', 'billing_tax')) {
                $table->dropColumn('billing_tax');
            }
            if (Schema::hasColumn('orders', 'billing_city')) {
                $table->dropColumn('billing_city');
            }
            if (Schema::hasColumn('orders', 'billing_province')) {
                $table->dropColumn('billing_province');
            }
            if (Schema::hasColumn('orders', 'billing_postalcode')) {
                $table->dropColumn('billing_postalcode');
            }
            if (Schema::hasColumn('orders', 'payment_gateway')) {
                $table->dropColumn('payment_gateway');
            }
            if (Schema::hasColumn('orders', 'error')) {
                $table->dropColumn('error');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('billing_email')->nullable();
            $table->string('billing_name')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_name_on_card')->nullable();
            $table->integer('billing_tax');
            $table->string('billing_city')->nullable();
            $table->string('billing_province')->nullable();
            $table->string('billing_postalcode')->nullable();
            $table->string('payment_gateway')->default('stripe');
            $table->string('error')->nullable();
        });
    }
};
