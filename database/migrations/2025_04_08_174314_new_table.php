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
        if (Schema::hasColumn('event_details', 'eventDescription')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->text('eventDescription')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('event_details', 'eventDescription')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->string('eventDescription')->nullable()->change();
            });
        }
    }
};
