<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JoinEvent;
use App\Models\RosterMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\JoinEvent>
 */
final class JoinEventFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = JoinEvent::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'event_details_id' => \App\Models\EventDetail::factory(),
            'team_id' => \App\Models\Team::factory(),
            'joiner_id' => \App\Models\User::factory(),
            'joiner_participant_id' => fake()->randomNumber(),
            'payment_status' => fake()->randomElement(['pending', 'completed', 'waived']),
            'join_status' => fake()->randomElement(['canceled', 'confirmed', 'pending']),
            'vote_starter_id' => \App\Models\User::factory(),
            'vote_ongoing' => fake()->optional()->randomNumber(1),
            'roster_captain_id' => \App\Models\RosterMember::factory(),
        ];
    }


    public function seed($options = [
        'event' => []
    ]) {
        // Store the events and teams
        $events = [];
        $organizers = [];
        $eventFactory = new EventDetailFactory();
        
        $result = $eventFactory->seed(0, $options['event']);
        $events[] = $result['event'];
        $organizers[] = $result['organizer'];
        
        $teamMemberFactory = new TeamMemberFactory();
        $teamResult = $teamMemberFactory->seed();
        $teams = $teamResult['teams'];
        $members = $teamResult['members'];
        foreach ($teams as $team) { 
            $team->load(['user', 'user.participant']);
        }

        $joinEvents = [];
        foreach ($events as $event) {
            foreach ($teams as $team) {
                
                $joinEvent = JoinEvent::updateOrCreate([
                    'event_details_id' => $event->id,
                    'team_id' => $team->id,
                ],
                
                [
                    'team_id' => $team->id,
                    'joiner_id' => $team->creator_id,
                    'joiner_participant_id' => $team->user->participant->id,
                    'payment_status' => 'completed', 
                    'join_status' => 'confirmed',    
                    'vote_ongoing' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $joinEvents[] = $joinEvent;
            }
        }

        foreach ($joinEvents as $joinEvent) {
            foreach ($members as $teamMember) {
             
                // Create new roster member
                RosterMember::updateOrCreate([
                    'user_id' => $teamMember->user_id,
                    'join_events_id' => $joinEvent->id,
                    'team_member_id' => $teamMember->id,
                ],[
                    'team_id' => $joinEvent->team_id,
                    'vote_to_quit' => false,
                ]);
                
            }
        }
        
        return [
            'events' => $events,
            'joinEvents' => $joinEvents,
            'organizer' => $organizers,
            ...$teamResult
        ];
    }
}
