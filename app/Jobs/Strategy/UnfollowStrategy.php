<?php
namespace App\Jobs\Strategy;

use App\Models\ActivityLogs;

class UnfollowStrategy
{
    public function handle($parameters)
    {
        ActivityLogs::where($parameters)->delete();
    }
}