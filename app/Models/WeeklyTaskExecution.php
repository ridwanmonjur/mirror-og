<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyTaskExecution extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];
}
