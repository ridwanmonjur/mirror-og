<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('payment_transactions') || Schema::hasTable('all_payment_details')) {
            $tableName = Schema::hasTable('payment_transactions') ? 'payment_transactions' : 'all_payment_details';
            
            Schema::rename($tableName, 'all_payment_transactions');
            
            Schema::table('all_payment_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('all_payment_transactions', 'payment_request_id')) {
                    $table->dropColumn('payment_request_id');
                }
                
                if (Schema::hasColumn('all_payment_transactions', 'discount_id') && !Schema::hasColumn('all_payment_transactions', 'system_discount_id')) {
                    $table->renameColumn('discount_id', 'system_discount_id');
                }
                
                if (!Schema::hasColumn('all_payment_transactions', 'user_discount_id')) {
                    $table->unsignedBigInteger('user_discount_id')->nullable();
                    $table->foreign('user_discount_id')->references('id')->on('user_discounts');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('all_payment_transactions')) {
            Schema::table('all_payment_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('all_payment_transactions', 'user_discount_id')) {
                    $table->dropForeign(['user_discount_id']);
                    $table->dropColumn('user_discount_id');
                }
                
                if (Schema::hasColumn('all_payment_transactions', 'system_discount_id')) {
                    $table->renameColumn('system_discount_id', 'discount_id');
                }
                
                $table->unsignedBigInteger('payment_request_id')->nullable();
            });
            
            Schema::rename('all_payment_transactions', 'payment_transactions');
        }
    }
};