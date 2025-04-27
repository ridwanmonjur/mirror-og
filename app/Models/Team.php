<?php

namespace App\Models;

use App\Notifications\EventCancelNotification;
use App\Notifications\EventConfirmNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = ['teamName', 'teamBanner', 'creator_id', 'teamDescription', 'country', 'country_name', 'country_flag'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function captain(): HasOne
    {
        return $this->hasOne(TeamCaptain::class, 'teams_id', 'id');
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

   

    public function findTeamFollowerByUserId(int $userId)
    {
        $cacheKey = sprintf( config('cache.keys.user_team_follows'), $userId);
        
        return Cache::remember($cacheKey, now()->addSeconds(config('cache.ttl')), function () use ($userId) {
            return DB::table('team_follows')
                ->where('team_id', $this->id)
                ->where('user_id', $userId)
                ->exists();
        });
    }

    public function findTeamMemberByUserId(int $userId)
    {
        // $cacheKey = sprintf(config('cache.keys.user_team_member'), $userId, $this->id);
        
        // return Cache::remember($cacheKey, now()->addSeconds(config('cache.ttl')), function () use ($userId) {
            return TeamMember::where('team_id', $this->id)
                ->where('user_id', $userId)
                ->first();
        // });
    }

    function getMembersAndTeamCount() {
        // $cacheKey = sprintf(config('cache.keys.team_member_count'), $this->id);
    
        // return Cache::remember($cacheKey, now()->addSeconds(config('cache.ttl')), function () {
            $this->loadCount([
                'members as accepted_count' => function ($query) {
                    $query->where('status', 'accepted');
                },
                'members as left_count' => function ($query) {
                    $query->where('status', 'left');
                },
            ]);

            return [
                'accepted' => $this->accepted_count,
                'left_plus_accepted' => $this->accepted_count + $this->left_count,
            ];
        // });
    }

    public function createdAtHumaReadable() {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public static function getTeamAndMembersByTeamId(int| string $teamId): ?self
    {
        return self::with(['members' => function ($query) {
            $query->where('status', 'accepted')
                ->with(['user' => function ($query) {
                    $query->select(['id', 'name', 'email', 'userBanner']);
                }]);
        }])
        ->findOrFail($teamId);
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
            ->whereHas('roster', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
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

    public static function validateAndSaveTeam($request, $team, $user_id) 
    {     
        $customMessages = [
            'teamName.unique' => 'Please give a unique name for your team.',
            'teamName.required' => 'Please give your team a name',
            'teamName.max' => 'Team name cannot exceed 25 characters',
            'teamName.string' => 'Team name must be text',
        ];

        $request->validate([
            'teamName' => 'required|string|unique:teams|max:25',
        ], $customMessages);
        
        $team->teamName = $request->input('teamName');
        $team->creator_id = $user_id;
        $team->save();
        
        return $team;
    }

    public function processTeamRegistration($userId, $eventId): int {
        $participant = Participant::where('user_id', $userId)
            ->select(['id', 'user_id'])
            ->firstOrFail();

        $joinEvent = JoinEvent::saveJoinEvent([
            'team_id' => $this->id,
            'joiner_id' => $userId,
            'joiner_participant_id' => $participant->id,
            'event_details_id' => $eventId,
        ]);

        RosterMember::userJoinEventRoster($joinEvent->id, $this->members, $this->id, $userId);
        return $joinEvent->id;
    }


    public function uploadTeamBanner($request)
    {
       $oldBanner = $this->teamBanner;
       $newBannerPath = null;
       
       try {
            $requestData = json_decode($request->getContent(), true);
            if (!isset($requestData['file'])) {
                return null;
            }

            $fileData = $requestData['file'];
            $fileContent = base64_decode($fileData['content']);
            
            $fileNameInitial = 'teamBanner-'.time().'.'.pathinfo($fileData['filename'], PATHINFO_EXTENSION);
            $fileName = "images/team/{$fileNameInitial}";
            $storagePath = storage_path('app/public/'.$fileName);
            
            if (!file_exists(dirname($storagePath))) {
                mkdir(dirname($storagePath), 0755, true);
            }

            if (file_put_contents($storagePath, $fileContent) === false) {
                throw new \Exception('Failed to save file');
            }

            $newBannerPath = $fileName;
        
            $this->teamBanner = $fileName;
            $this->save();
            $this->destroyTeanBanner($oldBanner);
            return $fileName;
    
       } catch (\Exception $e) {
            if ($newBannerPath && file_exists(storage_path('app/public/'.$newBannerPath))) {
                unlink(storage_path('app/public/'.$newBannerPath));
            }
            
            $this->teamBanner = $oldBanner;
            $this->save();
            throw $e; 
       }
    }

    public function destroyTeanBanner($fileName)
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
