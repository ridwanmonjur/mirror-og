<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterCaptain extends Model
{
    public $timestamps = false;

    protected $table = 'rosters_captain';

    protected $fillable = ['team_member_id', 'join_events_id', 'team_id'];
}
