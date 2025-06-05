<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class DatabaseRestore extends Command
{
    protected $signature = 'tasks:restore-db {--path=database/backups/backup.sql : Path to the backup file}';
    protected $description = 'Restore the database from a SQL file';

    public function handle()
    {
        try {
        $path = $this->option('path');
        
        if (!file_exists($path)) {
            $this->error("Backup file does not exist: {$path}");
            return 1;
        }
        
        // Get connection details from config
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        
        // Create the command
        $command = "mysql -h {$host} -u {$username} " . ($password ? "-p{$password}" : "") . " {$database} < {$path}";
        
        // Execute the command
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("Database restored successfully from {$path}");
        } else {
            $this->error('Database restore failed');
        }
        } catch (Exception $e) {
            $this->logError(null, $e);
        }
    }
}