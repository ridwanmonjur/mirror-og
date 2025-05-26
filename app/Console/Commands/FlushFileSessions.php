<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class FlushFileSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          $files = File::glob(storage_path('framework/sessions/*'));

          // Exclude the .ignore file
          $files = array_filter($files, function ($file) {
              return basename($file) !== '.ignore';
          });
  
          File::delete(...$files);
  
          $this->info('Session cleared successfully.');
          
    }
}
