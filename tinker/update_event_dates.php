<?php

use App\Models\EventDetail;
use Carbon\Carbon;

// Get all events
$events = EventDetail::all();

echo "Found {$events->count()} events to update.\n";

// December 2024 to August 2025 range
$startRange = Carbon::create(2024, 12, 1);
$endRange = Carbon::create(2025, 8, 31);

// Calculate total days in range - subtract 21 days to ensure end date doesn't go into September
$totalDays = $startRange->diffInDays($endRange) - 21;

foreach ($events as $event) {
    // Generate random start date within range
    $randomDays = rand(0, $totalDays);
    $newStartDate = $startRange->copy()->addDays($randomDays);

    // End date is 21 days after start date
    $newEndDate = $newStartDate->copy()->addDays(21);

    $oldStartDate = $event->startDate;
    $oldEndDate = $event->endDate;

    // Update the event
    $event->startDate = $newStartDate->format('Y-m-d');
    $event->endDate = $newEndDate->format('Y-m-d');
    $event->save();

    echo "Event ID {$event->id}: '{$event->eventName}'\n";
    echo "  Old: {$oldStartDate} to {$oldEndDate}\n";
    echo "  New: {$newStartDate->format('Y-m-d')} to {$newEndDate->format('Y-m-d')}\n\n";
}

echo "Successfully updated {$events->count()} events.\n";
