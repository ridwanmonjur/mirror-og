<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = ['teamName', 'teamDescription', 'country', 'country_name', 'country_flag'];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function awards()
    {
        return $this->hasMany(AwardResults::class, 'join_events_id', 'id');
    }

    public function invitationList()
    {
        return $this->hasMany(EventInvitation::class, 'team_id');
    }

    public function profile()
    {
        return $this->hasOne(TeamProfile::class, 'team_id');
    }

    public function activities()
    {
        return $this->morphMany(ActivityLogs::class, 'subject');
    }

    private static function getTeamByCreatorId($teamId)
    {
        return self::where('id', $teamId)->value('user_id');
    }

    public static function getTeamAndMembersByTeamId($teamId)
    {
        return self::with(['members.user' => function ($query) {
            $query->select(['name', 'id', 'email']);
        }])
            ->whereHas('members', function ($query) {
                $query->where('status', 'accepted');
            })
            ->find($teamId);
    }

    public static function getUserTeamListAndPluckIds($user_id)
    {
        $teamList = self::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'accepted');
        })
            ->with(['members' => function ($query) {
                $query->where('status', 'accepted');
            }])
            ->get();

        $teamIdList = $teamList->pluck('id')->toArray();

        if ($teamList->isNotEmpty()) {
            return [
                'teamList' => $teamList,
                'teamIdList' => $teamIdList,
            ];
        } else {
            return [
                'teamList' => null,
                'teamIdList' => null,
            ];
        }
    }

    public static function getUserTeamList($user_id, $status = 'accepted')
    {
        $teamList = self::whereHas('members', function ($query) use ($user_id, $status) {
            $query->where('user_id', $user_id)->where('status', $status);
        })
            ->with(['members' => function ($query) {
                $query->where('status', 'accepted');
            }])
            ->withCount(['members' => function ($query) {
                $query->where('status', 'accepted');
            }])
            ->get();

        $teamIdList = $teamList->pluck('id')->toArray();

        return [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ];
    }

    public static function getUserPastTeamList($user_id)
    {
        $teamList = self::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'rejected');
        })
            ->withCount(['members' => function ($query) {
                $query->where('status', 'accepted');
            }])
            ->get();

        return $teamList;
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
        } else {
            return [
                'teamList' => [],
                'count' => 0,
            ];
        }
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
            ->with(['members' => function ($q) {
                $q->where('status', 'accepted')
                    ->select('id', 'user_id', 'team_id', 'status')
                    ->with(['user' => function ($q) {
                        $q->select('id');
                    },
                    ]);
            }]
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
            $memberNotification['text'] = '<span class="notification-gray"> You have created a new team named'
                .' <span class="notification-black">'.$this->teamName.'</span> and joined'
                .' <span class="notification-black">'.$event->user->name.' \'s </span> event'
                .' <span class="notification-blue">'.$event->eventName.' </span>.'
                .'</span>';
            foreach ($teamMembers as $member) {
                $memberList[] = $member->user;
                $allEventLogs[] = [
                    'action' => 'join',
                    'subject_id' => $member->user->id,
                    'subject_type' => '\App\Models\User',
                    'log' => '<span class="notification-gray"> You have joined'
                    .' <span class="notification-black">'.$event->user->name.' \'s </span> event'
                    .' <span class="notification-blue">'.$event->eventName.' </span>.'
                    .'</span>',
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
                    'log' => '<span class="notification-gray"> You have joined'
                    .' <span class="notification-black">'.$event->user->name.' \'s </span> event'
                    .' <span class="notification-blue">'.$event->eventName.' </span>.'
                    .'</span>',
                ];
            }

            $memberNotification['text'] = '<span class="notification-gray">You have selected a team named'
                .' <span class="notification-black">'.$this->teamName.'</span> and joined'
                .' <span class="notification-black">'.$event->user->name.' \'s </span> event'
                .' <span class="notification-blue">'.$event->eventName.' </span>.'
                .'</span>';
            $rosterCaptain = TeamMember::where('team_id', $this->id)
                ->where('user_id', $user->id)->get()->first();
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

        // $memberNotification, $organizerNotificatio => $text, $data, $links, $user
        return [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs];
    }

    public function uploadTeamBanner($request)
    {
        $file = $request->file('file');
        $fileNameInitial = 'teamBanner-'.time().'.'.$file->getClientOriginalExtension();
        $fileName = "images/team/$fileNameInitial";
        $file->storeAs('images/team/', $fileNameInitial);
        $this->teamBanner = $fileName;
        $fileName = asset('/storage'.'/'.$fileName);
        $this->save();

        return $fileName;
    }

    public static function destroyTeanBanner($fileName)
    {
        if ($fileName) {
            $fileNameInitial = str_replace('images/team/', '', $fileName);
            $fileNameFinal = "images/team/$fileNameInitial";

            if (file_exists($fileNameFinal)) {
                unlink($fileNameFinal);
            }
        }
    }
}
