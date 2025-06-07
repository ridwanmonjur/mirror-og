<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class ActivityLogs extends Model
{
    use HasFactory;
    protected $fillable = ['action', 'subject_id', 'subject_type',
        'object_id', 'object_type', 'log', 
    ];

    
    protected $table = 'activity_logs';

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function object(): MorphTo
    {
        return $this->morphTo();
    }

    public static function findActivityLog(array $parameters): Builder
    {
        $query = ActivityLogs::query();
        $query->where([
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

    public static function createActivityLogs(array $parameters): void
    {
        $data = [];
        $isLogArray = is_array($parameters['log']); 
        foreach ($parameters['subject_id'] as $index => $subjectId) {
            $data[] = [
                'subject_type' => $parameters['subject_type'],
                'object_type' => $parameters['object_type'],
                'subject_id' => $subjectId,
                'object_id' => $parameters['object_id'],
                'action' => $parameters['action'],
                'log' => $isLogArray? $parameters['log'][$index] : $parameters['log'],
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ];
        }

        ActivityLogs::insert($data);
    }

    public static function retrievePaginatedActivityLogs($userId, $duration, $perPage, $page)
    {
        $activityLogsQuery = ActivityLogs::where('subject_id', $userId)
                ->where('subject_type', User::class);
    
            if ($duration == 'new') {
                $activityLogsQuery->whereDate('created_at', operator: Carbon::today()->utc());
            } elseif ($duration == 'recent') {
                $activityLogsQuery->whereBetween('created_at', [Carbon::today()->utc()->subWeek()->startOfWeek(), Carbon::today()->utc()]);
            } else {
                $activityLogsQuery->whereDate('created_at', '<', Carbon::today()->utc()->subWeek()->startOfWeek());
            }
    
            $activityLogs = $activityLogsQuery
                ->orderBy('id', 'desc')
                ->simplePaginate($perPage, ['*'], 'page', $page);
                
        return $activityLogs;
    }
}
