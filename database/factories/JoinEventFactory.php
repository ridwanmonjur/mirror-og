<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JoinEvent;
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


    public function seed() {
        // Store the events and teams
        $events = [];
        $eventFactory = new EventDetailFactory();
        
        for ($i = 0; $i < 3; $i++) {
            $result = $eventFactory->seed($i);
            $events[] = $result['event'];
        }
        
        $teamMemberFactory = new TeamMemberFactory();
        $teamResult = $teamMemberFactory->seed();
        $teams = $teamResult['teams'];
        
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
                    'payment_status' => 'completed', // Accepted payment status
                    'join_status' => 'confirmed',    // Accepted join status
                    'vote_ongoing' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $joinEvents[] = $joinEvent;
            }
        }
        
        return [
            'events' => $events,
            'teams' => $teams,
            'joinEvents' => $joinEvents
        ];
    }
}
