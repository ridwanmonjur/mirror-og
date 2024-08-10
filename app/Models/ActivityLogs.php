<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

class ActivityLogs extends Model
{
    use HasFactory;
    protected $fillable = ['action', 'subject_id', 'subject_type',
        'object_id', 'object_type', 'log', 'image',
        'created_at', 'updated_at',
    ];

    protected $table = 'activity_logs';

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function findActivityLog(array $parameters): Builder
    {
        $query = ActivityLogs::query();
        $query->where([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action'],
        ]);

        if (is_array($parameters['subject_id'])) {
            $query->whereIn('subject_id', $parameters['subject_id']);
        } else {
            $query->where('subject_id', $parameters['subject_id']);
        }

        return $query;
    }

    public function createActivityLogs(array $parameters): void
    {
        Log::info('hit createActivityLogs');
        Log::info($parameters['subject_id']);
        if (is_array($parameters['subject_id'])) {
            $data = [];
            foreach ($parameters['subject_id'] as $subjectId) {
                $data[] = [
                    'subject_type' => $parameters['subject_type'],
                    'object_type' => $parameters['object_type'],
                    'subject_id' => $subjectId,
                    'object_id' => $parameters['object_id'],
                    'action' => $parameters['action'],
                    'log' => $parameters['log'],
                    'image' => $parameters['image'],
                ];
            }

            ActivityLogs::insert($data);
        } else {
            ActivityLogs::create([
                'subject_type' => $parameters['subject_type'],
                'object_type' => $parameters['object_type'],
                'subject_id' => $parameters['subject_id'],
                'object_id' => $parameters['object_id'],
                'action' => $parameters['action'],
                'log' => $parameters['log'],
                'image' => $parameters['image'],
            ]);
        }
    }
}
