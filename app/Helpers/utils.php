<?php

use Carbon\Carbon;

use Illuminate\Support\Str;

function generateCarbonDateTime($startDate, $startTime)
{
    $startTime = fixTimeToRemoveSeconds($startTime);

    if ($startDate !== null && $startTime !== null) {
        return Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, 'UTC') ?? null;
    }
    return null;
}

function generateToken(?int $number = 64): string
{
    return Str::random($number);
}



