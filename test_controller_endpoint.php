<?php

/**
 * Test the actual controller endpoint
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\RosterMember;
use App\Models\RosterCaptain;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTING CONTROLLER ENDPOINT ===\n\n";

try {
    // Clean up previous test data
    echo "Cleaning up previous test data...\n";
    $user = User::find(6);
    if ($user) {
        $joinEvents = JoinEvent::whereHas('roster', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('event_details_id', 20)->get();

        foreach ($joinEvents as $join) {
            DB::transaction(function() use ($join) {
                RosterCaptain::where('join_events_id', $join->id)->delete();
                RosterMember::where('join_events_id', $join->id)->delete();
                $join->delete();
            });
        }
        echo "✓ Cleaned up\n\n";
    }

    // Create a mock request
    $request = Request::create(
        '/participant/event/20/join/redirect/selectOrCreateTeamToJoinEvent',
        'POST'
    );

    // Set the authenticated user
    $request->attributes->set('user', $user);

    // Instantiate controller
    $paymentService = app(\App\Services\PaymentService::class);
    $eventMatchService = app(\App\Services\EventMatchService::class);
    $controller = new \App\Http\Controllers\Participant\ParticipantEventController(
        $paymentService,
        $eventMatchService
    );

    echo "Testing redirectToSelectOrCreateTeamToJoinEvent...\n";
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "Event ID: 20 (Premier Football League - Solo Event)\n\n";

    // Check team count before
    $teamsBefore = Team::whereHas('members', function($q) use ($user) {
        $q->where('user_id', $user->id)->where('status', 'accepted');
    })->count();
    echo "Teams before: {$teamsBefore}\n\n";

    // Call the controller method
    $response = $controller->redirectToSelectOrCreateTeamToJoinEvent($request, 20);

    // Check if response is a view
    if ($response instanceof \Illuminate\View\View) {
        echo "✓ Controller returned a view: {$response->name()}\n\n";

        if ($response->name() == 'Participant.EventNotify') {
            echo "✅ SUCCESS: Auto-registration completed!\n";
            echo "   User was automatically registered for the solo event\n\n";

            // Verify the data
            $joinEvent = JoinEvent::whereHas('roster', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('event_details_id', 20)->first();

            if ($joinEvent) {
                echo "Verification:\n";
                echo "  - Join Event ID: {$joinEvent->id}\n";
                echo "  - Team ID: {$joinEvent->team_id}\n";
                echo "  - Roster Captain ID: {$joinEvent->roster_captain_id}\n";

                $team = Team::find($joinEvent->team_id);
                echo "  - Team Name: {$team->teamName}\n";
                echo "  - Team Member Limit: {$team->member_limit}\n";

                $rosterCount = RosterMember::where('join_events_id', $joinEvent->id)->count();
                echo "  - Roster Members: {$rosterCount}\n";

                $captainExists = RosterCaptain::where('join_events_id', $joinEvent->id)->exists();
                echo "  - Roster Captain Set: " . ($captainExists ? 'Yes ✓' : 'No ✗') . "\n";

                // Check team count after
                $teamsAfter = Team::whereHas('members', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('status', 'accepted');
                })->count();
                echo "  - Teams after: {$teamsAfter}\n";

                if ($team->member_limit == 1 && $team->teamName == $user->name && $captainExists) {
                    echo "\n✅ ALL CHECKS PASSED!\n";
                } else {
                    echo "\n⚠ Some checks failed\n";
                }
            } else {
                echo "⚠ Could not find join event in database\n";
            }
        } else {
            echo "⚠ Unexpected view: {$response->name()}\n";
            echo "   Expected: Participant.EventNotify\n";

            // Check if there were errors
            $data = $response->getData();
            if (isset($data['errorMessage'])) {
                echo "   Error: {$data['errorMessage']}\n";
            }
        }
    } else {
        echo "⚠ Response is not a view\n";
        echo "   Response type: " . get_class($response) . "\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== CONTROLLER TEST COMPLETE ===\n\n";
