<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brackets extends Model
{
    use HasFactory;

    protected $table = 'brackets';

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id', 'id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id', 'id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_details_id', 'id');
    }

    protected $fillable = [
        'order',
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'team1_position',
        'team2_position',
        'loser_next_position',
        'team1_points',
        'team2_points',
        'event',
        'event_details_id',
        'stage_name',
        'inner_stage_name',
        'status',
        'result',
    ];

    public function scopeFilterByDeadlines($query, $bracketDeadlines)
    {
        return $query->where(function ($query) use ($bracketDeadlines) {
            foreach ($bracketDeadlines as $deadline) {
                $query->orWhere(function ($query) use ($deadline) {
                    $query->where('stage_name', $deadline->stage)
                          ->where('inner_stage_name', $deadline->inner_stage);
                });
            }
        });
    }
}
