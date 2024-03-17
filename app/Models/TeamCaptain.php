<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamCaptain extends Model
{
    protected $table = 'captains';
    protected $fillable = ['userID', 'team_id'];
}
