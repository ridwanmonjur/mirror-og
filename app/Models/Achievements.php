<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievements extends Model
{
    protected $table = 'achievements';
    use HasFactory;

    /**
    * @param string|int $id
    * @return \Illuminate\Support\Collection<int, \App\Models\JoinEvent>
    */
    public static function getTeamAchievements(string| int $id): Collection
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
