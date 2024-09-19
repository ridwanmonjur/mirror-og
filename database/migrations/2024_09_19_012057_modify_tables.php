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
        Schema::table('user_discounts', function (Blueprint $table) {
            $tableName = 'user_discounts';
            $this->dropColumnIfExists($table, $tableName, 'name');
            $this->dropColumnIfExists($table, $tableName, 'coupon');
            $this->dropColumnIfExists($table, $tableName, 'type');
        });

        Schema::table('all_payment_transactions', function (Blueprint $table) {
            $table->double('coupon_amount')->nullable();
            $table->double('released_amount')->nullable();
            if (Schema::hasColumn('all_payment_transactions', 'user_discount_id')) {
                $table->dropForeign(['user_discount_id']);
                $table->dropColumn('user_discount_id');
            }
        });
    }

    private function dropColumnIfExists(Blueprint $table, $tableName, $columnName)
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            $table->dropColumn($columnName);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_discounts', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('coupon')->nullable();
            $table->string('type')->nullable();
        });

        Schema::table('all_payment_transactions', function (Blueprint $table) {
            $tableName = 'all_payment_transactions';
            $this->dropColumnIfExists($table, $tableName, 'coupon_amount');
            $this->dropColumnIfExists($table, $tableName, 'released_amount');

            if (!Schema::hasColumn('all_payment_transactions', 'user_discount_id')) {
                $table->unsignedBigInteger('user_discount_id')->nullable();
                $table->foreign('user_discount_id')->references('id')->on('user_discounts')->onDelete('set null');
            }
        });

    }
};
