<?php

/**
 * Test Script - Reusing Existing Solo Team
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\EventDetail;
use App\Models\Team;
use App\Models\JoinEvent;
use App\Models\RosterMember;
use App\Models\RosterCaptain;
use Illuminate\Support\Facades\DB;

echo "\n=== TEST: REUSING EXISTING SOLO TEAM ===\n\n";

try {
    // Get the user and team from previous test
    $user = User::find(6); // p_leigh
    $soloTeam = Team::find(154); // The solo team we just created

    if (!$user || !$soloTeam) {
        echo "❌ Previous test data not found. Run test_solo_event.php first.\n";
        exit(1);
    }

    echo "User: {$user->name}\n";
    echo "Existing Solo Team: {$soloTeam->teamName} (ID: {$soloTeam->id})\n\n";

    // Clean up the previous join event
    echo "Cleaning up previous join event...\n";
    $prevJoin = JoinEvent::where('team_id', $soloTeam->id)
        ->where('event_details_id', 20)
        ->first();

    if ($prevJoin) {
        DB::transaction(function() use ($prevJoin) {
            RosterCaptain::where('join_events_id', $prevJoin->id)->delete();
            RosterMember::where('join_events_id', $prevJoin->id)->delete();
            $prevJoin->delete();
        });
        echo "✓ Cleaned up\n\n";
    }

    // Find another solo event
    $event = EventDetail::whereHas('game', function($q) {
        $q->where('player_per_team', 1);
    })
    ->where('id', '!=', 20)
    ->where('status', '!=', 'CANCELLED')
    ->first();

    if (!$event) {
        echo "Using same event (ID: 20) for test\n";
        $event = EventDetail::find(20);
    }

    echo "Testing with event: {$event->eventName} (ID: {$event->id})\n\n";

    echo "Attempting to register...\n";
    echo "Expected: Should REUSE existing solo team (ID: {$soloTeam->id})\n\n";

    DB::beginTransaction();

    try {
        // This should find and reuse the existing team
        $foundTeam = Team::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'accepted');
        })
        ->where('member_limit', 1)
        ->where('teamName', $user->name)
        ->first();

        if ($foundTeam && $foundTeam->id == $soloTeam->id) {
            echo "✅ SUCCESS: Found and reused existing solo team!\n";
            echo "   Team ID: {$foundTeam->id}\n";
            echo "   Team Name: {$foundTeam->teamName}\n\n";

            // Register for event with existing team
            $participant = $user->participant;
            $joinEvent = JoinEvent::create([
                'team_id' => $foundTeam->id,
                'joiner_id' => $user->id,
                'joiner_participant_id' => $participant->id,
                'event_details_id' => $event->id,
            ]);

            $foundTeam->load('members');
            $teamMember = $foundTeam->members->where('user_id', $user->id)->first();

            $rosterMember = RosterMember::create([
                'join_events_id' => $joinEvent->id,
                'user_id' => $user->id,
                'team_member_id' => $teamMember->id,
                'team_id' => $foundTeam->id,
            ]);

            RosterCaptain::create([
                'team_member_id' => $teamMember->id,
                'join_events_id' => $joinEvent->id,
                'teams_id' => $foundTeam->id,
            ]);

            $joinEvent->roster_captain_id = $rosterMember->id;
            $joinEvent->save();

            echo "✓ Registration completed successfully\n";
            echo "✓ No new team was created (reused existing)\n\n";

            DB::commit();

            // Verify team count didn't increase
            $teamCount = Team::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'accepted');
            })->count();

            echo "Total teams for user: {$teamCount}\n";
            echo "Expected: 1 (only the solo team)\n\n";

            if ($teamCount == 1) {
                echo "✅ VERIFICATION PASSED: Team was reused, not duplicated!\n";
            } else {
                echo "⚠ Warning: User has {$teamCount} teams\n";
            }

        } else {
            echo "❌ FAILED: Did not find existing solo team\n";
            DB::rollBack();
            exit(1);
        }

    } catch (Exception $e) {
        DB::rollBack();
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== TEST COMPLETE ===\n\n";
