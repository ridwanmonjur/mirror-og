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

    /**
     * Get team description based on index
     */
    private function getTeamDescription($teamIndex, $isSolo = false)
    {
        if ($isSolo) {
            $soloDescriptions = [
                "Dedicated solo competitor pushing limits", "Strategic solo player seeking glory", "Passionate solo gamer with champion mindset",
                "Elite solo competitor dominating the scene", "Skilled solo player rising through ranks", "Determined solo competitor chasing victory",
                "Experienced solo gamer with winning record", "Ambitious solo player aiming for top", "Focused solo competitor with tactical prowess",
                "Veteran solo player bringing excellence", "Talented solo gamer mastering the meta", "Relentless solo competitor never backing down",
                "Pro solo player with tournament experience", "Sharp solo gamer with analytical mind", "Fearless solo competitor embracing challenges",
                "Consistent solo player with strong fundamentals", "Dynamic solo gamer adapting to meta", "Resilient solo competitor with clutch potential",
                "Methodical solo player with strategic vision", "Aggressive solo gamer hunting victories", "Calculated solo competitor with game sense",
                "Technical solo player with mechanical skill", "Versatile solo gamer playing all styles", "Disciplined solo competitor training daily",
                "Creative solo player with unique strategies", "Patient solo gamer waiting for opportunities", "Bold solo competitor making big plays",
                "Tactical solo player reading opponents well", "Competitive solo gamer seeking championships", "Professional solo competitor with dedication",
                "Skilled solo player with tournament wins", "Strategic solo gamer with deep knowledge", "Focused solo competitor with winning mentality",
                "Determined solo player grinding to top", "Talented solo gamer with natural ability", "Experienced solo competitor with wisdom",
                "Ambitious solo player chasing dreams", "Elite solo gamer dominating competition", "Passionate solo competitor loving the game",
                "Veteran solo player with proven results", "Sharp solo gamer with quick reflexes", "Confident solo competitor trusting abilities",
                "Consistent solo player with steady improvement", "Dynamic solo gamer with adaptability", "Resilient solo competitor bouncing back strong",
                "Methodical solo player with game plans", "Aggressive solo gamer taking risks", "Calculated solo competitor with precision",
                "Technical solo player with execution", "Versatile solo gamer with flexibility", "Disciplined solo competitor with routine",
                "Creative solo player with innovation", "Patient solo gamer with composure", "Bold solo competitor with courage",
                "Tactical solo player with awareness", "Competitive solo gamer with hunger", "Professional solo competitor with standards",
                "Skilled solo player with mechanics", "Strategic solo gamer with IQ", "Focused solo competitor with concentration",
                "Determined solo player with perseverance", "Talented solo gamer with potential", "Experienced solo competitor with lessons",
                "Ambitious solo player with goals", "Elite solo gamer with dominance", "Passionate solo competitor with heart",
                "Veteran solo player with legacy", "Sharp solo gamer with instincts", "Confident solo competitor with belief",
                "Consistent solo player with reliability", "Dynamic solo gamer with energy", "Resilient solo competitor with grit",
                "Methodical solo player with structure", "Aggressive solo gamer with intensity", "Calculated solo competitor with planning",
                "Technical solo player with polish", "Versatile solo gamer with range", "Disciplined solo competitor with work ethic",
                "Creative solo player with ideas", "Patient solo gamer with timing", "Bold solo competitor with fearlessness",
                "Tactical solo player with positioning", "Competitive solo gamer with drive", "Professional solo competitor with commitment",
                "Skilled solo player with talent", "Strategic solo gamer with vision", "Focused solo competitor with determination",
                "Determined solo player with resolve", "Talented solo gamer with flair", "Experienced solo competitor with maturity",
                "Ambitious solo player with aspirations", "Elite solo gamer with championship caliber", "Passionate solo competitor with dedication",
                "Veteran solo player with longevity", "Sharp solo gamer with sharpness", "Confident solo competitor with self-belief",
                "Consistent solo player with dependability", "Dynamic solo gamer with excitement", "Resilient solo competitor with toughness",
                "Methodical solo player with system", "Aggressive solo gamer with assertiveness", "Calculated solo competitor with thought",
                "Technical solo player with finesse", "Versatile solo gamer with diversity", "Disciplined solo competitor with focus",
                "Creative solo player with originality", "Patient solo gamer with patience", "Bold solo competitor with bravery",
                "Tactical solo player with intelligence", "Competitive solo gamer with competitiveness", "Professional solo competitor with professionalism"
            ];
            return $soloDescriptions[$teamIndex % count($soloDescriptions)];
        }

        $teamDescriptions = [
            "Elite esports squad competing at international level with championship aspirations",
            "Professional gaming team dedicated to excellence and continuous improvement",
            "Competitive roster striving for tournament victories and regional dominance",
            "United squad with championship mindset and collaborative teamwork",
            "Veteran team bringing years of competitive experience to every match",
            "Rising stars aiming to make their mark on the esports scene",
            "Tactical powerhouse known for strategic gameplay and coordination",
            "Aggressive team with explosive playstyle and clutch performances",
            "Disciplined roster focused on fundamentals and consistent execution",
            "Innovative squad pioneering new strategies and meta developments",
            "Resilient team bouncing back from setbacks with determination",
            "Dynamic roster adapting to meta changes and opponent strategies",
            "Championship-caliber team with proven track record of success",
            "Hungry competitors grinding to reach the top of rankings",
            "Experienced lineup with multiple tournament wins and accolades",
            "Cohesive unit with exceptional communication and synergy",
            "Versatile team capable of executing multiple strategies and compositions",
            "Calculated squad with analytical approach to competitive gaming",
            "Passionate gamers united by love for competition and victory",
            "Strategic team emphasizing vision control and map awareness",
            "Mechanical monsters with individual skill and team coordination",
            "Clutch performers known for winning crucial high-pressure moments",
            "Methodical roster with structured practice and game planning",
            "Explosive team with aggressive early game and snowball potential",
            "Defensive specialists with strong late game team fighting",
            "Adaptive squad that thrives in diverse meta environments",
            "Team-oriented players prioritizing objectives and macro gameplay",
            "Creative roster with unconventional picks and surprise strategies",
            "Consistent performers with steady improvement and growth mindset",
            "Tournament veterans with nerves of steel and experience",
            "Coordinated squad with flawless execution and timing",
            "Balanced team with solid fundamentals across all roles",
            "Ambitious competitors with sights set on championship glory",
            "Skilled roster with deep champion pools and flexibility",
            "United front with strong team identity and chemistry",
            "Comeback kings known for clutch late game performances",
            "Early game specialists dominating opening phases of matches",
            "Vision masters with superior map control and awareness",
            "Team fight experts with coordinated engage and disengage",
            "Objective-focused squad prioritizing strategic goals over kills",
            "Mechanical team with high individual skill ceiling and potential",
            "Strategic minds with superior draft phase and counter-picking",
            "Disciplined players with excellent positioning and macro sense",
            "Innovative squad constantly experimenting with new strategies",
            "Championship-proven roster with multiple title wins",
            "Rising talents grinding to break into top tier competition",
            "Veteran-led squad blending experience with young talent",
            "Aggressive early game team with strong laning phase",
            "Defensive powerhouse with excellent wave management",
            "Rotation specialists with superior map movement and timing",
            "Team-fighting monsters with devastating combo execution",
            "Split-push masters with strong side lane pressure",
            "Scaling team with powerful late game win conditions",
            "Tempo-controlling squad dictating pace of matches",
            "Objective traders with calculated risk-reward decision making",
            "Vision-denying team with superior control ward placement",
            "Counter-engage specialists with reactive team fighting",
            "Pick-potential squad with strong catch and assassination",
            "Siege experts with poke compositions and tower pressure",
            "Disengage masters with excellent retreat and repositioning",
            "All-in team with commitment to decisive team fights",
            "Peel-focused squad protecting carries with precision",
            "Dive composition specialists with aggressive backline access",
            "Kiting experts with superior spacing and positioning",
            "Zone control team with area denial and terrain advantage",
            "Sustain-focused squad with healing and shielding synergy",
            "Burst damage team with explosive combo potential",
            "Crowd control specialists with layered lockdown abilities",
            "Mobility squad with superior map presence and flanking",
            "Tanky frontline team with durable engage and peel",
            "Glass cannon roster with high risk high reward playstyle",
            "Balanced composition with coverage across all damage types",
            "Early spike team capitalizing on power spike timings",
            "Item-dependent squad scaling with gold and experience",
            "Level-focused team with experience advantage strategies",
            "Objective-priority squad with baron and dragon control",
            "Lane-swap specialists with flexible role assignments",
            "Roaming team with strong mid-game skirmishing",
            "Farming-focused squad with superior gold generation",
            "Trading specialists with efficient poke and harass",
            "Wave-clear team with defensive stalling capabilities",
            "Push-focused squad with fast tower taking and pressure",
            "Jungle-control team with superior vision and invades",
            "Gank-heavy roster with roaming and pick potential",
            "Counter-jungle specialists with aggressive jungle control",
            "Support-focused team with strong warding and vision",
            "Carry-oriented squad with hyper-carry protection",
            "Mid-focused team with roaming mid lane priority",
            "ADC-centric roster with strong bot lane advantage",
            "Top-lane specialists with strong split push and dueling",
            "Flex-pick team with position-swapping versatility",
            "One-trick squad with mastery of signature champions",
            "Meta-following team with strong tier-list awareness",
            "Counter-pick specialists with champion pool depth",
            "Comfort-pick team with signature champion preferences",
            "Blind-pick squad with safe first-rotation champions",
            "Draft-priority team with strong pick and ban strategy",
            "Red-side specialists with counter-pick advantages",
            "Blue-side team utilizing first-pick priority effectively",
            "Patch-adaptive squad with quick meta understanding",
            "Scrim-tested team with extensive practice and preparation",
            "VOD-review focused squad with analytical improvement",
            "Coaching-supported team with strategic guidance",
            "Data-driven roster with statistical analysis and metrics",
            "Communication-focused squad with clear shot-calling",
            "Mental-fortitude team with strong psychological resilience",
            "Bootcamp-trained squad with intensive preparation periods",
            "International-experienced roster with global tournament participation"
        ];

        return $teamDescriptions[$teamIndex % count($teamDescriptions)];
    }

    /**
     * Get team banner image based on game type
     */
    private function getTeamImage($game, $teamIndex)
    {
        $gameLower = strtolower($game);

        if ($gameLower === 'chess') {
            $prefix = 'ch';
            $imageIndex = $teamIndex % 32;
        } elseif ($gameLower === 'fifa') {
            $prefix = 'f';
            $imageIndex = $teamIndex % 32;
        } else {
            // For other games, use numbered team images (0-80)
            $prefix = '';
            $imageIndex = $teamIndex % 81;
        }

        $fileName = $prefix . $imageIndex;
        $basePath = storage_path('app/public/images/team');
        $possibleExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

        foreach ($possibleExtensions as $ext) {
            $filePath = "{$basePath}/{$fileName}.{$ext}";
            if (file_exists($filePath)) {
                return "images/team/{$fileName}.{$ext}";
            }
        }

        // Fallback to .jpg if no file found (maintains backward compatibility)
        return "images/team/{$fileName}.jpg";
    }

    /**
     * Get user profile image based on ethnicity
     */
    private function getUserImage($userNumber)
    {
        // Ethnicity-based distribution for all users
        if ($userNumber <= 84) {
            // Malay player: m0 to m83 (84 images)
            $prefix = 'm';
            $imageIndex = ($userNumber - 1) % 84;
        } elseif ($userNumber <= 108) {
            // Chinese player: c0 to c23 (24 images)
            $prefix = 'c';
            $imageIndex = ($userNumber - 85) % 24;
        } else {
            // Tamil player: t0 to t11 (12 images)
            $prefix = 't';
            $imageIndex = ($userNumber - 109) % 12;
        }

        $fileName = $prefix . $imageIndex;
        $basePath = storage_path('app/public/images/user');
        $possibleExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

        foreach ($possibleExtensions as $ext) {
            $filePath = "{$basePath}/{$fileName}.{$ext}";
            if (file_exists($filePath)) {
                return "images/user/{$fileName}.{$ext}";
            }
        }

        // Fallback to .jpg if no file found (maintains backward compatibility)
        return "images/user/{$fileName}.jpg";
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
        // Only override if the passed values are defaults (not explicitly set)
        $gameLower = strtolower($eventGame);
        if (isset($gameConfigs[$gameLower])) {
            $config = $gameConfigs[$gameLower];
            // Only use game defaults if numberOfUsers is the function default (10)
            // Otherwise respect the passed parameter
            if ($numberOfUsers == 10 && $playersPerTeam == 5 && $startUserIndex == 1) {
                $numberOfUsers = $config['numberOfUsers'];
                $playersPerTeam = $config['playersPerTeam'];
                $startUserIndex = $config['startUserIndex'];
            }
            // If custom values were passed, only override startUserIndex
            else {
                $startUserIndex = $config['startUserIndex'];
            }
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
            'Azim Danial Ariffin', 'Hilmi Faizal Bakar', 'Asyraf Hakam Che Mat', 'Farhan Danish Din', 'Hasnul Fikri Elias',
            'Imran Qayyum Faruq', 'Razali Hafeez Ghaffar', 'Salman Yahya Zain', 'Taufiq Nasri Harun'
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
                // User exists, update profile image if needed and add to participants array
                $user = $existingUsers[$email];
                if (empty($user->userBanner)) {
                    $userImage = $this->getUserImage($userNumber);
                    if ($userImage) {
                        $user->update(['userBanner' => $userImage]);
                    }
                }
                // Ensure existing user has a slug
                if (empty($user->slug) && method_exists($user, 'slugify')) {
                    $baseSlug = \Illuminate\Support\Str::slug($user->name);
                    $slug = $baseSlug;
                    $counter = 1;

                    // Check if slug already exists
                    while (User::where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $user->slug = $slug;
                    $user->save();
                }
                $participants[] = $user;
            } else {
                // User doesn't exist, prepare for bulk insert
                $userImage = $this->getUserImage($userNumber);

                $usersToCreate[] = [
                    'email' => $email,
                    'name' => $userName,
                    'email_verified_at' => $now,
                    'password' => bcrypt('123456'),
                    'remember_token' => \Illuminate\Support\Str::random(10),
                    'role' => 'PARTICIPANT',
                    'status' => null,
                    'userBanner' => $userImage,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Bulk insert new users if any
        if (!empty($usersToCreate)) {
            User::insert($usersToCreate);

            // Fetch newly created users and generate slugs
            $newEmails = array_column($usersToCreate, 'email');
            $newUsers = User::whereIn('email', $newEmails)->get();

            foreach ($newUsers as $user) {
                if (empty($user->slug) && method_exists($user, 'slugify')) {
                    $baseSlug = \Illuminate\Support\Str::slug($user->name);
                    $slug = $baseSlug;
                    $counter = 1;

                    // Check if slug already exists
                    while (User::where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $user->slug = $slug;
                    $user->save();
                }
            }

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

        // Generate team names
        $teamNamesToCheck = [];

        for ($i = 0; $i < $noOfConTeams; $i++) {
            if ($playersPerTeam == 1) {
                // For 1-player teams (Chess/FIFA), use actual player name as team name
                $playerIndex = $i * $playersPerTeam;
                if ($playerIndex < count($participants)) {
                    $teamNamesToCheck[] = $participants[$playerIndex]->name;
                }
            } else {
                // For multi-player teams, use team names without prefix (slugs handle uniqueness)
                $baseTeamName = $teamNames[$i % count($teamNames)];
                $teamNamesToCheck[] = $baseTeamName;
            }
        }

        $existingTeams = Team::whereIn('teamName', $teamNamesToCheck)->get()->keyBy('teamName');

        $teamsToCreate = [];
        $teams = [];

        for ($i = 0; $i < $noOfConTeams; $i++) {
            $teamBanner = $this->getTeamImage($eventGame, $i);
            $teamName = $teamNamesToCheck[$i];
            $isSolo = ($playersPerTeam == 1);
            $teamDescription = $this->getTeamDescription($i, $isSolo);

            if (isset($existingTeams[$teamName])) {
                // Team exists, update if needed
                $team = $existingTeams[$teamName];
                $team->update([
                    'creator_id' => $creatorUsers[$i]->id,
                    'teamDescription' => $teamDescription,
                    'teamBanner' => $teamBanner,
                    'member_limit' => $playersPerTeam,
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
                    'teamDescription' => $teamDescription,
                    'teamBanner' => $teamBanner,
                    'member_limit' => $playersPerTeam,
                    'country' => 'MY',
                    'country_name' => 'Malaysia',
                    'country_flag' => '🇲🇾',
                ]);

                // Generate slug for new team
                if (method_exists($team, 'slugify')) {
                    $team->slugify();
                    $team->save();
                }

                $teams[] = $team;
            }
        }

        // Generate slugs for existing teams that don't have them
        foreach ($teams as $team) {
            if (empty($team->slug) && method_exists($team, 'slugify')) {
                $team->slugify();
                $team->save();
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
