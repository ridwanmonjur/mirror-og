<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    use HasFactory;
    protected $table = 'teams';
    protected $fillable = ['teamName'];

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

    private static function getTeamByCreatorId($teamId)
    {
        return Team::where('id', $teamId)->value('user_id');
    }

    public static function getUserTeamListAndPluckIds($user_id)
    {
        $teamList = self::leftJoin('team_members', 'teams.id', '=', 'team_members.team_id')
            ->where(function ($query) use ($user_id) {
                $query->where('teams.creator_id', $user_id)->orWhere(function ($query) use ($user_id) {
                    $query->where('team_members.user_id', $user_id)->where('status', 'accepted');
                });
            })
            ->groupBy('teams.id')
            ->select('teams.*')
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

    public static function getUserTeamList($user_id)
    {
        $teamList = self::whereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 'accepted');
            })
            ->with(['members' => function($query) {
                $query->where('status', 'accepted');
            }])
        ->get();

        $teamIdList = $teamList->pluck('id')->toArray();
        return [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ];
    }

    public static function getUserTeamListAndCount($user_id)
    {
        $teamList = self::where('teams.creator_id', $user_id)
            ->orWhere(function ($query) use ($user_id) {
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

    public function getAwardListByTeam() {
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

    public static function getAwardListByTeamIdList($teamIdList) {
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

    public  function getAchievementListByTeam() {
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

    public static function getAchievementListByTeamIdList($teamIdList) {
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

    public static function getTeamMembersCountForEachTeam($teamIdList) {
        return DB::table('teams')
            ->leftJoin('team_members', function($join) {
                $join->on('teams.id', '=', 'team_members.team_id')
                    ->where('team_members.status', '=', 'accepted');
            })
            ->whereIn('teams.id', $teamIdList)
            ->groupBy('teams.id')
            ->selectRaw('teams.id as team_id, COALESCE(COUNT(team_members.id), 0) as member_count')
            ->pluck('member_count', 'team_id')
            ->toArray();
    }
}
