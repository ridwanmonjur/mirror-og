<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating payment_status for confirmed join_events...\n";

$updated = DB::table('join_events')
    ->where('join_status', 'confirmed')
    ->where(function($q) {
        $q->where('payment_status', '')
          ->orWhereNull('payment_status');
    })
    ->update(['payment_status' => 'confirmed']);

echo "✓ Updated {$updated} records\n";

// Verify
$count = DB::table('join_events')
    ->where('join_status', 'confirmed')
    ->where('payment_status', 'confirmed')
    ->count();

echo "✓ Verified: {$count} join_events now have payment_status = 'confirmed'\n";
