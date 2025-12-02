<?php

// Script to setup social connections for users
// Run with: php artisan tinker < setup_social_connections.php

use App\Models\User;
use App\Models\Friend;
use App\Models\ParticipantFollow;
use App\Models\OrganizerFollow;
use App\Models\Like;
use App\Models\EventDetail;

echo "Starting social connections setup...\n";

// Get all participants
$participants = User::where('role', 'PARTICIPANT')->get();
echo "Found " . $participants->count() . " participants\n";

// Get all organizers
$organizers = User::where('role', 'ORGANIZER')->get();
echo "Found " . $organizers->count() . " organizers\n";

// Get all events
$events = EventDetail::all();
echo "Found " . $events->count() . " events\n";

$friendshipsCreated = 0;
$participantFollowsCreated = 0;
$organizerFollowsCreated = 0;
$likesCreated = 0;

// Process each participant
foreach ($participants as $participant) {
    echo "\nProcessing participant: {$participant->name} (ID: {$participant->id})\n";

    // 1. Create 3 friendships with other participants
    $existingFriends = Friend::where(function($query) use ($participant) {
        $query->where('user1_id', $participant->id)
              ->orWhere('user2_id', $participant->id);
    })->where('status', 'accepted')->count();

    $friendsNeeded = max(0, 3 - $existingFriends);
    echo "  Existing friends: {$existingFriends}, needs {$friendsNeeded} more\n";

    if ($friendsNeeded > 0) {
        // Get potential friends (other participants, excluding self and existing friends)
        $existingFriendIds = Friend::where(function($query) use ($participant) {
            $query->where('user1_id', $participant->id)
                  ->orWhere('user2_id', $participant->id);
        })->get()->map(function($friend) use ($participant) {
            return $friend->user1_id == $participant->id ? $friend->user2_id : $friend->user1_id;
        })->toArray();

        $potentialFriends = User::where('role', 'PARTICIPANT')
            ->where('id', '!=', $participant->id)
            ->whereNotIn('id', $existingFriendIds)
            ->inRandomOrder()
            ->limit($friendsNeeded)
            ->get();

        foreach ($potentialFriends as $friend) {
            Friend::create([
                'user1_id' => $participant->id,
                'user2_id' => $friend->id,
                'status' => 'accepted',
                'actor_id' => $participant->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $friendshipsCreated++;
            echo "    Created friendship with {$friend->name}\n";
        }
    }

    // 2. Create 3 participant follows (participants following other participants)
    $existingFollows = ParticipantFollow::where('participant_follower', $participant->id)->count();
    $followsNeeded = max(0, 3 - $existingFollows);
    echo "  Existing follows: {$existingFollows}, needs {$followsNeeded} more\n";

    if ($followsNeeded > 0) {
        $existingFollowIds = ParticipantFollow::where('participant_follower', $participant->id)
            ->pluck('participant_followee')
            ->toArray();

        $potentialFollowees = User::where('role', 'PARTICIPANT')
            ->where('id', '!=', $participant->id)
            ->whereNotIn('id', $existingFollowIds)
            ->inRandomOrder()
            ->limit($followsNeeded)
            ->get();

        foreach ($potentialFollowees as $followee) {
            ParticipantFollow::create([
                'participant_follower' => $participant->id,
                'participant_followee' => $followee->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $participantFollowsCreated++;
            echo "    Following participant {$followee->name}\n";
        }
    }
}

// Process each organizer - give them 3 participant followers
foreach ($organizers as $organizer) {
    echo "\nProcessing organizer: {$organizer->name} (ID: {$organizer->id})\n";

    $existingFollowers = OrganizerFollow::where('organizer_user_id', $organizer->id)->count();
    $followersNeeded = max(0, 3 - $existingFollowers);
    echo "  Existing followers: {$existingFollowers}, needs {$followersNeeded} more\n";

    if ($followersNeeded > 0) {
        $existingFollowerIds = OrganizerFollow::where('organizer_user_id', $organizer->id)
            ->pluck('participant_user_id')
            ->toArray();

        $potentialFollowers = User::where('role', 'PARTICIPANT')
            ->whereNotIn('id', $existingFollowerIds)
            ->inRandomOrder()
            ->limit($followersNeeded)
            ->get();

        foreach ($potentialFollowers as $follower) {
            OrganizerFollow::create([
                'organizer_user_id' => $organizer->id,
                'participant_user_id' => $follower->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $organizerFollowsCreated++;
            echo "    Added follower {$follower->name}\n";
        }
    }
}

// Process each event - give them likes from random participants
foreach ($events as $event) {
    echo "\nProcessing event: {$event->name} (ID: {$event->id})\n";

    // Get existing likes for this event
    $existingLikes = Like::where('event_id', $event->id)->count();

    // Determine how many likes to add (between 3-10 random likes if less than 3)
    if ($existingLikes < 3) {
        $likesNeeded = rand(3, 10);
        echo "  Existing likes: {$existingLikes}, adding {$likesNeeded} likes\n";

        $existingLikerIds = Like::where('event_id', $event->id)
            ->pluck('user_id')
            ->toArray();

        $potentialLikers = User::where('role', 'PARTICIPANT')
            ->whereNotIn('id', $existingLikerIds)
            ->inRandomOrder()
            ->limit($likesNeeded)
            ->get();

        foreach ($potentialLikers as $liker) {
            Like::create([
                'user_id' => $liker->id,
                'event_id' => $event->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $likesCreated++;
            echo "    Added like from {$liker->name}\n";
        }
    } else {
        echo "  Event already has {$existingLikes} likes, skipping\n";
    }
}

echo "\n=== Summary ===\n";
echo "Friendships created: {$friendshipsCreated}\n";
echo "Participant follows created: {$participantFollowsCreated}\n";
echo "Organizer follows created: {$organizerFollowsCreated}\n";
echo "Event likes created: {$likesCreated}\n";
echo "\nDone!\n";
