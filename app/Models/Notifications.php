<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = ['type', 'notifiable_id', 'notifiable_type', 
        // 'object_id', 'object_type', 
        'read_at', 'data'
    ];

}
