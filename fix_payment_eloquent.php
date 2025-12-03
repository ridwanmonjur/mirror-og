<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\JoinEvent;

echo "Updating payment_status using Eloquent...\n";

$joinEvents = JoinEvent::where('join_status', 'confirmed')->get();

echo "Found {$joinEvents->count()} confirmed join_events\n";

$updated = 0;
foreach ($joinEvents as $joinEvent) {
    if ($joinEvent->payment_status !== 'confirmed') {
        $joinEvent->payment_status = 'confirmed';
        if ($joinEvent->save()) {
            $updated++;
        }
    }
}

echo "✓ Updated {$updated} records\n";

// Verify
$confirmed = JoinEvent::where('join_status', 'confirmed')
    ->where('payment_status', 'confirmed')
    ->count();

echo "✓ Verified: {$confirmed} join_events now have payment_status = 'confirmed'\n";
