<?php

use App\Models\EventDetail;
use Illuminate\Support\Str;

echo "=== RENAMING REMAINING EVENTS ===\n\n";

// Event ID 62 - Test 16-Team League (FIFA)
$event62 = EventDetail::find(62);
if ($event62) {
    $oldName62 = $event62->eventName;
    $newName62 = "Penang FIFA Champions League";

    // Update name and slug
    $event62->eventName = $newName62;
    $event62->slug = Str::slug($newName62);

    // Update description
    $event62->eventDescription = "Welcome to the Penang FIFA Champions League!

Get ready for an epic 16-team FIFA tournament taking place in the heart of Penang! This championship brings together the best FIFA players in a competitive single elimination format with BO3 semifinals and finals.

With a team entry fee of RM25, 16 teams of 2 players each will battle for a RM750 prize pool:

1st place - RM300
2nd place - RM200
3rd place - RM125
4th place - RM75
5th-8th place - RM50 each

Register your 2-player team, then lock in your roster and complete payment. Split the fee however works for your duo!

Signups close May 1st, 2025. Confirmation deadline May 3rd, 2025. Cancellations result in full refunds.

Coordinate match times with opponents within provided windows. Tag-team format means both players participate! Report results after each series!

Can your partnership conquer Penang?";

    $event62->save();

    echo "Event ID 62:\n";
    echo "  Old Name: {$oldName62}\n";
    echo "  New Name: {$newName62}\n";
    echo "  New Slug: {$event62->slug}\n";
    echo "  Description: Updated\n\n";
}

// Event ID 63 - Autumn 32-Team Invitational (FIFA)
$event63 = EventDetail::find(63);
if ($event63) {
    $oldName63 = $event63->eventName;
    $newName63 = "Kuching FIFA Grand Championship";

    // Update name and slug
    $event63->eventName = $newName63;
    $event63->slug = Str::slug($newName63);

    // Update description
    $event63->eventDescription = "The most prestigious FIFA event in Sarawak! Welcome to the Kuching FIFA Grand Championship!

This is the ultimate FIFA tournament experience! 32 elite teams competing in our largest invitational event. Navigate through Swiss system group stages followed by intense single elimination playoffs!

With a team entry fee of RM40, 32 teams of 4 players each will compete for a massive RM2,000 prize pool:

1st place - RM800
2nd place - RM500
3rd place - RM300
4th place - RM200
5th-8th place - RM100 each
9th-16th place - RM50 each

Register your 4-player squad, select your starting lineup (1 sub allowed), and complete payment. Decide as a team how to split the entry fee!

This is an invitational championship event - signups close June 23rd, 2025. Confirmation required by June 25th, 2025. Full refunds if tournament doesn't proceed.

Match scheduling is crucial with multiple rounds. Coordinate carefully with opponents. Different team members can play different matches. Report results accurately and on time!

Do you have what it takes to become the Kuching Grand Champion?";

    $event63->save();

    echo "Event ID 63:\n";
    echo "  Old Name: {$oldName63}\n";
    echo "  New Name: {$newName63}\n";
    echo "  New Slug: {$event63->slug}\n";
    echo "  Description: Updated\n\n";
}

echo "=== SUMMARY ===\n";
echo "Successfully renamed and updated descriptions for 2 events!\n";
