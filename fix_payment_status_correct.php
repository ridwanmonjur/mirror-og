<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Fixing Payment Status for Confirmed Join Events\n";
echo "========================================\n\n";

echo "Note: payment_status ENUM values are: 'pending', 'completed', 'waived'\n";
echo "Setting all confirmed join_events to 'completed'\n\n";

// Count before
$pendingCount = DB::table('join_events')
    ->where('join_status', 'confirmed')
    ->where('payment_status', 'pending')
    ->count();

echo "Records with 'pending' payment_status: {$pendingCount}\n";

// Update
$updated = DB::table('join_events')
    ->where('join_status', 'confirmed')
    ->update(['payment_status' => 'completed']);

echo "✓ Updated {$updated} records to 'completed'\n\n";

// Verify
$completedCount = DB::table('join_events')
    ->where('join_status', 'confirmed')
    ->where('payment_status', 'completed')
    ->count();

echo "Verification:\n";
echo "  - Confirmed join_events with 'completed' payment_status: {$completedCount}\n\n";

echo "✓ Done!\n";
