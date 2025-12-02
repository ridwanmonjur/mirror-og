<?php

use App\Models\EventDetail;

// List of Malaysian and Southeast Asian locations
$locations = [
    // Malaysia
    'Kuala Lumpur, Malaysia',
    'Penang, Malaysia',
    'Johor Bahru, Malaysia',
    'Malacca, Malaysia',
    'Ipoh, Malaysia',
    'Kuching, Malaysia',
    'Kota Kinabalu, Malaysia',
    'Cyberjaya, Malaysia',
    'Petaling Jaya, Malaysia',

    // Other Southeast Asian countries
    'Singapore',
    'Bangkok, Thailand',
    'Manila, Philippines',
    'Jakarta, Indonesia',
    'Ho Chi Minh City, Vietnam',
    'Hanoi, Vietnam',
];

$events = EventDetail::all();

echo "=== UPDATING EVENT LOCATIONS ===\n\n";

foreach ($events as $event) {
    $oldVenue = $event->venue;

    // Assign a random location from the list
    $newVenue = $locations[array_rand($locations)];

    $event->venue = $newVenue;
    $event->save();

    echo "Event ID {$event->id}: {$event->eventName}\n";
    echo "  Old: " . ($oldVenue ?: 'No location') . "\n";
    echo "  New: {$newVenue}\n\n";
}

echo "=== SUMMARY ===\n";
echo "Successfully updated {$events->count()} event locations to Malaysia/Southeast Asia!\n";
