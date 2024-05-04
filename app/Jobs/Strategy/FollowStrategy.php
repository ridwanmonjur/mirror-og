<?php
namespace App\Jobs\Strategy;

use App\Models\ActivityLogs;

class FollowStrategy
{
    public function handle($parameters)
    {
        ActivityLogs::create($parameters);
    }
}