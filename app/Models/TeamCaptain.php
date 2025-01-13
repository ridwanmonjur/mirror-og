<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamCaptain extends Model
{
    public $timestamps = false;

    protected $table = 'captains';

    protected $fillable = ['userID', 'team_id'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teams_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_member_id', 'id');
    }
}
