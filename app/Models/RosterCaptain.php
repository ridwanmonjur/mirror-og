<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterCaptain extends Model
{
    protected $table = 'rosters_captain';
    protected $fillable = ['userID', 'join_events_id'];

    public $timestamps = false;
}
