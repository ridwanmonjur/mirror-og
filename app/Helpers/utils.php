<?php

use Carbon\Carbon;

function generateCarbonDateTime($startDate, $startTime)
{
    $startTime = fixTimeToRemoveSeconds($startTime);

    if ($startDate !== null && $startTime !== null) {
        return Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, 'UTC') ?? null;
    }
    return null;
}
