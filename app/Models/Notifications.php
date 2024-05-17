<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = ['type', 'notifiable_id', 'notifiable_type',
        'object_id', 'object_type', 'image', 'id',
        'read_at', 'data',
    ];

    public function findNotifications($parameters)
    {
        $query = Notifications::where([
            'notifiable_type' => User::class,
            'object_type' => $parameters['object_type'],
            'object_id' => $parameters['object_id'],
        ]);

        if (is_array($parameters['subject_id'])) {
            $query->whereIn('notifiable_id', $parameters['subject_id']);
        } else {
            $query->where('notifiable_id', $parameters['subject_id']);
        }

        return $query;
    }

    public function createNotifications($parameters)
    {
        Log::info('hit createNotifications');

        if (is_array($parameters['subject_id'])) {
            $data = [];
            foreach ($parameters['subject_id'] as $subjectId) {
                $data[] = [
                    'id' => uuid_create(),
                    'notifiable_type' => User::class,
                    'notifiable_id' => $subjectId,
                    'type' => Notifications::class,
                    'object_type' => $parameters['object_type'],
                    'object_id' => $parameters['object_id'],
                    'data' => json_encode($parameters['data']),
                    'image' => $parameters['image'],
                ];
            }

            Notifications::insert($data);
        } else {
            Notifications::create([
                'id' => uuid_create(),
                'notifiable_type' => User::class,
                'notifiable_id' => $parameters['subject_id'],
                'type' => Notifications::class,
                'object_type' => $parameters['object_type'],
                'object_id' => $parameters['object_id'],
                'data' => json_encode($parameters['data']),
                'image' => $parameters['image'],
            ]);
        }
    }
}
