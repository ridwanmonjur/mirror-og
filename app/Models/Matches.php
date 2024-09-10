<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'team1_position',
        'team2_position',
        'winner_id',
        'winner_next_position',
        'loser_next_position',
        'team1_points',
        'team2_points',
        'event',
        'match_type',
        'stage_name',
        'inner_stage_name',
        'status',
        'result',
    ];

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id')->withDefault(function ($team) {
            $team->name = 'Deleted';
        });
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id')->withDefault(function ($team) {
            $team->name = 'Deleted';
        });
    }
}
