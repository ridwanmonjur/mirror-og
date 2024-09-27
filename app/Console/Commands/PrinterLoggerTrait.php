<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait PrinterLoggerTrait
{
    private function logEntry(Carbon $today, string $commandName): int
    {
        return DB::transaction(function () use ($commandName, $today) {
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
                    'type' => 'Daily cron check',
                    'created_at' => $today,
                    'cron_expression' => '0 0 * * *',
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

    private function logError(int $id, string $errorMsg): void
    {
        Log::error($errorMsg);
        DB::table('monitored_scheduled_task_log_items')->insert([
            'monitored_scheduled_task_id' => $id, 
            'type' => "Error", 
            'logs' => $errorMsg, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}