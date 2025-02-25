<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

trait PrinterLoggerTrait
{
    private function logEntry(
        string $commandName, 
        string $commandType,
        string $cronExpression = "0 0 * * *",
        Carbon $today, 
    ): int
    {
        return DB::transaction(function () use ($commandName, $commandType, $cronExpression, $today) {
            $record = DB::table('monitored_scheduled_tasks')
                ->where('name', $commandName)
                ->where('type', 'Daily cron check')
                ->whereDate('created_at', $today->toDateString())
                ->first();
        
            if ($record) {
                DB::table('monitored_scheduled_tasks')
                    ->where('id', $record->id)
                    ->update([
                        'last_started_at' => $today,
                        'updated_at' => $today,
                    ]);
                return $record->id;
            } else {
                return DB::table('monitored_scheduled_tasks')->insertGetId([
                    'name' => $commandName,
                    'type' => $commandName,
                    'created_at' => $today,
                    'cron_expression' => $cronExpression,
                    'last_started_at' => $today,
                    'updated_at' => $today,
                ]);
            }
        });
    }

    private function logExit(int $id, Carbon $now): void
    {
        DB::table('monitored_scheduled_tasks')
            ->where('id', $id)
            ->update([
                'last_ended_at' => $now,
                'updated_at' => $now,
            ]);
    }

    private function logError(int $id, Exception $e): void
    {
        Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        DB::table('monitored_scheduled_task_log_items')->insert([
            'monitored_scheduled_task_id' => $id, 
            'type' => "Error", 
            'logs' => $e->getMessage(), 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}