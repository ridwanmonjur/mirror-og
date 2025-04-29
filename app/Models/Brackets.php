<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brackets extends Model
{
    use HasFactory;

    protected $table = 'brackets';

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
        'stage_name',
        'inner_stage_name',
        'status',
        'result',
    ];

}
