<?php

use App\Models\EventDetail;
use Carbon\Carbon;

// Get all events
$events = EventDetail::all();

echo "=== EVENT DATE AND NAME VERIFICATION ===\n";
echo "Total events: {$events->count()}\n\n";

$dateRangeStart = Carbon::create(2024, 12, 1);
$dateRangeEnd = Carbon::create(2025, 8, 31);

$datesValid = true;
$eventsReport = [];

foreach ($events as $event) {
    $startDate = Carbon::parse($event->startDate);
    $endDate = Carbon::parse($event->endDate);
    $daysDiff = $startDate->diffInDays($endDate);

    $isStartDateValid = $startDate->between($dateRangeStart, $dateRangeEnd);
    $isEndDateValid = $endDate->lte($dateRangeEnd);
    $isDurationValid = $daysDiff == 21;

    $status = '✓ Valid';
    if (!$isStartDateValid || !$isEndDateValid || !$isDurationValid) {
        $status = '✗ Invalid';
        $datesValid = false;
    }

    $eventsReport[] = [
        'id' => $event->id,
        'name' => $event->eventName,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'duration' => $daysDiff,
        'status' => $status,
        'validStart' => $isStartDateValid,
        'validEnd' => $isEndDateValid,
        'validDuration' => $isDurationValid,
    ];
}

// Sort by start date
usort($eventsReport, function($a, $b) {
    return strcmp($a['startDate'], $b['startDate']);
});

// Display report
echo "Date Range: December 2024 to August 2025\n";
echo "Expected Duration: 21 days\n\n";
echo str_repeat('=', 100) . "\n";
printf("%-5s %-45s %-12s %-12s %-8s %-10s\n", "ID", "Event Name", "Start Date", "End Date", "Days", "Status");
echo str_repeat('=', 100) . "\n";

foreach ($eventsReport as $report) {
    printf(
        "%-5s %-45s %-12s %-12s %-8s %-10s\n",
        $report['id'],
        substr($report['name'], 0, 45),
        $report['startDate'],
        $report['endDate'],
        $report['duration'],
        $report['status']
    );

    if (!$report['validStart'] || !$report['validEnd'] || !$report['validDuration']) {
        if (!$report['validStart']) echo "  ⚠ Start date outside valid range\n";
        if (!$report['validEnd']) echo "  ⚠ End date outside valid range\n";
        if (!$report['validDuration']) echo "  ⚠ Duration is {$report['duration']} days, expected 21\n";
    }
}

echo str_repeat('=', 100) . "\n\n";

if ($datesValid) {
    echo "✓ All events have valid dates!\n";
} else {
    echo "✗ Some events have invalid dates!\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total events: {$events->count()}\n";
echo "Events in December 2024: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2024-12'))->count() . "\n";
echo "Events in January 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-01'))->count() . "\n";
echo "Events in February 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-02'))->count() . "\n";
echo "Events in March 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-03'))->count() . "\n";
echo "Events in April 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-04'))->count() . "\n";
echo "Events in May 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-05'))->count() . "\n";
echo "Events in June 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-06'))->count() . "\n";
echo "Events in July 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-07'))->count() . "\n";
echo "Events in August 2025: " . collect($eventsReport)->filter(fn($e) => str_starts_with($e['startDate'], '2025-08'))->count() . "\n";
