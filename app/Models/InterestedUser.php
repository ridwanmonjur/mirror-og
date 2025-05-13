<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestedUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'email_verified_at',
        'email_verified_token',
        'pass_text'
    ];

    protected $table = 'interested_user';

}
