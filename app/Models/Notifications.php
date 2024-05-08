<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = ['type', 'notifiable_id', 'notifiable_type', 
        'object_id', 'object_type', 'image', 'id',
        'read_at', 'data'
    ];

    public function findNotifications($parameters)
    {
        return Notifications::where([
            'notifiable_type' => User::class,
            'notifiable_id' => $parameters['subject_id'],
            'object_type' => $parameters['object_type'],
            'object_id' => $parameters['object_id'],
        ]);
    }

   
    public function createNotifications($parameters) {
        Notifications::create([
            'id' => uuid_create(),
            'notifiable_type' => User::class,
            'notifiable_id' => $parameters['subject_id'],
            'type' => Notifications::class,
            'object_type' => $parameters['object_type'],
            'object_id' => $parameters['object_id'],
            'data' => json_encode($parameters['data'])
        ]);
    }

}
