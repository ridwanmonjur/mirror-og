<?php

use Carbon\Carbon;

use Illuminate\Support\Str;

function generateCarbonDateTime($startDate, $startTime)
{
    if ($startTime != null) {
        if (substr_count($startTime, ':') === 2) {
            $startTime = explode(':', $startTime);
            $startTime = $startTime[0].':'.$startTime[1];
        }
    }


    if ($startDate !== null && $startTime !== null) {
        return Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, 'UTC') ?? null;
    }
    return null;
}

function generateToken(?int $number = 64): string
{
    return Str::random($number);
}



