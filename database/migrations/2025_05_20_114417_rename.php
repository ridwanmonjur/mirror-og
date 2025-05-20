<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('user_discounts', 'user_wallet');

        Schema::table('user_wallet', function (Blueprint $table) {
            if (Schema::hasColumn('user_wallet', 'amount')) {
                $table->renameColumn('amount', 'usable_balance');
            }

            if (!Schema::hasColumn('user_wallet', 'current_balance')) {
                $table->decimal('current_balance', 10, 2)->default(0);
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
