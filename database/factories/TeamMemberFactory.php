<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationCounter;
use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Str;

/**
 * @extends Factory<\App\Models\TeamMember>
 */
final class TeamMemberFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = TeamMember::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'team_id' => \App\Models\Team::factory(),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected', 'left']),
            'actor' => fake()->randomElement(['team', 'user']),
        ];
    }

    public function seed()
    {
        
        $participants = [];
        $participantModel = [];
        
        for ($i = 1; $i <= 20; $i++) {
            $user = User::firstOrCreate([
                'email' => "testplayer$i@example.com",
            ],[
                'name' => "TestPlayer$i",
                'email_verified_at' => now(),
                'password' => bcrypt('123456'),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role' => 'PARTICIPANT',
                'status' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $participant = Participant::create([
                'user_id' => $user->id,
                'nickname' => "TestPlayer$i",
                'age' => fake()->numberBetween(13, 60),
                'isAgeVisible' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            NotificationCounter::firstOrCreate([
                'user_id' => $user->id,
            ],[
                'user_id' => $user->id,
                'social_count' => 0,
                'teams_count' => 0,
                'event_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $participants[] = $user;
            $participantModel[] = $participant;
        }
        
        $creatorUsers = [$participants[0], $participants[6], $participants[11], $participants[15]];
        
        $teams = [];

        for ($i = 1; $i < 4; $i++) {
            $team = Team::firstOrCreate([
                'teamName' => "Team $i"
            ],
            [
                'creator_id' => $creatorUsers[$i]->id, 
                'teamDescription' => "Description for Team $i",
                'teamBanner' => "team$i.png", 
                'country' => 'DZ',
                'country_name' => 'Algeria',
                'country_flag' => '🇩🇿',
            ]);
            $team->save();
            $teams[] = $team;
        }
        
        $members = [];
        foreach ($teams as $team) {
            foreach ($participants as $participant) {
                $member = TeamMember::firstOrCreate([
                    'user_id' => $participant->id,
                ],
                [
                    'team_id' => $team->id,
                    'status' => "accepted",
                    'actor' => 'team',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $members[] = $member;
        }
        
        return [
            'participants' => $participants,
            'participantModel' => $participantModel,
            'teams' => $teams,
            'member' => $members
        ];
    }
}
