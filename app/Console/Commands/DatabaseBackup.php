<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseBackup extends Command
{
    protected $signature = 'tasks:backup {--path=database/backups/backup.sql : Path to save the backup}';
    protected $description = 'Backup the database to a SQL file';

    public function handle()
    {
        try {
        $path = $this->option('path');
        
        // Get connection details from config
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        
        // Create the command
        $command = "mysqldump -h {$host} -u {$username} " . ($password ? "-p{$password}" : "") . " {$database} > {$path}";
        
        // Execute the command
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("Database backup created successfully at {$path}");
        } else {
            $this->error('Database backup failed');
        }
        } catch (Exception $e) {
            $this->logError(null, $e);
        }
    }
}