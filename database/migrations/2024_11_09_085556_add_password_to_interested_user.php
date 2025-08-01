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
        if (Schema::hasTable('interested_user')) {
            if (! Schema::hasColumn('interested_user', 'pass_text')) {
                Schema::table('interested_user', function (Blueprint $table) {
                    $table->string('pass_text')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('interested_user')) {
            if (Schema::hasColumn('interested_user', 'pass_text')) {
                Schema::table('interested_user', function (Blueprint $table) {
                    $table->dropColumn('pass_text');
                });
            }
        }
    }
};
