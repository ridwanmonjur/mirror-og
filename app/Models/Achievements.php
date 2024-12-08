<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Achievements extends Model
{
    use HasFactory;
    protected $table = 'achievements';

    /**
     * @param string|int $id
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\JoinEvent>
     */
    public static function getTeamAchievements(string| int $id): Collection
    {
        return DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->where('join_status', 'confirmed')
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->join('achievements', 'join_events.id', '=', 'achievements.join_event_id')
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
