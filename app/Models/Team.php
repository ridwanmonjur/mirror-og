<?php

namespace App\Models;

use App\Notifications\EventCancelNotification;
use App\Notifications\EventConfirmNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

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

    public function roster(): HasMany
    {
        return $this->hasMany(RosterMember::class);
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

    protected function generateUrl($path)
    {
        return config('app.url') . '/' . $path;
    }

    public function processTeamRegistration($user, $event): array
    {
        $userId = $user->id;
        $teamBannerUrl = 
            $this->teamBanner ? 
            Storage::path($this->teamBanner) : 
            public_path('assets/images/404.png');

        $teamMembers = $this->members;
        $participant = Participant::where('user_id', $userId)->firstOrFail();
        $allEventLogs = [];
        $memberNotification = [
            'team' => ['id' => $this->id],
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

            $memberList = [];
            $memberNotification['banner'] = $teamBannerUrl;
            $memberNotification['textFirstPart'] = <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="{$teamBannerUrl}"
                        width="45" height="45"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
            HTML;
            $memberNotification['text'] = <<<HTML
                <span class="notification-gray">
                    You have joined 
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event.
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>
                    with a team named 
                    <a href="/view/team/{$this->id}">
                        <span class="notification-blue">{$this->teamName}</span>
                    </a>.
                </span>
            HTML;


            foreach ($teamMembers as $member) {
                $memberList[] = $member->user;
                $address = $member->user->id == $userId ? 'You': $member->user->name;
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
                            {$address} have joined 
                            <a href="/view/organizer/{$event->user->id}">
                                <span class="notification-blue">{$event->user->name}</span>
                            </a>'s event,
                            <a href="/event/{$event->id}">
                                <span class="notification-blue">{$event->eventName}</span>
                            </a>. Please register your roster and complete registration for this event.
                        </span>
                    HTML,
                ];
            }
            

        $organizerList = [$event->user];

        $organizerNotification = [
            'subject' => 'Team '.$this->teamName.' joining Event: '.$event->eventName,
            'team' => ['id' => $this->id],
            'banner' => $teamBannerUrl,
            'textFirstPart' => <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="{$teamBannerUrl}"
                        width="45" height="45"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
            HTML,
            'text' => <<<HTML
                <span class="notification-gray">
                    <a href="/view/team/{$this->id}">
                        <span class="notification-black">{$this->teamName}</span> 
                    </a>
                    has signed up for
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>. 
                </span>
            HTML,
            'links' => [
                [
                    'name' => 'Visit Event',
                    'url' => route('public.event.view', ['id' => $event->id]),
                ],
            ],
        ];

        $joinEvent = JoinEvent::saveJoinEvent([
            'team_id' => $this->id,
            'joiner_id' => $userId,
            'joiner_participant_id' => $participant->id,
            'event_details_id' => $event->id,
        ]);

        RosterMember::userJoinEventRoster($joinEvent->id, $teamMembers, $this->id, $userId);

        return [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs];
    }

    public function confirmTeamRegistration($event): void
    {
        $teamBannerUrl = 
            $this->teamBanner ? 
            Storage::path($this->teamBanner) : 
            public_path('assets/images/404.png');

        $teamMembers = $this->members;
        $memberNotificationDefault = [
            'team' => ['id' => $this],
            'subject' => 'Team '.$this->teamName.' confirming registration for Event: '.$event->eventName,
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
            $memberNotification['banner'] = $teamBannerUrl;
            $memberNotification['team'] = $memberNotificationDefault['team'];
            $memberNotification['subject'] = $memberNotificationDefault['subject'];
            $memberNotification['links'] = $memberNotificationDefault['links'];
            $memberNotification['textFirstPart'] = <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="{$teamBannerUrl}"
                        width="45" height="45"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
            HTML;

            $memberNotification['text'] = <<<HTML
                <span class="notification-gray">
                    Your team 
                    <a href="/view/team/{$this->id}">
                        <span class="notification-black">{$this->teamName}</span> 
                    </a> has confirmed registration for  
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>. Your registration is completed and all ready to go.
                </span>
            HTML;
        }

        $organizerList = [$event->user];

        $organizerNotification = [
            'subject' => 'Team '.$this->teamName.' confirmed registration for Event: '.$event->eventName,
            'team' => ['id' => $this->id],
            'banner' => $teamBannerUrl,
            'textFirstPart' => <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="{$teamBannerUrl}"
                        width="45" height="45"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
            HTML,
            'text' => <<<HTML
                <span class="notification-gray">
                    <a href="/view/team/{$this->id}">
                        <span class="notification-black">{$this->teamName}</span> 
                    </a>
                    has confirmed registration for
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>. They have confirmed their positions for this event.
                </span>
            HTML,
            'links' => [
                [
                    'name' => 'Visit Event',
                    'url' => route('public.event.view', ['id' => $event->id]),
                ],
            ],
        ];
        Notification::send($memberList, new EventConfirmNotification($memberNotification));
        Notification::send($organizerList, new EventConfirmNotification($organizerNotification));
    }

    public function cancelTeamRegistration(EventDetail $event, array $discountsByUserAndType, bool $isTeamCancelee = true)
    {
        if ($isTeamCancelee) {
            $data['subject'] = 'Team '.$this->teamName.' canceled registration for Event: '.$event->eventName;
            $data['refundString'] = 'half';
            $data['cacnelOrgHtml'] = <<<HTML
                <span>
                    Team   
                        <a href="/view/team/{$this->id}">
                            <span class="notification-black">{$this->teamName}</span> 
                        </a> has canceled registration for
                        your
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span>
                        </a>.
                </span>
                HTML;
            $data['cacnelMemeberHtml'] = <<<HTML
                <span>
                    Team   
                        <a href="/view/team/{$this->id}">
                            <span class="notification-black">{$this->teamName}</span> 
                        </a> has canceled registration for
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>.
                </span>
            HTML;
        } else {
            $data['subject'] = 'Organizer has '. 'canceled the Event: '.$event->eventName;
            $data['refundString'] = 'all';
            $data['cacnelOrgHtml'] = <<<HTML
                <span>
                    <span class="notification-black">You have canceled your event, </span>    
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>.
                </span>
                HTML;
            $data['cacnelMemeberHtml'] = <<<HTML
                <span>
                    Organizer   
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-black">{$event->user->name}</span> 
                    </a> has canceled the event: <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>.
                </span>
            HTML;
        }

        $teamBannerUrl = 
            $this->teamBanner ? 
            Storage::path($this->teamBanner) : 
            public_path('assets/images/404.png');

        $teamMembers = $this->members;
        $memberNotificationDefault = [
            'team' => ['id' => $this->id],
            'subject' => $data['subject'],
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
            $user = $member->user;
            $memberList[] = $user;
            $discount = isset($discountsByUserAndType[$member->user_id]) ? $discountsByUserAndType[$member->user_id] : null;
            $discountText = '';

            $issetReleasedAmount = isset($discount['released_amount']) && $discount['released_amount'] > 0;
            $issetCouponedAmount = isset($discount['couponed_amount']) && $discount['couponed_amount'] > 0;    
            if ( $issetReleasedAmount || $issetCouponedAmount ) {
                $discountText = "You have been returned {$data['refundString']} of your contribution: ";
                
                if ($issetReleasedAmount) {
                    $discountText .= "RM {$discount['released_amount']} in bank refunds" ;
                }
                
                if ($issetReleasedAmount && $issetCouponedAmount) {
                    $discountText .= " &";
                }
            
                if ($issetCouponedAmount) {
                    $discountText .= " RM {$discount['couponed_amount']} in coupons.";
                }
            }
            

            $memberNotification['banner'] = $teamBannerUrl;
            $memberNotification['team'] = $memberNotificationDefault['team'];
            $memberNotification['subject'] = $memberNotificationDefault['subject'];
            $memberNotification['links'] = $memberNotificationDefault['links'];
            $memberNotification['textFirstPart'] = <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="{$teamBannerUrl}"
                        width="45" height="45"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
            HTML;

            $memberNotification['text'] = <<<HTML
                <span class="notification-gray">
                    {$data['cacnelMemeberHtml']}
                    <br> {$discountText}
                </span> 
            HTML;
        }

        $organizerList = [$event->user];

        $organizerNotification = [
            'subject' => $data['subject'],
            'team' => ['id' => $this->id],
            'banner' => $teamBannerUrl,
            'textFirstPart' => <<<HTML
                <a href="/view/team/{$this->id}">
                    <img src="{$teamBannerUrl}"
                        width="45" height="45"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Team banner for {$this->teamName}">
                </a>
            HTML,
            'text' =>  <<<HTML
                <span class="notification-gray">
                    {$data['cacnelOrgHtml']}
                    <br>The system has refunded {$data['refundString']} the registration fees in refunds and coupons.
                </span>
            HTML,
            'links' => [
                [
                    'name' => 'Visit Event',
                    'url' => route('public.event.view', ['id' => $event->id]),
                ],
            ],
        ];

        Notification::send($memberList, new EventCancelNotification($memberNotification));
        Notification::send($organizerList, new EventCancelNotification($organizerNotification));
        
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
