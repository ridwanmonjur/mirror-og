<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(Award::class, 'join_events_id', 'id');
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

    public static function getUserTeamAndTeamMembersAndPluckIds($user_id)
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

    public static function getUserTeamListAndCount($user_id)
    {
        $teamList = self::where('teams.creator_id', $user_id)
            ->orWhere(function ($query) use ($user_id) {
                $query->whereHas('members', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 'accepted');
                });
            })
        ->groupBy('teams.id')
        ->select('teams.*')
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
}
