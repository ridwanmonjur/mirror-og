<?php

use App\Models\EventDetail;
use Illuminate\Support\Str;

// Find all events with generic "Test" names
$events = EventDetail::where('eventName', 'LIKE', 'Test%')
    ->orWhere('eventName', 'LIKE', '%Test%')
    ->get();

echo "Found {$events->count()} events with generic names.\n\n";

// Name templates for different games and types
$leagueNames = [
    'Chess' => ['Grand Masters Championship', 'Strategic Minds League', 'Elite Chess Circuit'],
    'Fifa' => ['Champions Cup', 'Premier Football League', 'Ultimate Soccer Showdown'],
    'Valorant' => ['Tactical Strike Series', 'Agents Arena League', 'Phantom Force Championship'],
    'CSGO' => ['Global Offensive Masters', 'Counter Strike Elite', 'Tactical Warfare League'],
    'MLBB' => ['Mobile Legends Championship', 'Battle Arena Masters', 'Legends Clash League'],
    'Apex' => ['Apex Predators League', 'Battle Royale Championship', 'Legends Combat Series'],
    'LOL' => ['Summoners Cup', 'League of Champions', 'Rift Masters Series'],
    'Dota 2' => ['International Dota Series', 'Ancient Defense League', 'MOBA Masters Cup'],
];

$tournamentPrefixes = ['Spring', 'Summer', 'Winter', 'Autumn', 'Elite', 'Pro', 'Championship', 'Premier', 'Ultimate'];
$tournamentSuffixes = ['Cup', 'Series', 'Showdown', 'Battle', 'Challenge', 'Open', 'Invitational'];

foreach ($events as $event) {
    $oldName = $event->eventName;
    $newName = $oldName;

    // Extract game name from event name or category
    $gameName = null;
    if (preg_match('/Chess/i', $oldName)) $gameName = 'Chess';
    elseif (preg_match('/Fifa|Football|Soccer/i', $oldName)) $gameName = 'Fifa';
    elseif (preg_match('/Valorant/i', $oldName)) $gameName = 'Valorant';
    elseif (preg_match('/CSGO|Counter.*Strike|CS:GO/i', $oldName)) $gameName = 'CSGO';
    elseif (preg_match('/MLBB|Mobile.*Legends/i', $oldName)) $gameName = 'MLBB';
    elseif (preg_match('/Apex/i', $oldName)) $gameName = 'Apex';
    elseif (preg_match('/LOL|League.*of.*Legends/i', $oldName)) $gameName = 'LOL';
    elseif (preg_match('/Dota/i', $oldName)) $gameName = 'Dota 2';

    // Check if it's a league or tournament
    $isLeague = preg_match('/League/i', $oldName);
    $isTournament = preg_match('/Tournament/i', $oldName);

    // Extract team count if present
    $teamCount = null;
    if (preg_match('/(\d+)-Team/i', $oldName, $matches)) {
        $teamCount = $matches[1];
    }

    // Generate new name
    if ($gameName && $isLeague && isset($leagueNames[$gameName])) {
        // Pick a random name from the game-specific league names
        $newName = $leagueNames[$gameName][array_rand($leagueNames[$gameName])];

        // Add team count if it was in original name
        if ($teamCount) {
            $newName = "{$teamCount}-Team {$newName}";
        }
    } elseif ($gameName && $isTournament) {
        // Generate tournament name
        $prefix = $tournamentPrefixes[array_rand($tournamentPrefixes)];
        $suffix = $tournamentSuffixes[array_rand($tournamentSuffixes)];
        $newName = "{$prefix} {$gameName} {$suffix}";

        if ($teamCount) {
            $newName = "{$prefix} {$teamCount}-Team {$gameName} {$suffix}";
        }
    } elseif ($teamCount) {
        // Generic team-based event
        $prefix = $tournamentPrefixes[array_rand($tournamentPrefixes)];
        $suffix = $tournamentSuffixes[array_rand($tournamentSuffixes)];
        $newName = "{$prefix} {$teamCount}-Team {$suffix}";
    }

    // Make sure the new name is unique
    $baseNewName = $newName;
    $counter = 1;
    while (EventDetail::where('eventName', $newName)
        ->where('id', '!=', $event->id)
        ->exists()) {
        $newName = "{$baseNewName} " . Str::upper(Str::random(3));
        $counter++;
        if ($counter > 10) break; // Safety check
    }

    // Update slug as well
    $baseSlug = Str::slug($newName);
    $slug = $baseSlug;
    $counter = 1;
    while (EventDetail::where('slug', $slug)
        ->where('id', '!=', $event->id)
        ->exists()) {
        $slug = "{$baseSlug}-" . Str::lower(Str::random(3));
        $counter++;
        if ($counter > 10) break; // Safety check
    }

    // Update the event
    $event->eventName = $newName;
    $event->slug = $slug;
    $event->save();

    echo "Event ID {$event->id}:\n";
    echo "  Old: {$oldName}\n";
    echo "  New: {$newName}\n";
    echo "  Slug: {$slug}\n\n";
}

echo "Successfully renamed {$events->count()} events.\n";
