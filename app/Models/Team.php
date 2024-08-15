<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = ['teamName', 'teamDescription', 'country', 'country_name', 'country_flag'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(AwardResults::class, 'join_events_id', 'id');
    }

    public function invitationList(): HasMany
    {
        return $this->hasMany(EventInvitation::class, 'team_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(TeamProfile::class, 'team_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLogs::class, 'subject');
    }

    public static function getTeamAndMembersByTeamId(int| string $teamId): ?self
    {
        return self::with(['members.user' => function ($query) {
            $query->select(['name', 'id', 'email']);
        },
        ])
            ->whereHas('members', function ($query) {
                $query->where('status', 'accepted');
            })
            ->find($teamId);
    }

    public static function getUserTeamListAndPluckIds(int| string $user_id): array
    {
        $teamList = self::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'accepted');
        })
            ->with(['members' => function ($query) {
                $query->where('status', 'accepted');
            },
            ])
            ->get();

        $teamIdList = $teamList->pluck('id')->toArray();

        if ($teamList->isNotEmpty()) {
            return [
                'teamList' => $teamList,
                'teamIdList' => $teamIdList,
            ];
        }

        return [
            'teamList' => null,
            'teamIdList' => null,
        ];
    }

    public static function getUserTeamList($user_id, $status = 'accepted')
    {
        $teamList = self::whereHas('members', function ($query) use ($user_id, $status) {
            $query->where('user_id', $user_id)->where('status', $status);
        })
            ->with(['members' => function ($query) {
                $query->where('status', 'accepted');
            },
            ])
            ->withCount(['members' => function ($query) {
                $query->where('status', 'accepted');
            },
            ])
            ->get();

        $teamIdList = $teamList->pluck('id')->toArray();

        return [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ];
    }

    public static function getUserPastTeamList($user_id)
    {
        return self::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'rejected');
        })
            ->withCount(['members' => function ($query) {
                $query->where('status', 'accepted');
            },
            ])
            ->get();
    }

    public static function getUserTeamListAndCount($user_id)
    {
        $teamList = self::where(function ($query) use ($user_id) {
            $query->whereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 'accepted');
            });
        })->with('members')
            ->get();

        $count = count($teamList);

        if ($teamList->isNotEmpty()) {
            return [
                'teamList' => $teamList,
                'count' => $count,
            ];
        }

        return [
            'teamList' => [],
            'count' => 0,
        ];
    }

    public function getAwardListByTeam()
    {
        return DB::table('join_events')
            ->where('join_events.team_id', $this->id)
            ->join('awards_results', 'join_events.id', '=', 'awards_results.join_events_id')
            ->leftJoin('awards', 'awards_results.award_id', '=', 'awards.id')
            ->groupBy('awards.id')
            ->select(
                'awards.id',
                DB::raw('COUNT(awards.id) as awards_count'),
                'awards_results.id as results_id',
                'awards_results.award_id',
                'awards.title as awards_title',
                'awards.image as awards_image'
            )
            ->get();
    }

    public static function getAwardListByTeamIdList($teamIdList)
    {
        return DB::table('join_events')
            ->whereIn('join_events.team_id', $teamIdList)
            ->join('awards_results', 'join_events.id', '=', 'awards_results.join_events_id')
            ->leftJoin('awards', 'awards_results.award_id', '=', 'awards.id')
            ->groupBy('awards.id')
            ->select(
                'awards.id',
                DB::raw('COUNT(awards.id) as awards_count'),
                'awards_results.id as results_id',
                'awards_results.award_id',
                'awards.title as awards_title',
                'awards.image as awards_image'
            )
            ->get();
    }

    public function getAchievementListByTeam()
    {
        return DB::table('join_events')
            ->where('join_events.team_id', $this->id)
            ->join('achievements', 'join_events.id', '=', 'achievements.join_event_id')
            ->select(
                'achievements.id as achievements_id',
                'achievements.title',
                'achievements.description',
                'achievements.created_at',
            )
            ->get();
    }

    public static function getAchievementListByTeamIdList($teamIdList)
    {
        return DB::table('join_events')
            ->whereIn('join_events.team_id', $teamIdList)
            ->join('achievements', 'join_events.id', '=', 'achievements.join_event_id')
            ->select(
                'achievements.id as achievements_id',
                'achievements.title',
                'achievements.description',
                'achievements.created_at',
            )
            ->get();
    }

    public static function getTeamMembersCountForEachTeam($teamIdList)
    {
        return DB::table('teams')
            ->leftJoin('team_members', function ($join) {
                $join->on('teams.id', '=', 'team_members.team_id')
                    ->where('team_members.status', '=', 'accepted');
            })
            ->whereIn('teams.id', $teamIdList)
            ->groupBy('teams.id')
            ->selectRaw('teams.id as team_id, COALESCE(COUNT(team_members.id), 0) as member_count')
            ->pluck('member_count', 'team_id')
            ->toArray();
    }

    public static function getResultsTeamMemberIds($teamId)
    {
        $team = Team::where('id', $teamId)
            ->select(['id', 'teamName', 'teamBanner', 'creator_id'])
            ->with(
                ['members' => function ($q) {
                    $q->where('status', 'accepted')
                        ->select('id', 'user_id', 'team_id', 'status')
                        ->with(['user' => function ($q) {
                            $q->select('id');
                        },
                        ]);
                },
                ]
            )->first();

        $memberUserIds = $team
            ->members
            ->pluck('user.id')
            ->toArray();

        return [$team, $memberUserIds];
    }

    public function processTeamRegistration($user, $event, $isNewTeam)
    {
        $userId = $user->id;
        $teamMembers = $this->members;
        $participant = Participant::where('user_id', $userId)->firstOrFail();
        $allEventLogs = [];
        $memberNotification = [
            'subject' => 'Team '.$this->teamName.' joining Event: '.$event->eventName,
            'links' => [
                [
                    'name' => 'View Team',
                    'url' => route('public.team.view', ['id' => $this->id]),
                ],
                [
                    'name' => 'View Event',
                    'url' => route('public.event.view', ['id' => $event->id]),
                ],
            ],
        ];

        if ($isNewTeam) {
            $memberList = [];
            $memberNotification['text'] = <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="/storage/{$this->teamBanner}" 
                        width="30" height="30"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
                <span class="notification-gray">
                    You have created a new team named 
                    <a href="/view/team/{$this->id}">
                        <span class="notification-blue">{$this->teamName}</span>
                    </a>
                    and joined 
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}'s</span>
                    </a>
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>.
                </span>
            HTML;


            foreach ($teamMembers as $member) {
                $memberList[] = $member->user;
                $allEventLogs[] = [
                    'action' => 'join',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'subject_id' => $member->user->id,
                    'subject_type' => User::class,
                    'log' => <<<HTML
                        <a href="/view/team/{$this->id}">
                            <img src="/storage/{$this->teamBanner}"
                                width="30" height="30" 
                                onerror="this.src='/assets/images/404.png';"
                                class="object-fit-cover rounded-circle me-2"
                                alt="Team banner for {$this->teamName}">
                        </a>
                        <span class="notification-gray">
                            You have joined 
                            <a href="/view/organizer/{$event->user->id}">
                                <span class="notification-blue">{$event->user->name}'s</span>
                            </a>
                            <a href="/event/{$event->id}">
                                <span class="notification-blue">{$event->eventName}</span>
                            </a>.
                        </span>
                    HTML,
                ];
            }
            
            $rosterCaptain = $teamMembers[0];
        } else {
            $memberList = $memberNotification = [];
            foreach ($teamMembers as $member) {
                $memberList[] = $member->user;
                $allEventLogs[] = [
                    'action' => 'join',
                    'subject_id' => $member->user->id,
                    'subject_type' => '\App\Models\User',
                    'log' => <<<HTML
                        <a href="/view/team/{$this->id}">
                            <img src="/storage/{$this->teamBanner}"
                                width="30" height="30"  
                                onerror="this.src='/assets/images/404.png';"
                                class="object-fit-cover rounded-circle me-2"
                                alt="Team banner for {$this->teamName}">
                        </a>
                        <span class="notification-gray">
                            You have joined 
                            <a href="/view/organizer/{$event->user->id}">
                                <span class="notification-blue">{$event->user->name}'s</span>
                            </a>
                            <a href="/event/{$event->id}">
                                <span class="notification-blue">{$event->eventName}</span>
                            </a>.
                        </span>
                    HTML,
                ];
            }
            
            $memberNotification['text'] = <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="/storage/{$this->teamBanner}" 
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        width="30" height="30" 
                        alt="Team banner for {$this->teamName}">
                </a>
                <span class="notification-gray">
                    You have selected a team named 
                    <a href="/view/team/{$this->id}">
                        <span class="notification-black">{$this->teamName}</span> 
                    </a>
                    and joined 
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}'s</span>
                    </a>
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>.
                </span>
            HTML;
        
            $rosterCaptain = TeamMember::where('team_id', $this->id)
                ->where('user_id', $user->id)->first();
        }

        $organizerList = [$event->user];

        $organizerNotification = [
            'subject' => 'Team '.$this->teamName.' joining Event: '.$event->eventName,
            'text' => ucfirst($this->teamName).' has joined your event '.$event->eventName.'!',
            'links' => [
                [
                    'name' => 'Visit team',
                    'url' => route('event.index', ['id' => $event->id]),
                ],
            ],
        ];

        $joinEvent = JoinEvent::saveJoinEvent([
            'team_id' => $this->id,
            'joiner_id' => $userId,
            'joiner_participant_id' => $participant->id,
            'event_details_id' => $event->id,
        ]);

        RosterMember::bulkCreateRosterMembers($joinEvent->id, $teamMembers);
        RosterCaptain::insert([
            'team_member_id' => $rosterCaptain->id,
            'join_events_id' => $joinEvent->id,
            'teams_id' => $this->id,
        ]);

        return [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs];
    }

    public function cancelTeamRegistration($event)
    {
        // $userId = $user->id;
        $teamMembers = $this->members;
        $allEventLogs = [];
        $memberNotification = [
            'subject' => 'Team '.$this->teamName.' leaving Event: '. $event->eventName,
            'links' => [
                [
                    'name' => 'View Team',
                    'url' => route('public.team.view', ['id' => $this->id]),
                ],
                [
                    'name' => 'View Event',
                    'url' => route('public.event.view', ['id' => $event->id]),
                ],
            ],
        ];

        
        $memberList = $memberNotification = [];
        foreach ($teamMembers as $member) {
            $memberList[] = $member->user;
            $allEventLogs[] = [
                'action' => 'leave',
                'subject_id' => $member->user->id,
                'subject_type' => '\App\Models\User',
                'log' => <<<HTML
                <span class="notification-gray">
                    <a href="/view/team/{$this->id}">
                        <span class="notification-black">{$this->teamName}</span> 
                    </a>
                    has left
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}'s</span>
                    </a>
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>.
                </span>
                HTML,
            
            ];
        }
        
        $memberNotification['text'] = <<<HTML
            <a href="/view/team/{$this->id}">
                <img src="/storage/{$this->teamBanner}" 
                    onerror="this.src='/assets/images/404.png';"
                    class="object-fit-cover rounded-circle me-2"
                    width="30" height="30" 
                    alt="Team banner for {$this->teamName}">
            </a>
            <span class="notification-gray">
                <a href="/view/team/{$this->id}">
                    <span class="notification-black">{$this->teamName}</span> 
                </a>
                has left
                <a href="/view/organizer/{$event->user->id}">
                    <span class="notification-blue">{$event->user->name}'s</span>
                </a>
                <a href="/event/{$event->id}">
                    <span class="notification-blue">{$event->eventName}</span>
                </a>.
            </span>
        HTML;
       
        $organizerList = [$event->user];

        $organizerNotification = [
            'subject' => 'Team '.$this->teamName.' leaving Event: '.$event->eventName,
            'text' => ucfirst($this->teamName).' has left your event '.$event->eventName.'!',
            'links' => [
                [
                    'name' => 'Visit team',
                    'url' => route('event.index', ['id' => $event->id]),
                ],
            ],
        ];

        $joinEvent = JoinEvent::where([
            'team_id' => $this->id,
            'event_details_id' => $event->id,
        ])->first;
        
        // RosterMember::where([
        //     'join_events_id' => $joinEvent->id,
        // ])->delete();

        // RosterMember::bulkCreateRosterMembers($joinEvent->id, $teamMembers);
        // RosterCaptain::where([
        //     'join_events_id' => $joinEvent->id,
        //     'teams_id' => $this->id,
        // ])->delete();

        $joinEvent->delete();
        return [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs];
    }


    public function uploadTeamBanner($request)
    {
        $file = $request->file('file');
        $fileNameInitial = 'teamBanner-'.time().'.'.$file->getClientOriginalExtension();
        $fileName = "images/team/{$fileNameInitial}";
        $file->storeAs('images/team/', $fileNameInitial);
        $this->teamBanner = $fileName;
        $this->save();

        return $fileName;
    }

    public static function destroyTeanBanner($fileName)
    {
        if ($fileName) {
            $fileNameInitial = str_replace('images/team/', '', $fileName);
            $fileNameFinal = "images/team/{$fileNameInitial}";

            if (file_exists($fileNameFinal)) {
                unlink($fileNameFinal);
            }
        }
    }
}
