<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'event_categories';
    protected $casts = [
        'eventTags' => 'array',
    ];
}
