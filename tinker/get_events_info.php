<?php

use App\Models\EventDetail;

$events = EventDetail::with('game', 'tier', 'type')->get();

echo "=== ALL EVENTS INFO ===\n\n";

foreach($events as $e) {
    echo "ID: {$e->id}\n";
    echo "Name: {$e->eventName}\n";
    echo "Game: " . ($e->game->gameTitle ?? 'N/A') . "\n";
    echo "Type: " . ($e->type->eventTypeName ?? 'N/A') . "\n";
    echo "Tier: " . ($e->tier->tierName ?? 'N/A') . "\n";
    echo "Description: " . (strlen($e->eventDescription ?? '') > 0 ? 'Has description' : 'No description') . "\n";
    echo str_repeat('-', 80) . "\n";
}
