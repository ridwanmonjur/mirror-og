<?php

namespace App\Jobs;

use App\Models\ActivityLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FollowStrategy
{
    public function handle($parameters)
    {
        ActivityLogs::create($parameters);
    }
}

class UnfollowStrategy
{
    public function handle($parameters)
    {
        ActivityLogs::where($parameters)->delete();
    }
}

class HandleFollows implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $strategy;

    protected $parameters;

    public function __construct($strategy, $parameters)
    {
        $this->strategy = $strategy;
        $this->parameters = $parameters;
    }

    // Simple Strategy
    public function handle()
    {
        $strategyClass = __NAMESPACE__.'\\'.$this->strategy.'Strategy';

        if (! class_exists($strategyClass)) {
            throw new \InvalidArgumentException("Strategy class {$strategyClass} does not exist.");
        }
        $strategy = new $strategyClass;
        $strategy->handle($this->parameters);
    }
}
