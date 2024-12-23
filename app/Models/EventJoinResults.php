<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventJoinResults extends Model
{
    use HasFactory;

    protected $table = 'event_join_results';

    public static function getEventJoinResults(int| string $id): Collection
    {
        return DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            // ->where('join_status', 'confirmed')
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('event_join_results', 'join_events.id', '=', 'event_join_results.join_events_id')
            ->select(
                'join_events.join_status',
                'join_events.id as id1',
                'join_events.event_details_id',
                'join_events.team_id',
                'teams.*',
                'event_join_results.position',
            )->get();
    }
}
