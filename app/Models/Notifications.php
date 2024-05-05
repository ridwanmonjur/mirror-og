<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = ['type', 'notifiable_id', 'notifiable_type', 
        'object_id', 'object_type', 
        'read_at', 'data'
    ];

    public function findNotifications($parameters): Notifications
    {
        return Notifications::where([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'subject_id' => $parameters['subject_id'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action']
        ]);
    }

   
    public function createNotifications($parameters) {
        Notifications::create([
            'notifiable_type' => $parameters['notifiable_type'],
            'notifiable_id' => $parameters['notifiable_id'],
            'type' => Notifications::class,
            'object_type' => $parameters['object_type'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action'],
            'data' => $parameters['data']
        ]);
    }

}
