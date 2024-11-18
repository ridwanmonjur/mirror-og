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
        Schema::table('event_details', function (Blueprint $table) {
            if (Schema::hasColumn('event_details', 'eventDefinition')) {
                $table->dropColumn('eventDefinition');
            }

            if (!Schema::hasColumn('event_details', 'willNotify')) {
                $table->boolean('willNotify')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_details', function (Blueprint $table) {
            if (!Schema::hasColumn('event_details', 'eventDefinition')) {
                $table->text('eventDefinition')->nullable();
            }

            if (Schema::hasColumn('event_details', 'willNotify')) {
                $table->dropColumn('willNotify');
            }
        });
    }
};
