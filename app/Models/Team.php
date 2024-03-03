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
        return $this->hasMany(Member::class, 'team_id', 'id');
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

    public static function getUserTeamList($user_id)
    {
        $teamList = self::leftJoin('members', 'teams.id', '=', 'members.team_id')
            ->where(function ($query) use ($user_id) {
                $query->where('teams.user_id', $user_id)->orWhere('members.user_id', $user_id);
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
}
