<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AwardResults extends Model
{
    use HasFactory;
    protected $table = 'awards_results';
    public $fillable = ['join_events_id', 'award_id', 'team_id'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class, 'award_id', 'id');
    }
  

    public static function getTeamAwardResults(string|int $id): Collection
    {
        return DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            // ->where('join_events.join_status', '=', 'confirmed')
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->join('awards_results', 'join_events.id', '=', 'awards_results.join_events_id')
            ->join('awards', 'awards_results.award_id', '=', 'awards.id')
            // ->leftJoin('achievements', 'join_events.id', '=', 'achievements.join_event_id')
            ->select(
                'join_events.id as id1',
                'join_events.event_details_id',
                'join_events.team_id',
                'join_events.join_status',
                'teams.*',
                'awards_results.id as results_id',
                'awards_results.award_id',
                'awards.title as awards_title',
                'awards.image as awards_image',
                // 'achievements.id as achievements_id',
                // 'achievements.title as achievements_title',
                // 'achievements.description as achievements_description',
                // 'achievements.created_at as achievements_created_at',
            )
            ->get();
    }
}
