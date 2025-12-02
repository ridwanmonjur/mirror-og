<?php

use App\Models\User;
use App\Models\Friend;
use App\Models\ParticipantFollow;
use App\Models\OrganizerFollow;
use App\Models\Like;
use App\Models\EventDetail;

$stats = ['friendships' => 0, 'participantFollows' => 0, 'organizerFollows' => 0, 'likes' => 0];

// 1. Setup friendships for participants
$participants = User::where('role', 'PARTICIPANT')->get();
foreach ($participants as $participant) {
    $existingFriends = Friend::where(function($q) use ($participant) {
        $q->where('user1_id', $participant->id)->orWhere('user2_id', $participant->id);
    })->where('status', 'accepted')->count();

    $friendsNeeded = max(0, 3 - $existingFriends);
    if ($friendsNeeded > 0) {
        $existingFriendIds = Friend::where(function($q) use ($participant) {
            $q->where('user1_id', $participant->id)->orWhere('user2_id', $participant->id);
        })->get()->map(function($f) use ($participant) {
            return $f->user1_id == $participant->id ? $f->user2_id : $f->user1_id;
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
            ]);
            $stats['friendships']++;
        }
    }

    // 2. Setup participant follows
    $existingFollows = ParticipantFollow::where('participant_follower', $participant->id)->count();
    $followsNeeded = max(0, 3 - $existingFollows);
    if ($followsNeeded > 0) {
        $existingFollowIds = ParticipantFollow::where('participant_follower', $participant->id)
            ->pluck('participant_followee')->toArray();

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
            ]);
            $stats['participantFollows']++;
        }
    }
}

// 3. Setup organizer follows
$organizers = User::where('role', 'ORGANIZER')->get();
foreach ($organizers as $organizer) {
    $existingFollowers = OrganizerFollow::where('organizer_user_id', $organizer->id)->count();
    $followersNeeded = max(0, 3 - $existingFollowers);
    if ($followersNeeded > 0) {
        $existingFollowerIds = OrganizerFollow::where('organizer_user_id', $organizer->id)
            ->pluck('participant_user_id')->toArray();

        $potentialFollowers = User::where('role', 'PARTICIPANT')
            ->whereNotIn('id', $existingFollowerIds)
            ->inRandomOrder()
            ->limit($followersNeeded)
            ->get();

        foreach ($potentialFollowers as $follower) {
            OrganizerFollow::create([
                'organizer_user_id' => $organizer->id,
                'participant_user_id' => $follower->id,
            ]);
            $stats['organizerFollows']++;
        }
    }
}

// 4. Setup event likes
$events = EventDetail::all();
foreach ($events as $event) {
    $existingLikes = Like::where('event_id', $event->id)->count();
    if ($existingLikes < 3) {
        $likesNeeded = rand(3, 10);
        $existingLikerIds = Like::where('event_id', $event->id)->pluck('user_id')->toArray();

        $potentialLikers = User::where('role', 'PARTICIPANT')
            ->whereNotIn('id', $existingLikerIds)
            ->inRandomOrder()
            ->limit($likesNeeded)
            ->get();

        foreach ($potentialLikers as $liker) {
            Like::create([
                'user_id' => $liker->id,
                'event_id' => $event->id,
            ]);
            $stats['likes']++;
        }
    }
}

echo "=== Setup Complete ===\n";
echo "Friendships: {$stats['friendships']}\n";
echo "Participant Follows: {$stats['participantFollows']}\n";
echo "Organizer Follows: {$stats['organizerFollows']}\n";
echo "Event Likes: {$stats['likes']}\n";
