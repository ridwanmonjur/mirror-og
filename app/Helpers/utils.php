<?php

use Carbon\Carbon;
function generateCarbonDateTime($startDate, $startTime)
{
    $startTime= fixTimeToRemoveSeconds($startTime);

    if ($startDate != null && $startTime != null) {
        $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i', $startDate . ' ' . $startTime, 'UTC') ?? null;
        // $carbonDateTimeUtc = $carbonDateTimeUtc->setTimezone('Asia/Singapore');
        return $carbonDateTimeUtc;
    } else {
       return null;
    }
}