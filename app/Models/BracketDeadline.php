<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        $deadlinesInitial = self::where('event_details_id', $id)->get();
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

        // dd($deadlines);

        return $deadlines;
    }
}
