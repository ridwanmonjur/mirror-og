<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        if (Schema::hasColumn('tasks', 'updated_at')) {
            $table->dropColumn('updated_at');
        }
        
        if (Schema::hasColumn('tasks', 'isExecuted')) {
            $table->dropColumn('isExecuted');
        }
    });
}

/**
 * Reverse the migrations.
 *
 * @return void
 */
public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        if (!Schema::hasColumn('tasks', 'updated_at')) {
            $table->timestamp('updated_at')->nullable();
        }
        
        if (!Schema::hasColumn('tasks', 'isExecuted')) {
            $table->boolean('isExecuted')->default(false);
        }
    });
}
};