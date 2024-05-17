<?php

use Carbon\Carbon;

function generateCarbonDateTime($startDate, $startTime)
{
    $startTime = fixTimeToRemoveSeconds($startTime);

    if ($startDate != null && $startTime != null) {
        $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, 'UTC') ?? null;

        return $carbonDateTimeUtc;
    } else {
        return null;
    }
}
