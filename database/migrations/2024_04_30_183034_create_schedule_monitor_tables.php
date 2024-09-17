<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleMonitorTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('monitored_scheduled_task_log_items');
        Schema::dropIfExists('monitored_scheduled_tasks');
        Schema::create('monitored_scheduled_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique();
            $table->string('type')->nullable();
            $table->string('cron_expression');
            $table->dateTime('last_started_at')->nullable();
            $table->dateTime('last_ended_at')->nullable();

            $table->timestamps();
        });

        Schema::create('monitored_scheduled_task_log_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('monitored_scheduled_task_id');
            $table
                ->foreign('monitored_scheduled_task_id', 'fk_scheduled_task_id')
                ->references('id')
                ->on('monitored_scheduled_tasks')
                ->cascadeOnDelete();

            $table->string('type');

            $table->string('logs')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitored_scheduled_task_log_items');
        Schema::dropIfExists('monitored_scheduled_tasks');
    }
}
