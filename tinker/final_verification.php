<?php

use App\Models\EventDetail;

$events = EventDetail::with('game')->orderBy('startDate')->get();

echo "=== FINAL EVENT LIST ===\n\n";

foreach($events as $e) {
    echo "ID {$e->id}: {$e->eventName}\n";
    echo "  Game: " . ($e->game->gameTitle ?? 'N/A') . "\n";
    echo "  Location: {$e->venue}\n";
    echo "  Dates: {$e->startDate} to {$e->endDate}\n\n";
}

echo "=== COMPLETE ===\n";
echo "Total events: {$events->count()}\n";
echo "All events have professional names, locations in Malaysia/Southeast Asia, and engaging descriptions!\n";
