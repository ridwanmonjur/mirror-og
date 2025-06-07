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
        
        for ($i = 1; $i <= 10; $i++) {
            $user = User::updateOrCreate([
                'email' => "tester$i@driftwood.gg",
            ],[
                'name' => "TestPlayer$i",
                'email_verified_at' => DB::raw('NOW()'),
                'password' => bcrypt('123456'),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role' => 'PARTICIPANT',
                'status' => null,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]);
            
            $participant = Participant::updateOrCreate([
                'user_id' => $user->id,
            ],
            [
                'nickname' => "TestPlayer$i",
                'age' => fake()->numberBetween(13, 60),
                'isAgeVisible' => 1,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]);
            
            NotificationCounter::updateOrCreate([
                'user_id' => $user->id,
            ],[
                'user_id' => $user->id,
                'social_count' => 0,
                'teams_count' => 0,
                'event_count' => 0,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]);
            
            $participants[] = $user;
        }
        
        $creatorUsers = [$participants[0], $participants[5]];
        
        $teams = [];

        for ($i = 0; $i <= 1; $i++) {
            $index = $i + 1;
            $team = Team::updateOrCreate([
                'teamName' => "Team $index"
            ],
            [
                'creator_id' => $creatorUsers[$i]->id, 
                'teamDescription' => "Description for Team $index",
                'teamBanner' => "images/team/team$i.png", 
                'country' => 'DZ',
                'country_name' => 'Algeria',
                'country_flag' => '🇩🇿',
            ]);
            $team->save();
            $teams[] = $team;
        }
        
        $members = [];
        $participantsCount = count($participants);
        $teamsCount = count($teams);
        $participantsPerTeam = 5;

        for ($teamIndex = 0; $teamIndex < min($teamsCount, 2); $teamIndex++) {
            $team = $teams[$teamIndex];
            
            $startIndex = $teamIndex * $participantsPerTeam;
            $endIndex = min($startIndex + $participantsPerTeam, $participantsCount);
            
            for ($i = $startIndex; $i < $endIndex; $i++) {
                $participant = $participants[$i];
                
                $member = TeamMember::updateOrCreate(
                    [
                        'user_id' => $participant->id,
                        'team_id' => $team->id,
                    ],
                    [
                        'status' => "accepted",
                        'actor' => 'team',
                        'created_at' => DB::raw('NOW()'),
                        'updated_at' => DB::raw('NOW()'),
                    ]
                );
                
                $members[] = $member;
            }
        }

        
        return [
            'participants' => $participants,
            'teams' => $teams,
            'members' => $members
        ];
    }
}
