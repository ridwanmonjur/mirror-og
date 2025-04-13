<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BracketDeadline extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_details_id',
        'stage',
        'inner_stage',
        'start_date',
        'end_date',
        'created_at'
    
    ];
    
    protected $casts = [
        'deadlines' => 'array'
    ];

    public $timestamps = false;

    protected $table = 'bracket_deadlines';
    
    public function eventDetails()
    {
        return $this->belongsTo(EventDetail::class);
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
        
        $readableDate = null;
        
        if ($hasStarted && !$hasEnded && $endDate) {
            // Event has started but not ended - show time until end
            $readableDate = $currentDate->diffForHumans($endDate, true);
        } elseif (!$hasStarted && $startDate) {
            // Event has not started - show time until start
            $readableDate = $currentDate->diffForHumans($startDate, true);
        }
        
        $deadlines[$stage][$innerStage] = [
            'start' => $deadlineInital->start_date,
            'end' => $deadlineInital->end_date,
            'has_started' => $hasStarted,
            'has_ended' => $hasEnded,
            'readable_date' => $readableDate
        ];
    }

    $mergedDeadlines = [];

    if ($tierTeamSlot == 32) {
        $mergedDeadlines = [
            'U' => [
                'e1' => [
                    'start' => $deadlines['U']['e1']['start'] ?? null,
                    'end' => $deadlines['U']['e1']['end'] ?? null,
                    'has_started' => $deadlines['U']['e1']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e1']['readable_date'] ?? null
                ],
                'e2' => [
                    'start' => $deadlines['U']['e2']['start'] ?? null,
                    'end' => $deadlines['U']['e2']['end'] ?? null,
                    'has_started' => $deadlines['U']['e2']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e2']['readable_date'] ?? null
                ],
                'e3' => [
                    'start' => $deadlines['U']['e3']['start'] ?? null,
                    'end' => $deadlines['U']['e3']['end'] ?? null,
                    'has_started' => $deadlines['U']['e3']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e3']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e3']['readable_date'] ?? null
                ],
                'e4' => [
                    'start' => $deadlines['U']['e4']['start'] ?? null,
                    'end' => $deadlines['U']['e4']['end'] ?? null,
                    'has_started' => $deadlines['U']['e4']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e4']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e4']['readable_date'] ?? null
                ],
                'p0' => [
                    'start' => $deadlines['U']['p0']['start'] ?? null,
                    'end' => $deadlines['U']['p0']['end'] ?? null,
                    'has_started' => $deadlines['U']['p0']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['p0']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['p0']['readable_date'] ?? null
                ]
            ],
            'L' => [
                'e1' => [
                    'start' => $deadlines['L']['e1']['start'] ?? null,
                    'end' => $deadlines['L']['e1']['end'] ?? null,
                    'has_started' => $deadlines['L']['e1']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e1']['readable_date'] ?? null
                ],
                'e2' => [
                    'start' => $deadlines['L']['e2']['start'] ?? null,
                    'end' => $deadlines['L']['e2']['end'] ?? null,
                    'has_started' => $deadlines['L']['e2']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e2']['readable_date'] ?? null
                ],
                'e3' => [
                    'start' => $deadlines['L']['e3']['start'] ?? null,
                    'end' => $deadlines['L']['e3']['end'] ?? null,
                    'has_started' => $deadlines['L']['e3']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e3']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e3']['readable_date'] ?? null
                ],
                'e4' => [
                    'start' => $deadlines['L']['e4']['start'] ?? null,
                    'end' => $deadlines['L']['e4']['end'] ?? null,
                    'has_started' => $deadlines['L']['e4']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e4']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e4']['readable_date'] ?? null
                ],
                'e5' => [
                    'start' => $deadlines['L']['e5']['start'] ?? null,
                    'end' => $deadlines['L']['e5']['end'] ?? null,
                    'has_started' => $deadlines['L']['e5']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e5']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e5']['readable_date'] ?? null
                ],
                'e6' => [
                    'start' => $deadlines['L']['e6']['start'] ?? null,
                    'end' => $deadlines['L']['e6']['end'] ?? null,
                    'has_started' => $deadlines['L']['e6']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e6']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e6']['readable_date'] ?? null
                ],
                'p1' => [
                    'start' => $deadlines['L']['p1']['start'] ?? null,
                    'end' => $deadlines['L']['p1']['end'] ?? null,
                    'has_started' => $deadlines['L']['p1']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['p1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['p1']['readable_date'] ?? null
                ],
                'p2' => [
                    'start' => $deadlines['L']['p2']['start'] ?? null,
                    'end' => $deadlines['L']['p2']['end'] ?? null,
                    'has_started' => $deadlines['L']['p2']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['p2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['p2']['readable_date'] ?? null
                ]
            ],
            'f' => [
                'f' => [
                    'start' => $deadlines['f']['f']['start'] ?? null,
                    'end' => $deadlines['f']['f']['end'] ?? null,
                    'has_started' => $deadlines['f']['f']['has_started'] ?? null,
                    'has_ended' => $deadlines['f']['f']['has_ended'] ?? null,
                    'readable_date' => $deadlines['f']['f']['readable_date'] ?? null
                ],
                'w' => [
                    'start' => $deadlines['f']['w']['start'] ?? null,
                    'end' => $deadlines['f']['w']['end'] ?? null,
                    'has_started' => $deadlines['f']['w']['has_started'] ?? null,
                    'has_ended' => $deadlines['f']['w']['has_ended'] ?? null,
                    'readable_date' => $deadlines['f']['w']['readable_date'] ?? null
                ]
            ]
        ];
    } elseif ($tierTeamSlot == 16) {
        $mergedDeadlines = [
            'U' => [
                'e1' => [
                    'start' => $deadlines['U']['e1']['start'] ?? null,
                    'end' => $deadlines['U']['e1']['end'] ?? null,
                    'has_started' => $deadlines['U']['e1']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e1']['readable_date'] ?? null
                ],
                'e2' => [
                    'start' => $deadlines['U']['e2']['start'] ?? null,
                    'end' => $deadlines['U']['e2']['end'] ?? null,
                    'has_started' => $deadlines['U']['e2']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e2']['readable_date'] ?? null
                ],
                'e3' => [
                    'start' => $deadlines['U']['e3']['start'] ?? null,
                    'end' => $deadlines['U']['e3']['end'] ?? null,
                    'has_started' => $deadlines['U']['e3']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e3']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e3']['readable_date'] ?? null
                ],
                'e5' => [
                    'start' => $deadlines['U']['e5']['start'] ?? null,
                    'end' => $deadlines['U']['e5']['end'] ?? null,
                    'has_started' => $deadlines['U']['e5']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e5']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e5']['readable_date'] ?? null
                ],
                'p0' => [
                    'start' => $deadlines['U']['p0']['start'] ?? null,
                    'end' => $deadlines['U']['p0']['end'] ?? null,
                    'has_started' => $deadlines['U']['p0']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['p0']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['p0']['readable_date'] ?? null
                ]
            ],
            'L' => [
                'e1' => [
                    'start' => $deadlines['L']['e1']['start'] ?? null,
                    'end' => $deadlines['L']['e1']['end'] ?? null,
                    'has_started' => $deadlines['L']['e1']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e1']['readable_date'] ?? null
                ],
                'e2' => [
                    'start' => $deadlines['L']['e2']['start'] ?? null,
                    'end' => $deadlines['L']['e2']['end'] ?? null,
                    'has_started' => $deadlines['L']['e2']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e2']['readable_date'] ?? null
                ],
                'e3' => [
                    'start' => $deadlines['L']['e3']['start'] ?? null,
                    'end' => $deadlines['L']['e3']['end'] ?? null,
                    'has_started' => $deadlines['L']['e3']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e3']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e3']['readable_date'] ?? null
                ],
                'e4' => [
                    'start' => $deadlines['L']['e4']['start'] ?? null,
                    'end' => $deadlines['L']['e4']['end'] ?? null,
                    'has_started' => $deadlines['L']['e4']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e4']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e4']['readable_date'] ?? null
                ],
                'p1' => [
                    'start' => $deadlines['L']['p1']['start'] ?? null,
                    'end' => $deadlines['L']['p1']['end'] ?? null,
                    'has_started' => $deadlines['L']['p1']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['p1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['p1']['readable_date'] ?? null
                ],
                'p2' => [
                    'start' => $deadlines['L']['p2']['start'] ?? null,
                    'end' => $deadlines['L']['p2']['end'] ?? null,
                    'has_started' => $deadlines['L']['p2']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['p2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['p2']['readable_date'] ?? null
                ]
            ],
            'f' => [
                'f' => [
                    'start' => $deadlines['f']['f']['start'] ?? null,
                    'end' => $deadlines['f']['f']['end'] ?? null,
                    'has_started' => $deadlines['f']['f']['has_started'] ?? null,
                    'has_ended' => $deadlines['f']['f']['has_ended'] ?? null,
                    'readable_date' => $deadlines['f']['f']['readable_date'] ?? null
                ],
                'w' => [
                    'start' => $deadlines['f']['w']['start'] ?? null,
                    'end' => $deadlines['f']['w']['end'] ?? null,
                    'has_started' => $deadlines['f']['w']['has_started'] ?? null,
                    'has_ended' => $deadlines['f']['w']['has_ended'] ?? null,
                    'readable_date' => $deadlines['f']['w']['readable_date'] ?? null
                ]
            ]
        ];
    } elseif ($tierTeamSlot == 8) {
        $mergedDeadlines = [
            'U' => [
                'e1' => [
                    'start' => $deadlines['U']['e1']['start'] ?? null,
                    'end' => $deadlines['U']['e1']['end'] ?? null,
                    'has_started' => $deadlines['U']['e1']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e1']['readable_date'] ?? null
                ],
                'e2' => [
                    'start' => $deadlines['U']['e2']['start'] ?? null,
                    'end' => $deadlines['U']['e2']['end'] ?? null,
                    'has_started' => $deadlines['U']['e2']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['e2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['e2']['readable_date'] ?? null
                ],
                'p0' => [
                    'start' => $deadlines['U']['p0']['start'] ?? null,
                    'end' => $deadlines['U']['p0']['end'] ?? null,
                    'has_started' => $deadlines['U']['p0']['has_started'] ?? null,
                    'has_ended' => $deadlines['U']['p0']['has_ended'] ?? null,
                    'readable_date' => $deadlines['U']['p0']['readable_date'] ?? null
                ]
            ],
            'L' => [
                'e1' => [
                    'start' => $deadlines['L']['e1']['start'] ?? null,
                    'end' => $deadlines['L']['e1']['end'] ?? null,
                    'has_started' => $deadlines['L']['e1']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e1']['readable_date'] ?? null
                ],
                'e2' => [
                    'start' => $deadlines['L']['e2']['start'] ?? null,
                    'end' => $deadlines['L']['e2']['end'] ?? null,
                    'has_started' => $deadlines['L']['e2']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['e2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['e2']['readable_date'] ?? null
                ],
                'p1' => [
                    'start' => $deadlines['L']['p1']['start'] ?? null,
                    'end' => $deadlines['L']['p1']['end'] ?? null,
                    'has_started' => $deadlines['L']['p1']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['p1']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['p1']['readable_date'] ?? null
                ],
                'p2' => [
                    'start' => $deadlines['L']['p2']['start'] ?? null,
                    'end' => $deadlines['L']['p2']['end'] ?? null,
                    'has_started' => $deadlines['L']['p2']['has_started'] ?? null,
                    'has_ended' => $deadlines['L']['p2']['has_ended'] ?? null,
                    'readable_date' => $deadlines['L']['p2']['readable_date'] ?? null
                ]
            ],
            'f' => [
                'f' => [
                    'start' => $deadlines['f']['f']['start'] ?? null,
                    'end' => $deadlines['f']['f']['end'] ?? null,
                    'has_started' => $deadlines['f']['f']['has_started'] ?? null,
                    'has_ended' => $deadlines['f']['f']['has_ended'] ?? null,
                    'readable_date' => $deadlines['f']['f']['readable_date'] ?? null
                ],
                'w' => [
                    'start' => $deadlines['f']['w']['start'] ?? null,
                    'end' => $deadlines['f']['w']['end'] ?? null,
                    'has_started' => $deadlines['f']['w']['has_started'] ?? null,
                    'has_ended' => $deadlines['f']['w']['has_ended'] ?? null,
                    'readable_date' => $deadlines['f']['w']['readable_date'] ?? null
                ]
            ]
        ];
    }

    // dd($mergedDeadlines);

    return $mergedDeadlines;
}
}