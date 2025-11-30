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

    public function definition(): array
    {
        return [];
    }

   

    public function seed($numberOfUsers = 10, $playersPerTeam = 5, $eventGame = 'Dota 2', $startUserIndex = 1)
    {
        // Game-specific configurations
        $gameConfigs = [
            'chess' => [
                'numberOfUsers' => 32,
                'playersPerTeam' => 1,
                'startUserIndex' => 1,
            ],
            'fifa' => [
                'numberOfUsers' => 32,
                'playersPerTeam' => 1,
                'startUserIndex' => 33,
            ],
            'dota 2' => [
                'numberOfUsers' => 40,
                'playersPerTeam' => 5,
                'startUserIndex' => 1,
            ],
            'cs:go' => [
                'numberOfUsers' => 80,
                'playersPerTeam' => 5,
                'startUserIndex' => 41,
            ],
            'csgo' => [
                'numberOfUsers' => 80,
                'playersPerTeam' => 5,
                'startUserIndex' => 41,
            ],
            'lol' => [
                'numberOfUsers' => 80,
                'playersPerTeam' => 5,
                'startUserIndex' => 41,
            ],
            'league of legends' => [
                'numberOfUsers' => 80,
                'playersPerTeam' => 5,
                'startUserIndex' => 41,
            ],
        ];

        // Apply game-specific config if available
        $gameLower = strtolower($eventGame);
        if (isset($gameConfigs[$gameLower])) {
            $config = $gameConfigs[$gameLower];
            $numberOfUsers = $config['numberOfUsers'];
            $playersPerTeam = $config['playersPerTeam'];
            $startUserIndex = $config['startUserIndex'];
        }

        // Team names array based on game type
        $dotaLolCsgoTeamNames = [
            'Fauget Esports Gaming Elite', 'Valiant Warriors of Glory', 'Putrajaya Lions Gaming Squad', 'Skul Empire Rising Force',
            'Entropy Esports Pro Team', 'Elusivity Gaming Masters', 'Desert Lions Elite Squad', 'Lions Mane Gaming Club',
            'Evos Champions United', 'Wolves of Legend Gaming', 'GOAT Esports Champions', 'Team Demon Strike Force',
            'Granerz Elite Gaming', 'Giraffe Supra Pro Team', 'Hornets Gaming Squadron', 'Thunder Bolt Champions',
            'Phoenix Rising Squad', 'Dragon Force Elite', 'Storm Breakers Pro', 'Titan Gaming Legends'
        ];

        $chessNames = [
            'Magnus Andersson', 'Fabiano Romano', 'Hikaru Chen', 'Wesley Kumar', 'Levon Martinez',
            'Anish Singh', 'Maxime Dubois', 'Viswanathan Krishnan', 'Sergey Petrov', 'Ian Thompson',
            'Vladimir Ivanov', 'Teimour Ahmadi', 'Richard Lee', 'Peter Williams', 'Boris Sokolov',
            'Veselin Garcia', 'Alexander Popov', 'Dmitry Volkov', 'Shakhriyar Rahman', 'Pentala Reddy',
            'Vassily Medvedev', 'Samuel Jackson', 'Jan-Krzysztof Nowak', 'Wang Hao Chen', 'Yu Yangyi',
            'Ding Liren Wang', 'Bu Xiangzhi Liu', 'Wei Yi Zhang', 'Artemiev Vladislav', 'Grischuk Sasha',
            'Karjakin Sergey', 'Nepomniachtchi Ian', 'Gelfand Boris'
        ];

        $fifaNames = [
            'Cristiano Silva', 'Lionel Rodriguez', 'Neymar Santos', 'Kylian Martinez', 'Mohamed Ahmed',
            'Robert Muller', 'Karim Benzema', 'Luka Petrovic', 'Kevin Schmidt', 'Virgil Jansen',
            'Sadio Traore', 'Harry Wilson', 'Raheem Anderson', 'Toni Fischer', 'Joshua Wagner',
            'Thibaut Dubois', 'Sergio Fernandez', 'Bruno Costa', 'Paul Laurent', 'Romelu Bakayoko',
            'Son Kim', 'Marcus Johnson', 'Jadon Brown', 'Phil Taylor', 'Jack Thompson',
            'Mason Davies', 'Bukayo Okafor', 'Erling Nielsen', 'Joao Oliveira', 'Rafael Pereira',
            'Gabriel Alves', 'Vinicius Lima', 'Rodri Moreno'
        ];

        // Determine team names based on game
        $teamNames = match($gameLower) {
            'chess' => $chessNames,
            'fifa' => $fifaNames,
            default => $dotaLolCsgoTeamNames
        };

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
            'Arman Haiqal Bin Zulhilmi', 'Zulhilmi Asyraf Rahman Nazim', 'Nazim Hazwan Fadhil', 'Fadhil Izzat Bin Harith Amin',
            'Amir Hariz Bin Abdullah', 'Hakimi Danish Azmi', 'Rizal Fikri Hashim', 'Zikri Imran Ibrahim', 'Hafizuddin Jamaluddin',
            'Akmal Affan Kadir', 'Hakim Amsyar Mansor', 'Azri Farhan Noor', 'Ikhwan Danish Osman', 'Ridhwan Harith Ramli',
            'Haikal Zulhilmi Salleh', 'Naim Haziq Shaari', 'Izzuddin Iqmal Tahir', 'Zharif Aiman Umar', 'Zakwan Hafiz Wahab',
            'Ammar Irfan Yusri', 'Hazwan Ariff Zahir', 'Shahril Hazim Azhar', 'Firdaus Haris Basri', 'Adha Nabil Daud',
            'Hanis Zafran Fadil', 'Ismail Haidir Ghazali', 'Khairul Imad Halim', 'Izwan Huzaifi Jaafar', 'Muaz Hakim Khalid',
            'Danish Iman Latiff', 'Afiq Nashwan Mahadi', 'Fakhri Amsyar Nordin', 'Syahmi Raiyan Othman', 'Haziq Zaki Ramzi',
            'Ikmal Danish Sazali', 'Najmi Haikal Taufik', 'Aqil Aiman Uzair', 'Syazwan Hariz Yaakob', 'Haffiz Ammar Zainol',
            'Azim Danial Ariffin', 'Hilmi Faizal Bakar', 'Asyraf Hakam Che Mat', 'Farhan Danish Din', 'Hasnul Fikri Elias'
        ];

        $tamilNames = [
            'Raj Kumar Suresh', 'Siva Nathan Raman', 'Kiran Dass Murugan Selvam', 'Arjun Selvan Kumar',
            'Vikram Raman Nathan Raj', 'Deepak Murugan Dass', 'Karthik Sundaram Selvan Kumar', 'Selvam Krishnan Vikram',
            'Arun Prakash A/L Venkatesh', 'Venkatesh Mahesh Ganesh', 'Ganesh Ramesh A/L Suresh Kumar', 'Suresh Dinesh Rajesh',
            'Anand Kumar Ravi', 'Ravi Shankar Mohan', 'Mohan Babu Krishna', 'Krishna Murthy Srinivas',
            'Srinivas Reddy Balaji', 'Balaji Natarajan Aravind', 'Aravind Subramanian Praveen', 'Praveen Kumar Naveen',
            'Naveen Raj Karthikeyan', 'Karthikeyan Sundaram Manoj', 'Manoj Kumar Senthil', 'Senthil Nathan Vignesh',
            'Vignesh Ramesh Ashok', 'Ashok Kumar Dinesh', 'Dinesh Raj Gopal', 'Gopal Krishna Hari'
        ];

        // Additional international names for variety
        $internationalNames = [
            'Alex Chen Rodriguez', 'Marcus Lee Anderson', 'Ryan Tan Williams', 'Daniel Wong Martinez',
            'Kevin Lim Thompson', 'Jason Ng Garcia', 'David Ong Wilson', 'Michael Teo Moore',
            'Christopher Low Taylor', 'Steven Goh Jackson', 'Andrew Koh White', 'Matthew Sia Harris',
            'Joshua Tay Martin', 'Brandon Ho Thompson', 'Tyler Foo Garcia', 'Justin Gan Rodriguez'
        ];

        // Combine all names with proper distribution: 70% Malay, 20% Chinese, 10% Tamil
        // For 120 users: 84 Malay, 24 Chinese, 12 Tamil (all unique names)
        $allNames = [];

        // Add Malay names (70% = 84 unique names)
        $allNames = array_merge($allNames, $malayNames);

        // Add Chinese names (20% = 24 names)
        $allNames = array_merge($allNames, array_slice($chineseNames, 0, 24));

        // Add Tamil names (10% = 12 names)
        $allNames = array_merge($allNames, array_slice($tamilNames, 0, 12));

        // Shuffle to distribute evenly
        shuffle($allNames);

        // Available team images
        $teamImages = [
            'images/team/0.webp', 'images/team/1.jpg', 'images/team/2.avif', 'images/team/3.avif', 
            'images/team/4.jpg', 'images/team/5.jpg', 'images/team/6.avif', 'images/team/7.jpg',
            'images/team/8.png', 'images/team/9.webp', 'images/team/10.jpg', 'images/team/12.jpg',
            'images/team/13.jpg', 'images/team/14.png', 'images/team/15.jpg', 'images/team/2.avif'
        ];

        $participants = [];

        // Build array of emails to check
        $emailsToCheck = [];
        for ($i = 0; $i < $numberOfUsers; $i++) {
            $userNumber = $startUserIndex + $i;
            $emailDomain = ($userNumber <= 70) ? 'oceansgaming.gg' : 'driftwood.gg';
            $emailsToCheck[] = "tester$userNumber@$emailDomain";
        }

        // Bulk find existing users
        $existingUsers = User::whereIn('email', $emailsToCheck)->get()->keyBy('email');

        $usersToCreate = [];
        $now = now();

        for ($i = 0; $i < $numberOfUsers; $i++) {
            $userNumber = $startUserIndex + $i;
            $nameIndex = ($userNumber - 1) % count($allNames);
            $userName = $allNames[$nameIndex];
            $emailDomain = ($userNumber <= 70) ? 'oceansgaming.gg' : 'driftwood.gg';
            $email = "tester$userNumber@$emailDomain";

            if (isset($existingUsers[$email])) {
                // User exists, add to participants array
                $participants[] = $existingUsers[$email];
            } else {
                // User doesn't exist, prepare for bulk insert
                $usersToCreate[] = [
                    'email' => $email,
                    'name' => $userName,
                    'email_verified_at' => $now,
                    'password' => bcrypt('123456'),
                    'remember_token' => \Illuminate\Support\Str::random(10),
                    'role' => 'PARTICIPANT',
                    'status' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Bulk insert new users if any
        if (!empty($usersToCreate)) {
            User::insert($usersToCreate);

            // Fetch newly created users
            $newEmails = array_column($usersToCreate, 'email');
            $newUsers = User::whereIn('email', $newEmails)->get();
            $participants = array_merge($participants, $newUsers->all());
        }

        // Sort participants by email to maintain order (tester1, tester2, etc.)
        usort($participants, function($a, $b) {
            preg_match('/tester(\d+)@/', $a->email, $matchesA);
            preg_match('/tester(\d+)@/', $b->email, $matchesB);
            return (int)($matchesA[1] ?? 0) - (int)($matchesB[1] ?? 0);
        });

        // Bulk find existing participants
        $userIds = array_map(fn($user) => $user->id, $participants);
        $existingParticipants = Participant::whereIn('user_id', $userIds)->get()->keyBy('user_id');

        $participantsToCreate = [];
        foreach ($participants as $user) {
            if (!isset($existingParticipants[$user->id])) {
                $participantsToCreate[] = [
                    'user_id' => $user->id,
                    'nickname' => null,
                    'age' => fake()->numberBetween(13, 60),
                    'isAgeVisible' => 1,
                    'region_flag' => '🇲🇾',
                    'region_name' => 'Malaysia',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($participantsToCreate)) {
            Participant::insert($participantsToCreate);
        }

        // Bulk find existing notification counters
        $existingCounters = NotificationCounter::whereIn('user_id', $userIds)->get()->keyBy('user_id');

        $countersToCreate = [];
        foreach ($participants as $user) {
            if (!isset($existingCounters[$user->id])) {
                $countersToCreate[] = [
                    'user_id' => $user->id,
                    'social_count' => 0,
                    'teams_count' => 0,
                    'event_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($countersToCreate)) {
            NotificationCounter::insert($countersToCreate);
        }

        $noOfConTeams = intval(ceil($numberOfUsers / $playersPerTeam));

        $creatorUsers = [];
        for ($i = 0; $i < $noOfConTeams; $i++) {
            $creatorIndex = $i * $playersPerTeam;
            if ($creatorIndex < count($participants)) {
                $creatorUsers[] = $participants[$creatorIndex];
            }
        }

        // Generate unique team names for this game
        $teamNamesToCheck = [];
        $gamePrefix = strtoupper(substr($eventGame, 0, 3));

        for ($i = 0; $i < $noOfConTeams; $i++) {
            if ($playersPerTeam == 1) {
                // For 1-player teams (Chess/FIFA), use actual player name as team name
                $playerIndex = $i * $playersPerTeam;
                if ($playerIndex < count($participants)) {
                    $teamNamesToCheck[] = $participants[$playerIndex]->name;
                }
            } else {
                // For multi-player teams, use team names with game prefix for uniqueness
                $baseTeamName = $teamNames[$i % count($teamNames)];
                $teamNamesToCheck[] = "[$gamePrefix] " . $baseTeamName;
            }
        }

        $existingTeams = Team::whereIn('teamName', $teamNamesToCheck)->get()->keyBy('teamName');

        $teamsToCreate = [];
        $teams = [];

        for ($i = 0; $i < $noOfConTeams; $i++) {
            $teamImageIndex = $i % count($teamImages);
            $teamName = $teamNamesToCheck[$i];

            if (isset($existingTeams[$teamName])) {
                // Team exists, update if needed
                $team = $existingTeams[$teamName];
                $team->update([
                    'creator_id' => $creatorUsers[$i]->id,
                    'teamDescription' => "Elite esports team competing at the highest level",
                    'teamBanner' => $teamImages[$teamImageIndex],
                    'country' => 'MY',
                    'country_name' => 'Malaysia',
                    'country_flag' => '🇲🇾',
                ]);
                $teams[] = $team;
            } else {
                // Create new team
                $team = Team::create([
                    'teamName' => $teamName,
                    'creator_id' => $creatorUsers[$i]->id,
                    'teamDescription' => "Elite esports team competing at the highest level in $eventGame",
                    'teamBanner' => $teamImages[$teamImageIndex],
                    'country' => 'MY',
                    'country_name' => 'Malaysia',
                    'country_flag' => '🇲🇾',
                ]);
                $teams[] = $team;
            }
        }

        // Bulk find existing team members
        $teamIds = array_map(fn($team) => $team->id, $teams);
        $existingMembers = TeamMember::whereIn('team_id', $teamIds)
            ->whereIn('user_id', $userIds)
            ->get()
            ->keyBy(function($member) {
                return $member->user_id . '-' . $member->team_id;
            });

        $members = [];
        $membersToCreate = [];
        $participantsCount = count($participants);
        $teamsCount = count($teams);

        for ($teamIndex = 0; $teamIndex < $teamsCount; $teamIndex++) {
            $team = $teams[$teamIndex];
            $startIndex = $teamIndex * $playersPerTeam;
            $endIndex = min($startIndex + $playersPerTeam, $participantsCount);

            for ($i = $startIndex; $i < $endIndex; $i++) {
                $participant = $participants[$i];
                $key = $participant->id . '-' . $team->id;

                if (isset($existingMembers[$key])) {
                    // Member exists, update if needed
                    $member = $existingMembers[$key];
                    $member->update([
                        'status' => 'accepted',
                        'actor' => 'team',
                        'updated_at' => $now,
                    ]);
                    $members[] = $member;
                } else {
                    // Create new member
                    $membersToCreate[] = [
                        'user_id' => $participant->id,
                        'team_id' => $team->id,
                        'status' => 'accepted',
                        'actor' => 'team',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($membersToCreate)) {
            TeamMember::insert($membersToCreate);

            // Fetch newly created members
            $newMembers = TeamMember::whereIn('team_id', $teamIds)
                ->whereIn('user_id', $userIds)
                ->whereNotIn(DB::raw("CONCAT(user_id, '-', team_id)"), array_keys($existingMembers->toArray()))
                ->get();
            $members = array_merge($members, $newMembers->all());
        }

        // Validation: Ensure no player is on multiple teams for the same game
        $userTeamCount = [];
        foreach ($members as $member) {
            if (!isset($userTeamCount[$member->user_id])) {
                $userTeamCount[$member->user_id] = 0;
            }
            $userTeamCount[$member->user_id]++;
        }

        $duplicates = array_filter($userTeamCount, fn($count) => $count > 1);
        if (!empty($duplicates)) {
            throw new \Exception("Validation failed: Some users are assigned to multiple teams for game: $eventGame. User IDs: " . implode(', ', array_keys($duplicates)));
        }

        return [
            'participants' => $participants,
            'teams' => $teams,
            'members' => $members,
        ];
    }
}
