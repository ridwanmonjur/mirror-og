<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    protected $fillable = ['action', 'subject_id', 'subject_type', 
        'object_id', 'object_type', 'log', 'created_at', 'updated_at'
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    protected $table = 'activity_logs';

    public function findActivityLog($parameters): ActivityLogs
    {
        return ActivityLogs::where([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'subject_id' => $parameters['subject_id'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action']
        ]);
    }

    public function createActivityLogs($parameters) {
        ActivityLogs::create([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'subject_id' => $parameters['subject_id'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action'],
            'log' => $parameters['log']
        ]);
    }



}
