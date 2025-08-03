<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventJoinResults extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'event_join_results';

    protected $fillable = ['join_events_id', 'position', 'prize_sum', 'played', 'won', 'draw', 'points', 'lost'];

    public function joinEvent(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_id');
    }

    public static function getEventJoinResults(int|string $id): Collection
    {
        return DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->where('join_status', 'confirmed')
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('event_join_results', 'join_events.id', '=', 'event_join_results.join_events_id')
            ->select(
                'join_events.join_status',
                'join_events.id as id1',
                'join_events.event_details_id',
                'join_events.team_id',
                'teams.*',
                'event_join_results.position',
                'event_join_results.played',
                'event_join_results.won',
                'event_join_results.draw',
                'event_join_results.points',
                'event_join_results.lost',
            )->get();
    }

    public static function getEventJoinListResults(array $idList): Collection
    {
        return DB::table('join_events')
            ->whereIn('join_events.id', $idList)
            ->join('event_details', 'join_events.event_details_id', '=', 'event_details.id')
            // ->where('join_status', 'confirmed')
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('event_join_results', 'join_events.id', '=', 'event_join_results.join_events_id')
            ->whereNotNull('event_join_results.position')
            ->select(
                'join_events.join_status',
                'join_events.id as join_id',
                'join_events.event_details_id as event_id',
                'join_events.team_id',
                'teams.*',
                'teams.id as team_id',
                'event_details.id',
                'event_details.eventName',
                'event_details.eventBanner',
                'event_join_results.position',
            )->get();
    }
}
