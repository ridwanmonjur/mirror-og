<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $table = 'teams';
    protected $fillable = [
        'teamName',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'team_id', 'id');
    }

    
}


