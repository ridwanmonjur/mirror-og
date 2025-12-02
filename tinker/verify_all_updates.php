<?php

use App\Models\EventDetail;
use Carbon\Carbon;

$events = EventDetail::with('game')->orderBy('startDate')->get();

echo "=== COMPLETE EVENT VERIFICATION ===\n\n";

echo str_repeat('=', 120) . "\n";
printf("%-5s %-40s %-25s %-15s %-12s %-12s\n", "ID", "Event Name", "Game", "Location", "Start", "End");
echo str_repeat('=', 120) . "\n";

foreach ($events as $event) {
    printf(
        "%-5s %-40s %-25s %-15s %-12s %-12s\n",
        $event->id,
        substr($event->eventName, 0, 40),
        substr($event->game->gameTitle ?? 'N/A', 0, 25),
        substr($event->venue ?? 'No location', 0, 15),
        $event->startDate,
        $event->endDate
    );
}

echo str_repeat('=', 120) . "\n\n";

echo "=== DESCRIPTION PREVIEW ===\n\n";

foreach ($events as $event) {
    // Skip the "Welcome to Driftwood" events as they have original descriptions
    if (strpos($event->eventName, 'Welcome to Driftwood') !== false) {
        continue;
    }

    echo "Event: {$event->eventName} (ID: {$event->id})\n";
    echo "Game: " . ($event->game->gameTitle ?? 'N/A') . "\n";
    echo "Location: {$event->venue}\n";
    echo "Description Preview: " . substr($event->eventDescription, 0, 150) . "...\n";
    echo str_repeat('-', 100) . "\n\n";
}

echo "=== STATISTICS ===\n";
echo "Total events: {$events->count()}\n";

// Count by location country
$locationCounts = [];
foreach ($events as $event) {
    if (strpos($event->venue, 'Malaysia') !== false) {
        $locationCounts['Malaysia'] = ($locationCounts['Malaysia'] ?? 0) + 1;
    } elseif (strpos($event->venue, 'Singapore') !== false) {
        $locationCounts['Singapore'] = ($locationCounts['Singapore'] ?? 0) + 1;
    } elseif (strpos($event->venue, 'Thailand') !== false) {
        $locationCounts['Thailand'] = ($locationCounts['Thailand'] ?? 0) + 1;
    } elseif (strpos($event->venue, 'Philippines') !== false) {
        $locationCounts['Philippines'] = ($locationCounts['Philippines'] ?? 0) + 1;
    } elseif (strpos($event->venue, 'Indonesia') !== false) {
        $locationCounts['Indonesia'] = ($locationCounts['Indonesia'] ?? 0) + 1;
    } elseif (strpos($event->venue, 'Vietnam') !== false) {
        $locationCounts['Vietnam'] = ($locationCounts['Vietnam'] ?? 0) + 1;
    }
}

echo "\nEvents by country:\n";
foreach ($locationCounts as $country => $count) {
    echo "  {$country}: {$count} events\n";
}

// Check dates are valid
echo "\nDate validation:\n";
$allDatesValid = true;
$dateRangeStart = Carbon::create(2024, 12, 1);
$dateRangeEnd = Carbon::create(2025, 8, 31);

foreach ($events as $event) {
    $startDate = Carbon::parse($event->startDate);
    $endDate = Carbon::parse($event->endDate);

    if (!$startDate->between($dateRangeStart, $dateRangeEnd) || !$endDate->lte($dateRangeEnd)) {
        echo "  ✗ Event {$event->id} has invalid dates\n";
        $allDatesValid = false;
    }

    if ($startDate->diffInDays($endDate) != 21) {
        echo "  ✗ Event {$event->id} duration is not 21 days\n";
        $allDatesValid = false;
    }
}

if ($allDatesValid) {
    echo "  ✓ All dates are valid (Dec 2024 - Aug 2025, 21-day duration)\n";
}

echo "\n✓ Verification complete!\n";
