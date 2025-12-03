<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\JoinEvent;
use App\Models\ActivityLogs;

echo "========================================\n";
echo "Final Verification Report\n";
echo "========================================\n\n";

// Part 1: Join Events Verification
echo "PART 1: Join Events Status\n";
echo "----------------------------------------\n";

$totalConfirmed = JoinEvent::where('join_status', 'confirmed')->count();
echo "✓ Total confirmed join_events: {$totalConfirmed}\n";

$withCompletedPayment = JoinEvent::where('join_status', 'confirmed')
    ->where('payment_status', 'completed')
    ->count();
echo "✓ With 'completed' payment_status: {$withCompletedPayment}\n";

$withCaptains = JoinEvent::where('join_status', 'confirmed')
    ->whereNotNull('roster_captain_id')
    ->count();
echo "✓ With assigned roster captains: {$withCaptains}\n";

$withFullRosters = DB::select("
    SELECT COUNT(*) as count
    FROM join_events je
    INNER JOIN event_details ed ON je.event_details_id = ed.id
    INNER JOIN event_categories ec ON ed.event_category_id = ec.id
    WHERE je.join_status = 'confirmed'
    AND (
        SELECT COUNT(*)
        FROM roster_members rm
        WHERE rm.join_events_id = je.id
    ) >= ec.player_per_team
");
$fullRosterCount = $withFullRosters[0]->count ?? 0;
echo "✓ With full rosters: {$fullRosterCount}\n\n";

// Payment verification
echo "Payment Amount Verification:\n";
$paymentIssues = DB::select("
    SELECT COUNT(*) as count
    FROM join_events je
    INNER JOIN event_details ed ON je.event_details_id = ed.id
    INNER JOIN event_tier et ON ed.event_tier_id = et.id
    WHERE je.join_status = 'confirmed'
    AND (
        SELECT COALESCE(SUM(payment_amount), 0)
        FROM participant_payments pp
        WHERE pp.join_events_id = je.id
    ) != et.tierEntryFee
    AND et.tierEntryFee > 0
");
$issueCount = $paymentIssues[0]->count ?? 0;
echo "⚠ Join events with payment mismatches: {$issueCount}\n";
echo "  (This is expected if payments are handled separately)\n\n";

// Part 2: Activities Verification
echo "PART 2: User Activities\n";
echo "----------------------------------------\n";

$totalActivities = ActivityLogs::count();
echo "✓ Total activity logs: {$totalActivities}\n";

$starredActivities = ActivityLogs::where('action', 'starred')->count();
echo "✓ Friend (starred) activities: {$starredActivities}\n";

$followedActivities = ActivityLogs::where('action', 'followed')->count();
echo "✓ Follow activities: {$followedActivities}\n";

$joinedActivities = ActivityLogs::where('action', 'joined_event')->count();
echo "✓ Event join activities: {$joinedActivities}\n\n";

// Sample data
echo "Sample Join Event:\n";
echo "----------------------------------------\n";
$sample = JoinEvent::where('join_status', 'confirmed')
    ->with(['eventDetails.tier', 'roster', 'captain'])
    ->first();

if ($sample) {
    echo "ID: {$sample->id}\n";
    echo "Event: {$sample->eventDetails->eventName}\n";
    echo "Join Status: {$sample->join_status}\n";
    echo "Payment Status: {$sample->payment_status}\n";
    echo "Tier Entry Fee: {$sample->eventDetails->tier->tierEntryFee}\n";
    echo "Roster Size: {$sample->roster->count()}\n";
    echo "Captain ID: {$sample->roster_captain_id}\n";
    if ($sample->captain) {
        echo "Captain User ID: {$sample->captain->user_id}\n";
    }
}

echo "\n========================================\n";
echo "✓ Verification Complete!\n";
echo "========================================\n";
