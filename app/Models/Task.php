<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable =
        ['id',	'task_name',	'action_time',	'created_at',	'taskable_type',	'taskable_id',	'event_id'];

    public $timestamps = false;

    public function taskable(): MorphTo
    {
        return $this->morphTo;
    }
}
