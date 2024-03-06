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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id');
    }

    private static function storeTeanBanner($file)
    {
        $fileNameInitial = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
        $fileNameFinal = "images/team/$fileNameInitial";
        $file->storeAs('images/team/', $fileNameInitial);
        return $fileNameFinal;
    }

    public static function destroyTeanBanner($file)
    {
        $fileNameInitial = str_replace('images/team/', '', $file);
        $fileNameFinal = "images/team/$fileNameInitial";

        if (file_exists($fileNameFinal)) {
            unlink($fileNameFinal);
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
                $query->where('teams.creator_id', $user_id)->orWhere(
                    function ($query) use ($user_id) {
                        return $query->where('team_members.user_id', $user_id)
                            ->where('status', 'accepted');
                    });
                    
            })
            ->groupBy('teams.id')
            ->select('teams.*')
            ->get();
        
        $teamIdList = $teamList->pluck('id')->toArray();

        if ($teamList->isNotEmpty()) {
            return [
                'teamList' => $teamList,
                'teamIdList' => $teamIdList
            ];
        } else {
            return [
                'teamList' => null,
                'teamIdList' => null
            ];
        }
    }

    public static function getUserTeamListAndCount($user_id)
    {
        $teamList = self::leftJoin('team_members', 'teams.id', '=', 'team_members.team_id')
            ->where(function ($query) use ($user_id) {
                $query->where('teams.creator_id', $user_id)->orWhere(
                    function ($query) use ($user_id) {
                        return $query->where('team_members.user_id', $user_id)
                            ->where('status', 'accepted');
                    });
                    
            })
            ->groupBy('teams.id')
            ->select('teams.*')
            ->get();
        
        $count = count($teamList);

        if ($teamList->isNotEmpty()) {
            return [
                'teamList' => $teamList,
                'count' => $count
            ];
        } else {
            return [
                'teamList' => [],
                'count' => 0
            ];
        }
    }
}
