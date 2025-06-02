<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('wallet_topups')) {
            Schema::create('wallet_topups', function (Blueprint $table) {
                $table->id();

                if (!Schema::hasColumn('wallet_topups', 'amount')) {
                    $table->decimal('amount', 10, 2); 
                }

                if (!Schema::hasColumn('wallet_topups', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }

                if (!Schema::hasColumn('wallet_topups', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('wallet_topups')) {
            Schema::dropIfExists('wallet_topups');
        }
    }
};
