<?php

use App\Models\EventDetail;

// Define 12 new descriptions based on the tone of "Welcome to Driftwood: By The Beach"
$descriptions = [
    // Elite Chess Circuit - Chess
    18 => "Ready to outmaneuver your opponents? The Elite Chess Circuit is calling all strategic minds!

Join us for an intense chess competition where every move counts! This tournament brings together the sharpest players for tactical battles that will test your planning, patience, and prowess.

With an entry fee of just RM20 per player, 16 competitors will battle for a RM600 prize pool:

1st place - RM250
2nd place - RM175
3rd place - RM100
4th place - RM50
5th-8th place - RM25 each

Once you've registered, confirm your spot by completing payment before the deadline. All matches will follow standard chess tournament rules with a time control format to keep things exciting!

Registration opens now and closes on January 5th, 2025. Payment confirmation required by January 7th, 2025. If the tournament doesn't reach minimum participants, full refunds will be issued.

Matches can be scheduled flexibly within designated time windows - coordinate with your opponent to find the perfect time. Remember to report your results promptly after each game!

Will you claim the title of Elite Champion?",

    // Premier Football League - FIFA
    20 => "Kick off your FIFA journey with the Premier Football League!

Are you ready to dominate the virtual pitch? Join us for an action-packed FIFA tournament where skill, strategy, and quick reflexes will determine who rises to the top!

With an entry fee of only RM15 per player, 32 players will compete for a massive RM800 prize pool:

1st place - RM300
2nd place - RM200
3rd place - RM150
4th place - RM75
5th place - RM50
6th-8th place - RM25 each

After signing up, secure your spot by completing the entry fee payment. You'll compete in intense matches using the latest FIFA roster updates!

Signups are open until July 27th, 2025. Payment confirmation deadline is July 29th, 2025. Don't worry - if we don't hit our minimum player count, you'll receive a full refund.

Schedule your matches with opponents during the allocated time windows. Communication is key! Make sure to report match results within the reporting period to avoid penalties.

Are you the next FIFA champion?",

    // Phantom Force Championship - Valorant
    22 => "Agents, assemble! The Phantom Force Championship needs your tactical expertise!

Step into the battlefield for an explosive Valorant tournament! Gather your squad of 5 and showcase your aim, strategy, and teamwork in this competitive 5v5 tournament format.

With a team entry fee of just RM30, 12 teams will fight for a RM750 prize pool:

1st place - RM300
2nd place - RM200
3rd place - RM150
4th place - RM75
5th-6th place - RM37.50 each

Form your team and register, then confirm your participation by selecting your tournament roster (you can have substitutes!) and paying the full entry fee. Split it however works for your team!

Registration closes on May 13th, 2025. Roster lock and payment confirmation by May 15th, 2025. Full refunds provided if tournament doesn't proceed.

Coordinate with opposing teams to schedule matches within the designated time windows. All games will be played in a competitive map pool. Report your results on time to keep the tournament running smoothly!

Can your team clutch the championship?",

    // Global Offensive Masters - CS GO
    23 => "Counter-terrorists and terrorists unite! The Global Offensive Masters tournament is here!

Join the ultimate CS:GO showdown! Assemble your 5-player team and prove you have what it takes to be crowned masters of the game. Compete in thrilling best-of-one (BO1) matches leading to BO3 finals!

With a team entry fee of RM35, 16 teams will battle for a RM1,000 prize pool:

1st place - RM400
2nd place - RM250
3rd place - RM175
4th place - RM100
5th-8th place - RM50 each

Once registered, lock in your 5-player roster (subs allowed!) and complete payment to confirm. The entry fee can be split among teammates however you choose!

Signups close February 18th, 2025. Confirm your slot by February 20th, 2025. If minimum teams aren't reached, full refunds will be issued.

Schedule matches with opponents within time windows. All matches use active duty map pool with knife round for side selection. Submit match results promptly!

Will you defuse the competition?",

    // Legends Clash League - MLBB
    24 => "Welcome to the battlefield, Legends! The Legends Clash League is calling mobile MOBA champions!

Gather your squad for the ultimate Mobile Legends: Bang Bang tournament! Form your 5-player team and dominate the Land of Dawn in this competitive tournament featuring intense 5v5 battles.

With a team entry fee of only RM25, 10 teams will compete for a RM550 prize pool:

1st place - RM225
2nd place - RM150
3rd place - RM100
4th place - RM50
5th place - RM25

Register your team, then confirm by selecting your tournament roster (up to 2 subs allowed) and completing payment. Divide the entry fee among your team as you see fit!

Registration period ends March 5th, 2025. Roster and payment confirmation by March 7th, 2025. Full refunds if tournament is cancelled.

Coordinate match schedules with opponents during allocated windows. All games follow standard ranked rules with draft pick mode. Report results within the specified timeframe!

Can you achieve Mythical Glory in this tournament?",

    // Summer Valorant Cup - Valorant
    25 => "The Summer Valorant Cup is heating up! Time to show off your tactical FPS skills!

Calling all Valorant enthusiasts! Form your dream team of 5 and compete in this summer showdown. Navigate through single elimination rounds with best-of-three matches in the finals!

With a team entry fee of RM28, 8 teams will compete for a RM500 prize pool:

1st place - RM200
2nd place - RM140
3rd place - RM90
4th place - RM50
5th-6th place - RM20 each

After registration, lock in your roster (substitutes permitted) and complete the entry fee payment. Your team decides how to split the cost!

Signups close January 24th, 2025. Confirmation deadline is January 26th, 2025. Cancellation means full refunds for everyone.

Work with opposing teams to schedule within time windows. Matches use competitive map rotation. Make sure to report scores on time!

Ready to ace your way to victory?",

    // Legends Combat Series - Honor of Kings (was Apex)
    26 => "Heroes arise! The Legends Combat Series beckons the bravest MOBA warriors!

Join us for an epic Honor of Kings tournament! Assemble your 5-player team and prove your mastery in strategic team battles. Navigate through double elimination brackets with BO3 format!

With a team entry fee of RM30, 8 teams will fight for a RM550 prize pool:

1st place - RM220
2nd place - RM150
3rd place - RM100
4th place - RM50
5th-6th place - RM30 each

Register and then confirm by finalizing your roster (subs welcome!) and paying the entry fee. Split it among teammates as preferred!

Registration ends April 1st, 2025. Final confirmation by April 3rd, 2025. Full refunds issued if tournament doesn't proceed.

Schedule matches with opponents flexibly within designated periods. All games use draft pick mode with competitive settings. Report results promptly after each match!

Will your team achieve legendary status?",

    // Rift Masters Series - League of Legends: Wild Rift
    27 => "Summoners! The Rift Masters Series is your chance to prove dominance on Wild Rift!

Gather your team for the ultimate League of Legends: Wild Rift mobile tournament! Form your 5-player squad and battle through double elimination brackets in this competitive showcase.

With a team entry fee of just RM32, 12 teams will compete for a RM750 prize pool:

1st place - RM300
2nd place - RM200
3rd place - RM125
4th place - RM75
5th-6th place - RM50 each

After signing up, confirm your spot by locking your roster (up to 2 subs allowed) and completing payment. Decide among yourselves how to split the fee!

Signups close June 5th, 2025. Confirmation required by June 7th, 2025. Tournament cancellation means full refunds.

Coordinate with opponents to schedule matches in allocated time slots. All matches use draft pick with competitive bans. Submit results within reporting windows!

Can you master the rift?",

    // Spring 8-Team Battle - FIFA
    61 => "Spring into action with the Spring 8-Team Battle!

Get ready for fast-paced FIFA team competition! This unique tournament features 8 teams of 3 players each, competing in a team-based format where coordination and individual skill both matter.

With a team entry fee of RM20, 8 teams (24 players total) will compete for a RM400 prize pool:

1st place - RM160
2nd place - RM110
3rd place - RM70
4th place - RM40
5th-6th place - RM20 each

Register your 3-player team, then confirm by completing payment and roster selection. Your team chooses how to divide the entry fee!

Registration closes March 31st, 2025. Payment confirmation by April 2nd, 2025. Full refunds if minimum teams aren't met.

Schedule matches flexibly with opponents during designated windows. Each matchup is best-of-three with different team members playing. Report all results promptly!

Will your trio reign supreme?",

    // Autumn 16-Team Showdown - FIFA
    62 => "The Autumn 16-Team Showdown is here! Time to prove your FIFA supremacy!

Join us for an epic 16-team FIFA tournament! This showdown features single elimination with BO3 in semifinals and finals. Bring your A-game and rise through the ranks!

With a team entry fee of RM25, 16 teams of 2 players each will battle for a RM750 prize pool:

1st place - RM300
2nd place - RM200
3rd place - RM125
4th place - RM75
5th-8th place - RM50 each

Register your 2-player team, then lock in your roster and complete payment. Split the fee however works for your duo!

Signups close May 1st, 2025. Confirmation deadline May 3rd, 2025. Cancellations result in full refunds.

Coordinate match times with opponents within provided windows. Tag-team format means both players participate! Report results after each series!

Can your partnership dominate?",

    // Autumn 32-Team Invitational - FIFA
    63 => "The most prestigious FIFA event of the season! Welcome to the Autumn 32-Team Invitational!

This is it - our biggest FIFA tournament yet! 32 elite teams competing in an invitational format. Navigate through Swiss system group stages followed by single elimination playoffs!

With a team entry fee of RM40, 32 teams of 4 players each will compete for a massive RM2,000 prize pool:

1st place - RM800
2nd place - RM500
3rd place - RM300
4th place - RM200
5th-8th place - RM100 each
9th-16th place - RM50 each

Register your 4-player squad, select your starting lineup (1 sub allowed), and complete payment. Decide as a team how to split the entry fee!

This is an invitational event - signups close June 23rd, 2025. Confirmation required by June 25th, 2025. Full refunds if tournament doesn't proceed.

Match scheduling is crucial with multiple rounds. Coordinate carefully with opponents. Different team members can play different matches. Report results accurately and on time!

Do you have what it takes to win the Invitational?"
];

echo "=== UPDATING EVENT DESCRIPTIONS ===\n\n";

$updated = 0;
foreach ($descriptions as $eventId => $description) {
    $event = EventDetail::find($eventId);

    if ($event) {
        $event->eventDescription = $description;
        $event->save();

        echo "✓ Updated Event ID {$eventId}: {$event->eventName}\n";
        $updated++;
    } else {
        echo "✗ Event ID {$eventId} not found\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Successfully updated {$updated} event descriptions!\n";
