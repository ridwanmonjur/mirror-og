<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Achievements extends Model
{
    protected $table = 'achievements';

    public static function getTeamAchievements($id)
    {
        return DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('achievements', 'join_events.id', '=', 'achievements.join_event_id')
            ->select(
                'join_events.id as id1',
                'join_events.event_details_id',
                'join_events.team_id',
                'teams.*',
                'achievements.id as achievements_id',
                'achievements.title as achievements_title',
                'achievements.description as achievements_description',
                'achievements.created_at as achievements_created_at',
            )
            ->get();
    }
}
