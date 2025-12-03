<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Clearing cache...\n";
\Illuminate\Support\Facades\Cache::flush();
echo "Cache cleared\n\n";

$eventIds = [18, 21, 23, 24, 26, 62, 27, 22];

$factory = new \Database\Factories\BracketsFactory();
$reflection = new ReflectionClass($factory);

$seedMethod = $reflection->getMethod('seedLeagueDemoResults');
$seedMethod->setAccessible(true);

$calculateMethod = $reflection->getMethod('calculateLeagueStandings');
$calculateMethod->setAccessible(true);

foreach ($eventIds as $eventId) {
    echo "\n--- Processing Event $eventId ---\n";

    try {
        $event = \App\Models\EventDetail::find($eventId);

        if (!$event) {
            echo "Event $eventId not found, skipping\n";
            continue;
        }

        echo "Event: " . $event->event_name . "\n";

        // Get games per match
        $eventGame = $event->game->gameTitle ?? 'Dota 2';
        $eventCategory = \App\Models\EventCategory::where('gameTitle', $eventGame)->first();
        $gamesPerMatch = $eventCategory->games_per_match ?? 3;

        echo "Game: $eventGame, Games per match: $gamesPerMatch\n";

        // Clear cache before processing
        echo "Clearing event cache...\n";
        \Illuminate\Support\Facades\Cache::flush();

        // Seed new league demo results
        echo "Seeding match results to Firestore...\n";
        $seedMethod->invoke($factory, $eventId, $gamesPerMatch);

        // Wait for cloud function to process
        echo "Waiting for cloud function to process...\n";
        sleep(5);

        // Calculate league standings
        echo "Calculating league standings...\n";
        $calculateMethod->invoke($factory, $eventId);

        echo "Event $eventId completed successfully!\n";

        // Clear cache after processing
        echo "Clearing cache after event...\n";
        \Illuminate\Support\Facades\Cache::flush();

        // Wait between events to avoid overwhelming cloud functions
        if ($eventId !== end($eventIds)) {
            echo "Waiting before next event...\n";
            sleep(3);
        }

    } catch (\Exception $e) {
        echo "Error processing event $eventId: " . $e->getMessage() . "\n";
        echo "Continuing to next event...\n";
    }
}

echo "\n=== All events processed ===\n";
echo "Clearing final cache...\n";
\Illuminate\Support\Facades\Cache::flush();
echo "Done!\n";
