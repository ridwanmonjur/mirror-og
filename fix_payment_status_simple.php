<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Starting update...\n";

    // Count before
    $before = DB::table('join_events')
        ->where('join_status', 'confirmed')
        ->where('payment_status', '')
        ->count();

    echo "Records to update: {$before}\n";

    // Update
    $updated = DB::table('join_events')
        ->where('join_status', 'confirmed')
        ->where('payment_status', '')
        ->update(['payment_status' => 'confirmed']);

    echo "Updated: {$updated} records\n";

    // Count after
    $after = DB::table('join_events')
        ->where('join_status', 'confirmed')
        ->where('payment_status', 'confirmed')
        ->count();

    echo "Confirmed payment_status count: {$after}\n";
    echo "✓ Done!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
