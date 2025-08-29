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

    public function seed($numberOfUsers = 10, $playersPerTeam = 5)
    {
        // Team names array
        $teamNames = [
            'Fauget Esports Gaming Elite', 'Valiant Warriors of Glory', 'Putrajaya Lions Gaming Squad', 'Skul Empire Rising Force', 'Entropy Esports Pro Team', 'Elusivity Gaming Masters', 'Desert Lions Elite Squad', 'Lions Mane Gaming Club',
            'Evos Champions United', 'Wolves of Legend Gaming', 'GOAT Esports Champions', 'Team Demon Strike Force', 'Granerz Elite Gaming', 'Giraffe Supra Pro Team', 'Hornets Gaming Squadron', 'Thunder Bolt Champions'
        ];

        $chineseNames = [
            'Lim Wei Ming Chen', 'Tan Li Jun', 'Wong Chen Wei Ming Sheng', 'Lee Kai Jin', 'Ng Zhi Wei Hao', 'Ong Jie Ming',
            'Teo Hui Wei Jun', 'Low Ming Jun Heng', 'Goh Xin Wei', 'Koh Wei Ming Jian', 'Sia Jia Wei Quan', 'Tay Yen Ming',
            'Ho Jin Wei Long', 'Foo Yi Ming Kai', 'Gan Jun Wei Shun', 'Mah Lin Ming Xuan', 'Chong Ming Wei Yang', 'Yeoh Wei Jun Feng',
            'Lau Hui Ming Rui', 'Chin Zhi Wei Hao', 'Neo Kai Ming Jun', 'Teo Jun Wei Yang', 'Ang Yi Ming Zhen', 'Chua Wei Jun Bin',
            'Khoo Ming Wei Sheng', 'Seah Jie Ming Quan', 'Pang Hui Wei Long', 'Quek Zhi Ming Kai', 'Lim Kai Wei Jian', 'Tan Jun Ming Xuan',
            'Wong Yi Wei Yang', 'Lee Wei Ming Rui', 'Ng Hui Jun Feng', 'Ong Wei Zhi Hao', 'Teo Ming Jun Bin', 'Low Wei Jie Sheng'
        ];

        $malayNames = [
            'Ahmad Rizwan Hassan', 'Mohd Haziq Bin Rahman', 'Amin Faisal Ahmad Zaki', 'Zafran Aidil Ismail', 'Irfan Hakim Bin Omar',
            'Danial Haris Yusof', 'Luqman Zaki Aziz Rahman', 'Azlan Nabil Rashid', 'Alif Rahman Bin Karim Shah', 'Faris Iman Zain',
            'Syafiq Arif Halim Nazri', 'Hafiz Zain Nasir', 'Adib Nazim Bin Farid Amin', 'Farid Azim Iqbal', 'Iqbal Nasir Shafiq Haris',
            'Shafiq Danish Aiman', 'Aiman Yusuf Bin Nazar Fikri', 'Nazar Fikri Idris', 'Idris Hakeem Iskandar Jalal', 'Iskandar Rafiq Jalal',
            'Jalal Nazri Bin Kamil Sabri', 'Kamil Sabri Latif', 'Latif Zamri Malik Yazid', 'Malik Yazid Nasir', 'Nasir Zahid Bin Omar Rashid',
            'Omar Rashid Qasim', 'Qasim Saiful Razak Tariq', 'Razak Tariq Samir', 'Samir Walid Bin Tariq Yasin', 'Tariq Yasin Umar',
            'Umar Zulkifli Wafiq Yamin', 'Wafiq Yasmin Yamin', 'Yamin Zafir Bin Zainal Azhar', 'Zainal Azhar Anas', 'Anas Bashir Faiz Ghani',
            'Faiz Ghani Haikal', 'Haikal Jamil Bin Jihan Karim', 'Jihan Karim Lukman', 'Lukman Mahir Nazmi Omar', 'Nazmi Omar Ahmad Rizki',
            'Arman Haiqal Bin Zulhilmi', 'Zulhilmi Asyraf Rahman Nazim', 'Nazim Hazwan Fadhil', 'Fadhil Izzat Bin Harith Amin'
        ];

        $tamilNames = [
            'Raj Kumar Suresh', 'Siva Nathan Raman', 'Kiran Dass Murugan Selvam', 'Arjun Selvan Kumar',
            'Vikram Raman  Nathan Raj', 'Deepak Murugan Dass', 'Karthik Sundaram  Selvan Kumar', 'Selvam Krishnan Vikram',
            'Arun Prakash A/L Venkatesh', 'Venkatesh Mahesh Ganesh', 'Ganesh Ramesh A/L Suresh Kumar', 'Suresh Dinesh Rajesh'
        ];

        // Combine all names with proper distribution
        $allNames = array_merge($chineseNames, $malayNames, $tamilNames);

        // Available team images
        $teamImages = [
            'images/team/0.webp', 'images/team/1.jpg', 'images/team/2.avif', 'images/team/3.avif', 
            'images/team/4.jpg', 'images/team/5.jpg', 'images/team/6.avif', 'images/team/7.jpg',
            'images/team/8.png', 'images/team/9.webp', 'images/team/10.jpg', 'images/team/12.jpg',
            'images/team/13.jpg', 'images/team/14.png', 'images/team/15.jpg', 'images/team/2.avif'
        ];

        $participants = [];

        for ($i = 1; $i <= $numberOfUsers; $i++) {
            $nameIndex = ($i - 1) % count($allNames);
            $userName = $allNames[$nameIndex];
            
            $user = User::updateOrCreate([
                'email' => "tester$i@driftwood.gg",
            ], [
                'name' => $userName,
                'email_verified_at' => now(),
                'password' => bcrypt('123456'),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role' => 'PARTICIPANT',
                'status' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $participant = Participant::updateOrCreate([
                'user_id' => $user->id,
            ],
                [
                    'nickname' => null,
                    'age' => fake()->numberBetween(13, 60),
                    'isAgeVisible' => 1,
                    'region_flag' => '🇲🇾',
                    'region_name' => 'Malaysia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            NotificationCounter::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'user_id' => $user->id,
                'social_count' => 0,
                'teams_count' => 0,
                'event_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $participants[] = $user;
        }

        $noOfConTeams = intval(ceil($numberOfUsers / $playersPerTeam));
        
        $creatorUsers = [];
        for ($i = 0; $i < $noOfConTeams; $i++) {
            $creatorIndex = $i * $playersPerTeam;
            if ($creatorIndex < count($participants)) {
                $creatorUsers[] = $participants[$creatorIndex];
            }
        }

        $teams = [];
        
        for ($i = 0; $i < $noOfConTeams; $i++) {
            $teamNameIndex = $i % count($teamNames);
            $teamImageIndex = $i % count($teamImages);
            $teamName = $teamNames[$teamNameIndex];
            
            $team = Team::updateOrCreate([
                'teamName' => $teamName,
            ],
                [
                    'creator_id' => $creatorUsers[$i]->id,
                    'teamDescription' => "Elite esports team competing at the highest level",
                    'teamBanner' => $teamImages[$teamImageIndex],
                    'country' => 'MY',
                    'country_name' => 'Malaysia',
                    'country_flag' => '🇲🇾',
                ]);
            $team->save();
            $teams[] = $team;
        }

        $members = [];
        $participantsCount = count($participants);
        $teamsCount = count($teams);

        for ($teamIndex = 0; $teamIndex < $teamsCount; $teamIndex++) {
            $team = $teams[$teamIndex];

            $startIndex = $teamIndex * $playersPerTeam;
            $endIndex = min($startIndex + $playersPerTeam, $participantsCount);

            for ($i = $startIndex; $i < $endIndex; $i++) {
                $participant = $participants[$i];

                $member = TeamMember::updateOrCreate(
                    [
                        'user_id' => $participant->id,
                        'team_id' => $team->id,
                    ],
                    [
                        'status' => 'accepted',
                        'actor' => 'team',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $members[] = $member;
            }
        }

        return [
            'participants' => $participants,
            'teams' => $teams,
            'members' => $members,
        ];
    }
}
