<?php

use App\Models\EventCategory;

// Official game website URLs
$gameUrls = [
    'Dota 2' => 'https://www.dota2.com/',
    'Apex' => 'https://www.ea.com/games/apex-legends',
    'FIFA' => 'https://www.ea.com/games/fifa',
    'Chess' => 'https://www.chess.com/',
    'Valorant' => 'https://playvalorant.com/',
    'League of Legends: Wild Drift (Mobile)' => 'https://wildrift.leagueoflegends.com/',
    'Honor of Kings' => 'https://www.honorofkings.com/',
    'League of Legends' => 'https://www.leagueoflegends.com/',
    'Overwatch 2' => 'https://overwatch.blizzard.com/',
    'Tom Clancy\'s Rainbow Six Siege' => 'https://www.ubisoft.com/en-us/game/rainbow-six/siege',
    'CS GO' => 'https://www.counter-strike.net/',
    'Mobile Legends: Bang Bang (Mobile)' => 'https://m.mobilelegends.com/',
];

$updated = 0;

foreach ($gameUrls as $gameTitle => $url) {
    $category = EventCategory::where('gameTitle', $gameTitle)->first();
    if ($category) {
        $category->url = $url;
        $category->save();
        echo "Updated: {$gameTitle} -> {$url}\n";
        $updated++;
    } else {
        echo "Not found: {$gameTitle}\n";
    }
}

// Clear cache after updating
EventCategory::clearCache();

echo "\nTotal updated: {$updated}\n";
echo "Cache cleared.\n";
