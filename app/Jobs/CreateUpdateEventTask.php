<?php

namespace App\Jobs;

use App\Models\EventDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class CreateUpdateEventTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventDetail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EventDetail $eventDetail)
    {
        $this->eventDetail = $eventDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->eventDetail->makeSignupTables();
            $this->eventDetail->createUpdateTask();
            $this->eventDetail->createStructuredDeadlines();
        } catch (Exception $e) {
            // Log the error
            logger()->error('Failed to create event tasks: ' . $e->getMessage());
            
            // Optionally, throw the exception to mark the job as failed
            throw $e;
        }
    }
}