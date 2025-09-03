<?php

namespace App\Services;

use App\Models\NotificationCounter;
use App\Models\Task;
use App\Traits\PrinterLoggerTrait;
use App\Models\NotifcationsUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WeeklyTaskService
{
    use PrinterLoggerTrait;

    public function execute(): void
    {
        $today = Carbon::now();
        $taskId = $this->logEntry('Weekly tasks in the database', 'tasks:weekly', '0 0 * *', $today);
        try {
            $monthAgo = Carbon::now()->copy()->subDays(99);
            
            DB::table('monitored_scheduled_task_log_items')
                ->whereIn('monitored_scheduled_task_id', function($query) use ($monthAgo) {
                    $query->select('id')
                          ->from('monitored_scheduled_tasks')
                          ->where('last_started_at', '<', $monthAgo);
                })
                ->delete();
                
            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $monthAgo)->delete();
            NotifcationsUser::where('created_at', '<', $monthAgo)->delete();
            Task::where('action_time', '<=', $monthAgo)->delete();
            NotificationCounter::resetNegativeCounts();
            Task::where('created_at', '<', $monthAgo)->delete();
            
            $this->manageLogFiles();
            
            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }

    private function manageLogFiles(): void
    {
        $logsPath = storage_path('logs');
        
        try {
            $this->copyAndRenameExistingLogs($logsPath);
            $this->readAllLogsInDirectory($logsPath);
            $this->deleteOldLogs($logsPath);
        } catch (Exception $e) {
            Log::error('Error managing log files: ' . $e->getMessage());
            throw $e;
        }
    }

    private function copyAndRenameExistingLogs(string $logsPath): void
    {
        $oneWeekAgo = Carbon::now()->subWeek();
        $datePrefix = $oneWeekAgo->format('Y-m-d');
        
        $logFiles = File::glob($logsPath . '/*.log');
        
        foreach ($logFiles as $logFile) {
            if (!File::exists($logFile)) {
                continue;
            }
            
            $fileName = basename($logFile);
            
            // Skip files that already have date prefixes
            if (preg_match('/^\d{4}-\d{2}-\d{2}_/', $fileName)) {
                continue;
            }
            
            $newFileName = $datePrefix . '_' . $fileName;
            $newFilePath = $logsPath . '/' . $newFileName;
            
            if (!File::exists($newFilePath)) {
                try {
                    File::copy($logFile, $newFilePath);
                    
                    // Verify the copy was successful before deleting original
                    if (File::exists($newFilePath)) {
                        File::delete($logFile);
                        Log::info("Archived log file: {$fileName} to {$newFileName} and deleted original");
                    } else {
                        Log::error("Failed to properly copy {$fileName}, original file not deleted");
                    }
                } catch (Exception $e) {
                    Log::error("Error archiving log file {$fileName}: " . $e->getMessage());
                }
            }
        }
    }

    private function readAllLogsInDirectory(string $logsPath): void
    {
        $logFiles = File::files($logsPath);
        
        foreach ($logFiles as $logFile) {
            if ($logFile->getExtension() === 'log') {
                $content = File::get($logFile->getPathname());
                Log::info("Read log file: {$logFile->getFilename()}, Size: " . strlen($content) . " bytes");
            }
        }
    }

    private function deleteOldLogs(string $logsPath): void
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        $logFiles = File::files($logsPath);
        
        foreach ($logFiles as $logFile) {
            if ($logFile->getExtension() === 'log') {
                $fileName = $logFile->getFilename();
                
                if (preg_match('/^(\d{4}-\d{2}-\d{2})_/', $fileName, $matches)) {
                    $fileDate = Carbon::parse($matches[1]);
                    
                    if ($fileDate->lt($threeMonthsAgo)) {
                        File::delete($logFile->getPathname());
                        Log::info("Deleted old log file: {$fileName}");
                    }
                }
            }
        }
    }
}