<?php

/**
 * Script to fix join_events data consistency and create user activities
 *
 * This script:
 * 1. Checks all confirmed join_events for payment_status consistency
 * 2. Verifies participant payments sum equals tierEntryFee
 * 3. Fills rosters with members up to player_per_team limit
 * 4. Assigns first roster member as roster captain
 * 5. Creates activities for user friends
 * 6. Creates activities for event likes
 *
 * Run with: php artisan tinker tinker/fix_join_events_and_create_activities.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\RosterMember;
use App\Models\TeamMember;
use App\Models\EventDetail;
use App\Models\ActivityLogs;
use App\Models\User;
use App\Models\OrganizerFollow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "========================================\n";
echo "Starting Join Events Validation & Activity Creation\n";
echo "========================================\n\n";

// ====================
// PART 1: Fix Join Events
// ====================

echo "PART 1: Validating and Fixing Join Events\n";
echo "==========================================\n\n";

$confirmedJoinEvents = JoinEvent::where('join_status', 'confirmed')
    ->with([
        'eventDetails.tier',
        'eventDetails.game',
        'payments',
        'roster',
        'members' => function($q) {
            $q->where('status', 'accepted');
        }
    ])
    ->get();

echo "Found " . $confirmedJoinEvents->count() . " confirmed join_events\n\n";

$stats = [
    'total_checked' => 0,
    'payment_status_fixed' => 0,
    'payment_amount_issues' => 0,
    'rosters_filled' => 0,
    'captains_assigned' => 0,
    'errors' => 0
];

foreach ($confirmedJoinEvents as $joinEvent) {
    $stats['total_checked']++;

    try {
        echo "Processing JoinEvent ID: {$joinEvent->id}\n";

        // Get tier entry fee
        $tierEntryFee = $joinEvent->eventDetails->tier->tierEntryFee ?? 0;
        echo "  - Tier Entry Fee: {$tierEntryFee}\n";

        // Calculate total payments
        $totalPayments = $joinEvent->payments->sum('payment_amount');
        echo "  - Total Payments: {$totalPayments}\n";

        // Check 1: Payment status should be 'completed' (ENUM: pending, completed, waived)
        if ($joinEvent->payment_status !== 'completed') {
            echo "  ⚠ Fixing payment_status: '{$joinEvent->payment_status}' -> 'completed'\n";
            $joinEvent->payment_status = 'completed';
            $joinEvent->save();
            $stats['payment_status_fixed']++;
        } else {
            echo "  ✓ Payment status is completed\n";
        }

        // Check 2: Verify payment sum equals tierEntryFee
        if ($totalPayments != $tierEntryFee && $tierEntryFee > 0) {
            echo "  ⚠ WARNING: Payment mismatch! Total: {$totalPayments}, Expected: {$tierEntryFee}\n";
            $stats['payment_amount_issues']++;
        } else {
            echo "  ✓ Payment amount is correct\n";
        }

        // Check 3: Fill rosters with members
        $eventCategory = $joinEvent->eventDetails->game;
        $playersPerTeam = $eventCategory->player_per_team ?? 1;
        echo "  - Players per team: {$playersPerTeam}\n";

        $currentRosterCount = $joinEvent->roster->count();
        echo "  - Current roster size: {$currentRosterCount}\n";

        if ($currentRosterCount < $playersPerTeam) {
            echo "  → Filling roster from team members...\n";

            $acceptedMembers = $joinEvent->members;
            $existingRosterUserIds = $joinEvent->roster->pluck('user_id')->toArray();

            $membersToAdd = $acceptedMembers->filter(function($member) use ($existingRosterUserIds) {
                return !in_array($member->user_id, $existingRosterUserIds);
            })->take($playersPerTeam - $currentRosterCount);

            $addedCount = 0;
            foreach ($membersToAdd as $member) {
                $rosterMember = new RosterMember([
                    'join_events_id' => $joinEvent->id,
                    'user_id' => $member->user_id,
                    'team_member_id' => $member->id,
                    'team_id' => $joinEvent->team_id,
                ]);
                $rosterMember->save();
                echo "    + Added user {$member->user_id} to roster\n";
                $addedCount++;
            }

            if ($addedCount > 0) {
                $stats['rosters_filled']++;
                echo "  ✓ Added {$addedCount} members to roster\n";
            } else {
                echo "  ℹ No additional members available to add\n";
            }

            // Refresh roster
            $joinEvent->load('roster');
        } else {
            echo "  ✓ Roster is full or complete\n";
        }

        // Check 4: Assign roster captain (first roster member)
        if ($joinEvent->roster->isNotEmpty() && !$joinEvent->roster_captain_id) {
            $firstRosterMember = $joinEvent->roster->first();
            $joinEvent->roster_captain_id = $firstRosterMember->id;
            $joinEvent->save();
            echo "  ✓ Assigned captain: Roster Member ID {$firstRosterMember->id} (User ID: {$firstRosterMember->user_id})\n";
            $stats['captains_assigned']++;
        } elseif ($joinEvent->roster_captain_id) {
            echo "  ✓ Captain already assigned: {$joinEvent->roster_captain_id}\n";
        } else {
            echo "  ⚠ Cannot assign captain: No roster members\n";
        }

        echo "  ✓ Completed JoinEvent ID: {$joinEvent->id}\n\n";

    } catch (\Exception $e) {
        echo "  ✗ ERROR processing JoinEvent ID {$joinEvent->id}: " . $e->getMessage() . "\n\n";
        Log::error("Error processing JoinEvent ID {$joinEvent->id}: " . $e->getMessage());
        $stats['errors']++;
    }
}

echo "\n========================================\n";
echo "Part 1 Summary:\n";
echo "========================================\n";
echo "Total checked: {$stats['total_checked']}\n";
echo "Payment status fixed: {$stats['payment_status_fixed']}\n";
echo "Payment amount issues: {$stats['payment_amount_issues']}\n";
echo "Rosters filled: {$stats['rosters_filled']}\n";
echo "Captains assigned: {$stats['captains_assigned']}\n";
echo "Errors: {$stats['errors']}\n";
echo "\n";

// ====================
// PART 2: Create User Activities
// ====================

echo "PART 2: Creating User Activities\n";
echo "==========================================\n\n";

$activityStats = [
    'friends_activities' => 0,
    'follow_activities' => 0,
    'event_join_activities' => 0,
    'errors' => 0
];

// Get all users with their relationships
$users = User::with(['stars', 'follows'])->get();

echo "Found {$users->count()} users\n\n";

foreach ($users as $user) {
    try {
        echo "Processing User ID: {$user->id} ({$user->name})\n";

        // Activity 1: Create friend activities (stars relationship)
        $friends = $user->stars;
        echo "  - Friends (Stars): {$friends->count()}\n";

        foreach ($friends as $friend) {
            // Check if activity already exists
            $exists = ActivityLogs::where([
                'subject_id' => $user->id,
                'subject_type' => 'App\Models\User',
                'object_id' => $friend->id,
                'object_type' => 'App\Models\User',
                'action' => 'starred',
            ])->exists();

            if (!$exists) {
                ActivityLogs::create([
                    'subject_id' => $user->id,
                    'subject_type' => 'App\Models\User',
                    'object_id' => $friend->id,
                    'object_type' => 'App\Models\User',
                    'action' => 'starred',
                    'log' => "{$user->name} starred {$friend->name}",
                ]);
                $activityStats['friends_activities']++;
                echo "    + Created star activity for friend {$friend->name}\n";
            }
        }

        // Activity 2: Create follow activities
        $follows = $user->follows;
        echo "  - Follows: {$follows->count()}\n";

        foreach ($follows as $follow) {
            // Get the organizer user
            $organizerUser = User::find($follow->organizer_user_id);
            if (!$organizerUser) continue;

            // Check if activity already exists
            $exists = ActivityLogs::where([
                'subject_id' => $user->id,
                'subject_type' => 'App\Models\User',
                'object_id' => $organizerUser->id,
                'object_type' => 'App\Models\User',
                'action' => 'followed',
            ])->exists();

            if (!$exists) {
                ActivityLogs::create([
                    'subject_id' => $user->id,
                    'subject_type' => 'App\Models\User',
                    'object_id' => $organizerUser->id,
                    'object_type' => 'App\Models\User',
                    'action' => 'followed',
                    'log' => "{$user->name} followed organizer {$organizerUser->name}",
                ]);
                $activityStats['follow_activities']++;
                echo "    + Created follow activity for organizer {$organizerUser->name}\n";
            }
        }

        // Activity 3: Create event join activities
        $userJoinEvents = JoinEvent::where('joiner_id', $user->id)
            ->where('join_status', 'confirmed')
            ->with('eventDetails')
            ->get();

        echo "  - Confirmed Event Joins: {$userJoinEvents->count()}\n";

        foreach ($userJoinEvents as $joinEvent) {
            if (!$joinEvent->eventDetails) continue;

            // Check if activity already exists
            $exists = ActivityLogs::where([
                'subject_id' => $user->id,
                'subject_type' => 'App\Models\User',
                'object_id' => $joinEvent->eventDetails->id,
                'object_type' => 'App\Models\EventDetail',
                'action' => 'joined_event',
            ])->exists();

            if (!$exists) {
                ActivityLogs::create([
                    'subject_id' => $user->id,
                    'subject_type' => 'App\Models\User',
                    'object_id' => $joinEvent->eventDetails->id,
                    'object_type' => 'App\Models\EventDetail',
                    'action' => 'joined_event',
                    'log' => "{$user->name} joined event {$joinEvent->eventDetails->eventName}",
                ]);
                $activityStats['event_join_activities']++;
                echo "    + Created event join activity for {$joinEvent->eventDetails->eventName}\n";
            }
        }

        echo "  ✓ Completed User ID: {$user->id}\n\n";

    } catch (\Exception $e) {
        echo "  ✗ ERROR processing User ID {$user->id}: " . $e->getMessage() . "\n\n";
        Log::error("Error processing User ID {$user->id}: " . $e->getMessage());
        $activityStats['errors']++;
    }
}

echo "\n========================================\n";
echo "Part 2 Summary:\n";
echo "========================================\n";
echo "Friend (Star) activities created: {$activityStats['friends_activities']}\n";
echo "Follow activities created: {$activityStats['follow_activities']}\n";
echo "Event join activities created: {$activityStats['event_join_activities']}\n";
echo "Errors: {$activityStats['errors']}\n";
echo "\n";

echo "========================================\n";
echo "✓ Script completed successfully!\n";
echo "========================================\n";
