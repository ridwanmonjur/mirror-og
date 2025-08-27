<?php

namespace App\Traits;

use App\Mail\ErrorCommandMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait PrinterLoggerTrait
{
    private function logEntry(string $commandName, string $commandType, string $cronExpression, Carbon $today): int
    {
        return DB::table('monitored_scheduled_tasks')->insertGetId([
            'name' => $commandName,
            'type' => $commandType,
            'created_at' => $today,
            'cron_expression' => $cronExpression,
            'last_started_at' => $today,
            'updated_at' => $today,
        ]);
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

    private function logError(?int $id, Exception $e): void
    {
        Log::error($e->getMessage().PHP_EOL.$e->getTraceAsString());
        if ($id) {
            DB::table('monitored_scheduled_task_log_items')->insert([
                'monitored_scheduled_task_id' => $id,
                'type' => 'Error',
                'logs' => $e->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $fullClassName = get_class($this);
        $className = class_basename($fullClassName);

        Log::error("[$className] ".$e->getMessage().PHP_EOL.$e->getTraceAsString());

        try {
            $today = now()->toDateString();

            $this->trackDailyErrorAndNotify($className, $today, $e);
        } catch (Exception $dbException) {
            Log::error('Failed to log error to database: '.$dbException->getMessage());
        }
    }

    private function trackDailyErrorAndNotify(string $className, string $today, Exception $e): void
    {
        try {
            // Check if we already have an error count record for today
            $errorRecord = DB::table('daily_command_errors')->where('class_name', $className)->where('error_date', $today)->first();

            if ($errorRecord) {
                DB::table('daily_command_errors')->where('id', $errorRecord->id)->increment('error_count');

                Log::info("Error count incremented for $className on $today. Total: ".($errorRecord->error_count + 1));
            } else {
                DB::table('daily_command_errors')->insert([
                    'class_name' => $className,
                    'error_date' => $today,
                    'error_count' => 1,
                    'email_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->sendFirstErrorNotification($className, $e, $today);

                DB::table('daily_command_errors')
                    ->where('class_name', $className)
                    ->where('error_date', $today)
                    ->update(['email_sent' => true]);
            }
        } catch (Exception $trackingException) {
            Log::error('Failed to track daily error count: '.$trackingException->getMessage());
        }
    }

    private function sendFirstErrorNotification(string $className, Exception $e, string $date): void
    {
        try {
            $adminEmail = 'mjrrdn@gmail.com';

            $errorMail = new ErrorCommandMail(className: $className, errorMessage: $e->getMessage(), errorDate: $date, errorTime: now()->format('H:i:s'), stackTrace: $e->getTraceAsString());

            Mail::to($adminEmail)->send($errorMail);
        } catch (Exception $mailException) {
            Log::error('Failed to send error notification email: '.$mailException->getMessage());
        }
    }

    /**
     * Get daily error statistics for a specific date
     */
    private function getDailyErrorStats(?string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        return DB::table('daily_command_errors')->select('class_name', 'error_count', 'email_sent')->where('error_date', $date)->get()->toArray();
    }

    /**
     * Get error statistics for a specific command
     */
    private function getCommandErrorStats(string $className, int $days = 7): array
    {
        return DB::table('daily_command_errors')
            ->select('error_date', 'error_count', 'email_sent')
            ->where('class_name', $className)
            ->where('error_date', '>=', now()->subDays($days)->toDateString())
            ->orderBy('error_date', 'desc')
            ->get()
            ->toArray();
    }
}
