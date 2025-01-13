<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Blocks extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'blocks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'blocked_user_id',
    ];

  
    /**
     * Get the user who blocked another user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who is blocked.
     */
    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}
