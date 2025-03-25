<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_address')) {
            if (Schema::hasColumn('user_address', 'addressLine2')) {
                Schema::table('user_address', function (Blueprint $table) {
                    $table->string('addressLine2')->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_address')) {
            if (Schema::hasColumn('user_address', 'addressLine2')) {
                Schema::table('user_address', function (Blueprint $table) {
                    $table->string('addressLine2')->nullable(false)->change();
                });
            }
        }
    }
};
