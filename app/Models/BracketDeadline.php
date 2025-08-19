<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use App\Services\EventMatchService;
use Illuminate\Support\Facades\Log;

class BracketDeadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_details_id',
        'stage',
        'inner_stage',
        'start_date',
        'end_date',
        'created_at',

    ];

    protected $casts = [
        'deadlines' => 'array',
    ];

    public $timestamps = false;

    protected $table = 'bracket_deadlines';

    protected static function booted()
    {
        static::saved(function ($deadline) {
            self::clearEventCache($deadline->event_details_id);
            
        });

        static::deleted(function ($deadline) {
            self::clearEventCache($deadline->event_details_id);
        });
    }

    public function eventDetails()
    {
        return $this->belongsTo(EventDetail::class);
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public static function getByEventDetail($id, $tierTeamSlot)
    {
        $cacheKey = "deadlines_event_{$id}";
        
        $deadlinesInitial = Cache::remember($cacheKey, config('cache.ttl', 3600), function () use ($id) {
            return self::where('event_details_id', $id)
                ->orderBy('stage')
                ->orderBy('inner_stage')
                ->get();
        });

        $currentDate = \Carbon\Carbon::now();
        $deadlines = [];
        
        foreach ($deadlinesInitial as $deadlineInital) {
            $stage = $deadlineInital->stage;
            $innerStage = $deadlineInital->inner_stage;

            $startDate = $deadlineInital->start_date ? \Carbon\Carbon::parse($deadlineInital->start_date) : null;
            $endDate = $deadlineInital->end_date ? \Carbon\Carbon::parse($deadlineInital->end_date) : null;

            $hasStarted = $startDate && $currentDate->gte($startDate);
            $hasEnded = $endDate && $currentDate->gte($endDate);

            $diffDate = null;

            if ($hasStarted && ! $hasEnded && $endDate) {
                $diffDate = $deadlineInital->end_date;
            } elseif (! $hasStarted && $startDate) {
                $diffDate = $deadlineInital->start_date;
            }

            $deadlines[$stage][$innerStage] = [
                'start' => $deadlineInital->start_date,
                'end' => $deadlineInital->end_date,
                'has_started' => $hasStarted,
                'has_ended' => $hasEnded,
                'diff_date' => $diffDate,
            ];
        }

        // Optimize hash computation
        if (empty($deadlines)) {
            $deadlinesHash = "0";
        } else {
            $hashData = [];
            foreach ($deadlines as $stage => $innerStages) {
                foreach ($innerStages as $innerStage => $deadlineData) {
                    $hashData[] = "{$stage}{$innerStage}." . 
                        ($deadlineData['has_started'] ? '1' : '0') . 
                        ($deadlineData['has_ended'] ? '1' : '0');
                }
            }
            $deadlinesHash = dechex(crc32(implode('|', $hashData)));
        }
        Log::info($deadlinesHash);


        return [
            'deadlines' => $deadlines,
            'hash' => $deadlinesHash
        ];
    }

    public static function clearEventCache($eventId)
    {
        $cacheKey = "deadlines_event{$eventId}";
        Cache::forget($cacheKey);
    }
}
