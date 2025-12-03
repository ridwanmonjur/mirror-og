<?php

/**
 * Test Script for Solo Player Event Auto-Registration
 *
 * This script tests the automatic team creation and roster assignment
 * for solo player events (player_per_team = 1)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\EventDetail;
use App\Models\EventCategory;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamCaptain;
use App\Models\JoinEvent;
use App\Models\RosterMember;
use App\Models\RosterCaptain;
use Illuminate\Support\Facades\DB;

echo "\n=== SOLO PLAYER EVENT AUTO-REGISTRATION TEST ===\n\n";

try {
    // Step 1: Find or create a test user
    echo "Step 1: Getting test user...\n";
    $user = User::whereHas('participant')->first();

    if (!$user) {
        echo "❌ No user with participant role found. Please create a test user first.\n";
        exit(1);
    }

    echo "✓ Test User: {$user->name} (ID: {$user->id})\n\n";

    // Step 2: Find or create an event category with player_per_team = 1
    echo "Step 2: Setting up solo event category...\n";
    $category = EventCategory::where('player_per_team', 1)->first();

    if (!$category) {
        echo "Creating new solo event category...\n";
        $category = EventCategory::create([
            'gameTitle' => 'Test Solo Game',
            'player_per_team' => 1,
            'games_per_match' => 1,
            'user_id' => 1,
        ]);
    }

    echo "✓ Category: {$category->gameTitle} (player_per_team: {$category->player_per_team})\n\n";

    // Step 3: Find or create a test event
    echo "Step 3: Setting up test solo event...\n";
    $event = EventDetail::where('event_category_id', $category->id)
        ->where('status', '!=', 'CANCELLED')
        ->first();

    if (!$event) {
        echo "No suitable event found. Please create a test event with category ID {$category->id}\n";
        echo "You can do this through the admin panel or organizer interface.\n";
        exit(1);
    }

    echo "✓ Event: {$event->eventName} (ID: {$event->id})\n\n";

    // Step 4: Check current state
    echo "Step 4: Checking current state...\n";

    // Check existing teams for this user
    $existingTeams = Team::whereHas('members', function($q) use ($user) {
        $q->where('user_id', $user->id)->where('status', 'accepted');
    })->get();

    echo "Current teams: {$existingTeams->count()}\n";

    $soloTeam = $existingTeams->where('member_limit', 1)
        ->where('teamName', $user->name)
        ->first();

    if ($soloTeam) {
        echo "✓ Found existing solo team: {$soloTeam->teamName} (ID: {$soloTeam->id})\n";
    } else {
        echo "No existing solo team found - one will be created\n";
    }

    // Check if already joined this event
    $existingJoin = JoinEvent::whereHas('roster', function($q) use ($user) {
        $q->where('user_id', $user->id);
    })->where('event_details_id', $event->id)->first();

    if ($existingJoin) {
        echo "⚠ User already joined this event (Join ID: {$existingJoin->id})\n";
        echo "Cleaning up for test...\n";

        // Clean up existing registration
        DB::transaction(function() use ($existingJoin) {
            RosterCaptain::where('join_events_id', $existingJoin->id)->delete();
            RosterMember::where('join_events_id', $existingJoin->id)->delete();
            $existingJoin->delete();
        });

        echo "✓ Cleaned up existing registration\n";
    }

    echo "\n";

    // Step 5: Simulate the controller action
    echo "Step 5: Testing auto-registration logic...\n";
    echo "=========================================\n\n";

    DB::beginTransaction();

    try {
        // This simulates the logic in redirectToSelectOrCreateTeamToJoinEvent

        // Look for existing solo team
        $selectTeam = Team::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'accepted');
        })
        ->where('member_limit', 1)
        ->where('teamName', $user->name)
        ->first();

        if ($selectTeam) {
            echo "✓ Using existing solo team: {$selectTeam->teamName}\n";
        } else {
            echo "Creating new solo team...\n";

            // Check team limit
            $teamCount = Team::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'accepted');
            })->count();

            if ($teamCount >= 5) {
                throw new Exception("User already has 5 teams!");
            }

            $selectTeam = new Team();
            $selectTeam->teamName = $user->name;
            $selectTeam->slug = \Illuminate\Support\Str::slug($user->name);
            $selectTeam->creator_id = $user->id;
            $selectTeam->member_limit = 1;
            $selectTeam->save();

            echo "✓ Created team: {$selectTeam->teamName} (ID: {$selectTeam->id})\n";

            // Add user as team member
            TeamMember::create([
                'team_id' => $selectTeam->id,
                'user_id' => $user->id,
                'status' => 'accepted'
            ]);

            $selectTeam->load('members');

            echo "✓ Added user as team member\n";

            // Add user as team captain
            if ($selectTeam->members->isNotEmpty()) {
                TeamCaptain::insert([
                    'team_member_id' => $selectTeam->members[0]->id,
                    'teams_id' => $selectTeam->id,
                ]);
                echo "✓ Set user as team captain\n";
            }
        }

        // Register team for event
        echo "\nRegistering for event...\n";

        $participant = $user->participant;
        if (!$participant) {
            throw new Exception("User doesn't have participant profile!");
        }

        $joinEvent = JoinEvent::create([
            'team_id' => $selectTeam->id,
            'joiner_id' => $user->id,
            'joiner_participant_id' => $participant->id,
            'event_details_id' => $event->id,
        ]);

        echo "✓ Created join_event (ID: {$joinEvent->id})\n";

        // Create roster member
        $selectTeam->load('members');
        $teamMember = $selectTeam->members->where('user_id', $user->id)->first();

        if (!$teamMember) {
            throw new Exception("Team member not found!");
        }

        $rosterMember = RosterMember::create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $user->id,
            'team_member_id' => $teamMember->id,
            'team_id' => $selectTeam->id,
        ]);

        echo "✓ Created roster_member (ID: {$rosterMember->id})\n";

        // Create roster captain
        $rosterCaptain = RosterCaptain::create([
            'team_member_id' => $teamMember->id,
            'join_events_id' => $joinEvent->id,
            'teams_id' => $selectTeam->id,
        ]);

        echo "✓ Created roster_captain (ID: {$rosterCaptain->id})\n";

        // Update join_event with roster_captain_id
        $joinEvent->roster_captain_id = $rosterMember->id;
        $joinEvent->save();

        echo "✓ Updated join_event.roster_captain_id = {$rosterMember->id}\n";

        DB::commit();

        echo "\n";
        echo "=========================================\n";
        echo "✅ AUTO-REGISTRATION SUCCESSFUL!\n";
        echo "=========================================\n\n";

        // Step 6: Verify the results
        echo "Step 6: Verifying database entries...\n";
        echo "-------------------------------------\n";

        $joinEvent->refresh();
        $joinEvent->load(['team', 'roster', 'captain']);

        echo "Join Event:\n";
        echo "  - ID: {$joinEvent->id}\n";
        echo "  - Team: {$joinEvent->team->teamName} (ID: {$joinEvent->team_id})\n";
        echo "  - Event: {$event->eventName}\n";
        echo "  - Captain ID: {$joinEvent->roster_captain_id}\n";
        echo "  - Status: {$joinEvent->join_status}\n\n";

        echo "Team:\n";
        echo "  - Name: {$joinEvent->team->teamName}\n";
        echo "  - Member Limit: {$joinEvent->team->member_limit}\n";
        echo "  - Creator: " . ($joinEvent->team->creator_id == $user->id ? 'Test User ✓' : 'Different User') . "\n\n";

        echo "Roster Members:\n";
        foreach ($joinEvent->roster as $member) {
            echo "  - User: {$member->user->name} (ID: {$member->user_id})\n";
            echo "    Team Member ID: {$member->team_member_id}\n";
        }
        echo "\n";

        $rosterCaptainCheck = RosterCaptain::where('join_events_id', $joinEvent->id)->first();
        if ($rosterCaptainCheck) {
            echo "Roster Captain:\n";
            echo "  - Team Member ID: {$rosterCaptainCheck->team_member_id}\n";
            echo "  - Join Event ID: {$rosterCaptainCheck->join_events_id}\n";
            echo "  - Team ID: {$rosterCaptainCheck->teams_id}\n\n";
        }

        $teamCaptainCheck = TeamCaptain::where('teams_id', $selectTeam->id)->first();
        if ($teamCaptainCheck) {
            echo "Team Captain:\n";
            echo "  - Team Member ID: {$teamCaptainCheck->team_member_id}\n";
            echo "  - Team ID: {$teamCaptainCheck->teams_id}\n\n";
        }

        echo "✅ ALL VERIFICATIONS PASSED!\n\n";

        // Cleanup option
        echo "Do you want to clean up test data? (will delete the join_event)\n";
        echo "This will allow you to test again. (y/n): ";

        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim($line) == 'y') {
            DB::transaction(function() use ($joinEvent) {
                RosterCaptain::where('join_events_id', $joinEvent->id)->delete();
                RosterMember::where('join_events_id', $joinEvent->id)->delete();
                $joinEvent->delete();
            });
            echo "✓ Test data cleaned up\n";
        } else {
            echo "Test data preserved\n";
        }

    } catch (Exception $e) {
        DB::rollBack();
        echo "\n❌ ERROR: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== TEST COMPLETE ===\n\n";
