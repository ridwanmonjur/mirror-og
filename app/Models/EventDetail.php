<?php

namespace App\Models;

use App\Exceptions\TimeGreaterException;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Illuminate\Validation\UnauthorizedException;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;


class EventDetail extends Model implements Feedable
{
    use HasFactory;

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->id)
            ->title("Results: {$this->eventName}")
            ->summary(Str::limit($this->eventDescription ?? '', 155))
            ->updated($this->updated_at)
            ->link(route('public.event.view', [
                'id' => $this->id
            ]))
            ->authorName($this->user->name ?? 'Anonymous');
    }

    public static function getFeedItems()
    {
        return EventDetail::with(['user'])
            ->whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    protected $table = 'event_details';
    protected $fillable = [
        'eventDefinitions',
        'eventName',
        'startDate',
        'endDate',
        'startTime',
        'endTime',
        'eventDescription',
        'eventBanner',
        'eventTags',
        'status',
        'venue',
        'sub_action_public_date',
        'sub_action_public_time',
        'sub_action_private',
        'user_id',
        'event_type_id',
        'event_tier_id',
        'event_category_id',
        'payment_transaction_id',
        'willNotify',
    ];

    protected $guarded = [];

    protected $casts = [
        'eventTags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function invitationList(): HasMany
    {
        return $this->hasMany(EventInvitation::class, 'event_id');
    }

    public function tier(): BelongsTO
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }


    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(RecordStripe::class, 'payment_transaction_id');
    }

    public function joinEvents(): HasMany
    {
        return $this->hasMany(JoinEvent::class, 'event_details_id', 'id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Brackets::class, 'event_details_id', 'id');
    }

    public function signup(): HasOne
    {
        return $this->hasOne(EventSignup::class, 'event_id');
    }

    public function deadlines()
    {
        return $this->hasMany(BracketDeadline::class, 'event_details_id');
    }

    public static function destroyEventBanner(string| null $file): void
    {
        $fileNameInitial = str_replace('images/events/', '', $file);
        $fileNameFinal = "images/events/{$fileNameInitial}";

        if (file_exists($fileNameFinal)) {
            unlink($fileNameFinal);
        }
    }

    public static function processEvents(Collection $events, array $isFollowing): array
    {
        $activeEvents = collect();
        $historyEvents = collect();

        foreach ($events as $joinEvent) {
            $joinEvent->status = $joinEvent->statusResolved();
            $joinEvent->isFollowing = array_key_exists($joinEvent->user_id, $isFollowing);

            if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING'])) {
                $activeEvents[] = $joinEvent;
            } elseif ($joinEvent->status === 'ENDED') {
                $historyEvents[] = $joinEvent;
            }
        }

        return ['joinEvents' => $events, 'activeEvents' => $activeEvents, 'historyEvents' => $historyEvents];
    }

    public function isCompleteEvent(): bool
    {
        $isComplete = true;
        if (
            is_null($this->eventName) ||
            is_null($this->startDate) ||
            is_null($this->endDate) ||
            is_null($this->startTime) ||
            is_null($this->endTime) ||
            is_null($this->eventDescription) ||
            is_null($this->eventBanner) ||
            is_null($this->status) ||
            is_null($this->event_type_id) ||
            is_null($this->event_tier_id) ||
            is_null($this->event_category_id) ||
            is_null($this->sub_action_private) ||
            is_null($this->payment_transaction_id)
        ) {
            return false;
        }

        return $isComplete;
    }

    public function createStatusUpdateTask()
    {
        $now = now();

        $status = $this->statusResolved();
        Task::where('taskable_id', $this->getKey())
            ->where('taskable_type', EventDetail::class)
            ->whereIn('task_name', ['started', 'ended', 'live'])
            ->delete();

        if ($status !== 'PENDING' && $status!= 'DRAFT' && $status !== 'PREVIEW') {
            $tasksData = [
                [
                    'taskable_id' => $this->getKey(),
                    'taskable_type' => EventDetail::class,                    
                    'task_name' => 'started',
                    'action_time' => $this->startDate . ' ' . $this->startTime,
                    'created_at' => $now,
                ],
                [
                    'taskable_id' => $this->getKey(),
                    'taskable_type' => EventDetail::class,  
                    'task_name' => 'ended',
                    'action_time' => $this->endDate . ' ' . $this->endTime,
                    'created_at' => $now,
                ],
            ];

            if ($this->sub_action_public_date) {
                $tasksData[] = 
                [
                    'task_name' => 'live',
                    'action_time' => $this->sub_action_public_date . ' ' . $this->sub_action_public_time,
                    'created_at' => $now,
                    'taskable_id' => $this->getKey(),
                    'taskable_type' => EventDetail::class,  
                ];
                
            }

            Task::insert($tasksData);

        }
    }

    public function createRegistrationTask(): void {
        if ($this->event_tier_id && $this->event_type_id) {
            $signupValues = DB::table('event_tier_type_signup_dates')
                ->where('tier_id', $this->event_tier_id)
                ->where('type_id', $this->event_type_id)
                ->first();

            if (!$signupValues) {
                DB::table('event_tier_type_signup_dates')->insert([
                    'tier_id' => $this->event_tier_id,
                    'type_id' => $this->event_type_id,
                    'signup_open' => 800, // Default: 28 days before event
                    'signup_close' => 1,  // Default: 3 days before event
                    'normal_signup_start_advanced_close' => 7 // Default: 7 days before event
                ]);
                
                $signupValues = DB::table('event_tier_type_signup_dates')
                    ->where('tier_id', $this->event_tier_id)
                    ->where('type_id', $this->event_type_id)
                    ->first();
            }
                
            $startDateTime = Carbon::parse($this->startDate . ' ' . $this->startTime);
            
            $finalDate = $startDateTime->copy()->subDays($signupValues->normal_signup_start_advanced_close);
            $insertData = [
                'event_id' => $this->id,
                'signup_open' => $startDateTime->copy()->subDays($signupValues->signup_open),
                'signup_close' => $startDateTime->copy()->subDays($signupValues->signup_close),
                'normal_signup_start_advanced_close' => $finalDate
            ];
            
            DB::table('event_signup_dates')->updateOrInsert(
                ['event_id' => $this->id],
                $insertData
            );

            Task::updateOrCreate([
                'taskable_id' => $this->getKey(),
                'taskable_type' => EventDetail::class,                    
                'task_name' => 'reg_over',
            ], [
                'action_time' => $finalDate->format('Y-m-d H:i:s'),
            ]);
        }
    }

    public function createDeadlinesTask(): void
    {
        $deadlineSetup = BracketDeadlineSetup::where('tier_id', $this->event_tier_id)->first();
            
        if (!$deadlineSetup) {
            return; 
        }

        $deadlines = BracketDeadline::where('event_details_id', $this->id)->get();
        $deadlinesPast = $deadlines->pluck("id");
        
        Task::whereIn('taskable_id', $deadlinesPast)
            ->where('taskable_type', BracketDeadline::class)
            ->delete();
        
        BracketDeadline::whereIn('id', $deadlinesPast)->delete();
        
        $deadlineConfig = $deadlineSetup->deadline_config;
        
        $baseDateTime = Carbon::parse($this->startDate . ' ' . $this->startTime);
        $now = now();
        
        $deadlinesToCreate = [];
        $tasksToCreate = [];

        foreach ($deadlineConfig as $stage => $innerStages) {
            foreach ($innerStages as $innerStage => $times) {
                $startDays = $times['start'];
                $endDays = $times['end'];
                $startReport = $baseDateTime->copy()->addDays($startDays)->toDateTimeString();
                $endReport = $baseDateTime->copy()->addDays($endDays)->toDateTimeString();
                
                $deadlinesToCreate[] = [
                    'event_details_id' => $this->id,
                    'stage' => $stage,
                    'inner_stage' => $innerStage,
                    'start_date' => $startReport,
                    'end_date' => $endReport,
                    'created_at' => $now,
                ];
            }
        }
        
        BracketDeadline::insert($deadlinesToCreate);
        
        $createdDeadlines = BracketDeadline::where('event_details_id', $this->id)
            ->where('created_at', $now)
            ->get()
            ->keyBy(function ($deadline) {
                return $deadline->stage . '_' . $deadline->inner_stage;
            });
        
        foreach ($deadlineConfig as $stage => $innerStages) {
            foreach ($innerStages as $innerStage => $times) {
                $startDays = $times['start'];
                $endDays = $times['end'];
                $startReport = $baseDateTime->copy()->addDays($startDays)->toDateTimeString();
                $endReport = $baseDateTime->copy()->addDays($endDays)->toDateTimeString();
                $orgReport = $baseDateTime->copy()->addDays($endDays)
                    ->addHours(12)->toDateTimeString();
                
                $deadlineKey = $stage . '_' . $innerStage;
                $deadline = $createdDeadlines[$deadlineKey];
                
                $tasksToCreate = [...$tasksToCreate, 
                    [
                        'taskable_id' => $deadline->id,
                        'taskable_type' => BracketDeadline::class,
                        'task_name' => 'start_report',
                        'action_time' => $startReport,
                        'created_at' => $now,
                    ], 
                    [
                        'taskable_id' => $deadline->id,
                        'taskable_type' => BracketDeadline::class,
                        'task_name' => 'end_report',
                        'action_time' => $endReport,
                        'created_at' => $now,
                    ],
                    [
                        'taskable_id' => $deadline->id,
                        'taskable_type' => BracketDeadline::class,
                        'task_name' => 'org_report',
                        'action_time' => $orgReport,
                        'created_at' => $now,
                    ],
                ];
            }
        }
        
        Task::insert($tasksToCreate);
    }

    public function statusResolved(): string
    {
        
        if (in_array($this->status, ['DRAFT', 'ENDED', 'PENDING' ])) {
            return $this->status;
        }

        if (in_array($this->status, [ 'PREVEW'])) {
            return 'DRAFT';
        }
        if (is_null($this->payment_transaction_id) || $this->status === 'PENDING') {
            return 'PENDING';
        }

        // PROBABLY CAN REMOVE THIS FUNCTION NOW!!!
        $carbonPublishedDateTime = $this->createCarbonDateTimeFromDB(
            $this->sub_action_public_date,
            $this->sub_action_public_time
        );

        $carbonEndDateTime = $this->createCarbonDateTimeFromDB(
            $this->endDate,
            $this->endTime
        );
        
        $carbonStartDateTime = $this->createCarbonDateTimeFromDB(
            $this->startDate,
            $this->startTime
        );

        $carbonNow = Carbon::now()->utc();

        if (! $carbonEndDateTime || ! $carbonStartDateTime) {
            Log::error('EventDetail.php: statusResolved: EventDetail with id= '.$this->id
                .' and name= '.$this->eventName.' has null end or start date time');

            return 'ERROR';
        }
        if ($carbonEndDateTime < $carbonNow) {
            return 'ENDED';
        }

        if ($carbonStartDateTime < $carbonNow) {
            return 'ONGOING';
        }
        if ($carbonPublishedDateTime && $carbonPublishedDateTime > $carbonNow) {
            return 'SCHEDULED';
        }

        return 'UPCOMING';
    }

    public function getFormattedStartDate()
    {
        return Carbon::parse($this->startDate . ' ' . $this->startTime)->diffForHumans();
    }

    public function getRegistrationStatus(): string
    {
        $signupDates = $this->signup;

        if (!$signupDates) {
            return config('constants.SIGNUP_STATUS.CLOSED');
        }

        $now = Carbon::now();
        if ($now->between($signupDates->signup_open, $signupDates->normal_signup_start_advanced_close)) {
            return config('constants.SIGNUP_STATUS.EARLY');
        } elseif ($now->between($signupDates->normal_signup_start_advanced_close, $signupDates->signup_close)) {
            return config('constants.SIGNUP_STATUS.NORMAL');
        } 
        // elseif ($now->lt($signupDates->signup_open)) { 
        //     return config('constants.SIGNUP_STATUS.TOO_EARLY');
        // } 
        else {
            return config('constants.SIGNUP_STATUS.NORMAL');
        }
    }

    public function fixTimeToRemoveSeconds(string| null $time): string| null
    {
        if ($time === null) {
            return null;
        }
        if (substr_count($time, ':') === 2) {
            $time = explode(':', $time);
            return $time[0].':'.$time[1];
        }
        return $time;
    }

    public function createCarbonDateTimeFromDB(string| null $date, string| null $time): ?string
    {
        if ($date === null || $time === null) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i', $date.' '.$this->fixTimeToRemoveSeconds($time))
            ->utc();
    }

    public function createCarbonDateTime(string| null $date, string| null $time): ?Carbon
    {
        if ($date === null || $time === null) {
            return null;
        }
        return Carbon::createFromFormat('Y-m-d H:i', $date.' '.$this->fixTimeToRemoveSeconds($time))->utc();
    }

    function startDatesStr($startDate, $startTime)
{
    $startTime = $this->fixTimeToRemoveSeconds($startTime);
    if ($startDate !== null && $startTime !== null) {
        $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, 'UTC');
        $carbonDateTimeMalaysia = $carbonDateTimeUtc->setTimezone('Asia/Kuala_Lumpur');
        $datePart = $carbonDateTimeMalaysia->format('d M y');
        $timePart = $carbonDateTimeMalaysia->format('g:i A');
        $dayStr = $carbonDateTimeMalaysia->englishDayOfWeek;
        $dateStr = $datePart.' '.$timePart;
        $combinedStr = $datePart.' ('.$dayStr.')';
        
    } else {
        $datePart = 'Date is not set';
        $timePart = 'Time is not set';
        $dayStr = '';
        $dateStr = 'Please enter date and time';
        $combinedStr = 'Date/time is not set';
    }

    return [
        'datePart' => $datePart,
        'dateStr' => $dateStr,
        'timePart' => $timePart,
        'dayStr' => $dayStr,
        'combinedStr' => $combinedStr,
    ];
}


    function startDatesReadable(bool $willShowCountDown = false): array
    {
        // Use Malaysia timezone for current time
        $now = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $startsIn = null;
        $formattedDate = 'No date';
        $formattedTime = 'No time';
        $startsIn = 'Not available';
        if ($this->startDate !== null && $this->startTime !== null) {

            $startTime = $this->fixTimeToRemoveSeconds($this->startTime);
            $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i', $this->startDate.' '.$startTime, 'UTC') ?? null;
            if ($carbonDateTimeUtc) {
                $carbonDateTimeMalaysia = $carbonDateTimeUtc->setTimezone('Asia/Kuala_Lumpur');
                $formattedDate = $carbonDateTimeMalaysia->format('d M y');
                $formattedTime = $carbonDateTimeMalaysia->format('g:i A');
                
                if ($willShowCountDown) {
                    $nowCarbon = Carbon::now('Asia/Kuala_Lumpur');
                    $diff = $nowCarbon->diff($carbonDateTimeMalaysia);
                    if ($diff->days > 0) {
                        $startsIn = $diff->days . 'd ';
                    }
                    $startsIn .= $diff->h . 'h';
                } 
            } 
        }
        
        return [
            'fmtStartDt' => $formattedDate,
            'fmtStartT' => $formattedTime,
            'fmtStartIn' => $startsIn,
        ];
    }

 
    public static function generateOrganizerPartialQueryForFilter(Request $request)
    {
        $eventListQuery = self::query();
        $eventListQuery->when($request->has('status'), function ($query) use ($request) {
            $status = $request->input('status');
            if (is_array($status)) {
                $status = $status[0];
            }

            if (empty(trim($status))) {
                return $query;
            }

            $currentDateTime = Carbon::now()->utc();
            if ($status === 'DRAFT') {
                return $query->whereIn('status', ['DRAFT', 'PREVIEW']);
            }
            if ($status === 'PENDING') {
                return $query->where('status', 'PENDING')->orWhereNull('payment_transaction_id');
            }
            if ($status === 'ENDED') {
                return $query
                    ->whereRaw('CONCAT(endDate, " ", endTime) < ?', [$currentDateTime])
                    ->where('status', '<>', 'PREVIEW')
                    ->whereNotNull('payment_transaction_id')
                    ->where('status', '<>', 'DRAFT');
            }
            if ($status === 'LIVE') {
                return $query
                    ->where(function ($query) use ($currentDateTime) {
                        return $query
                            ->whereNull('sub_action_public_date')
                            ->orWhereNull('sub_action_public_time')
                            ->orWhereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) < ?', [$currentDateTime]);
                    })
                    ->whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
                    ->whereNotNull('payment_transaction_id')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            } elseif ($status === 'SCHEDULED') {
                return $query
                    ->whereNotNull('sub_action_public_date')
                    ->whereNotNull('sub_action_public_time')
                    ->whereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime])
                    ->where('status', '<>', 'DRAFT')
                    ->where('status', '<>', 'PREVIEW')
                    ->whereNotNull('payment_transaction_id')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            }
        });

        $eventListQuery->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            }
            return $query->where(function ($q) use ($search) {
                return $q
                    ->orWhere('eventDescription', 'LIKE', "%{$search}%")
                    ->orWhere('eventName', 'LIKE', "%{$search}%")
                    ->orWhere('eventTags', 'LIKE', "%{$search}%")
                    ->orWhereHas('game', function ($query) use ($search) {
                        $query->where('gameTitle', 'LIKE', "%{$search}%");
                    });
            });
        });

        return $eventListQuery;
    }

    public static function landingPageQuery(Request $request, $currentDateTime) {
        return self::whereNotIn('status', ['DRAFT', 'PENDING'])
        ->whereNotNull('payment_transaction_id')
        ->where('sub_action_private', '<>', 'private')
        ->where(function ($query) use ($currentDateTime) {
            $query
                ->whereRaw('CONCAT(sub_action_public_time, " ", sub_action_public_date) < ?', [$currentDateTime])
                ->orWhereNull('sub_action_public_time')
                ->orWhereNull('sub_action_public_date');
        })
        ->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            }

            return $query->where('eventName', 'LIKE', "%{$search}%")->orWhere('eventTags', 'LIKE', "%{$search}%");
        })
        ->with(['tier', 'type', 'game', 'signup'])
        ->with(['user' => function($q) {
            $q->select('id', 'name')
              ->with(['organizer' => function ($innerQ) {
                  $innerQ->select(['id', 'user_id']);
              }])
              ->withCount('follows');
        }])
        ->withCount(
        ['joinEvents' => function ($q) {
            $q->where('join_status', 'confirmed');
        }]);
    }

    public static function generateOrganizerFullQueryForFilter(Request $request)
    {
        $eventListQuery = self::generateOrganizerPartialQueryForFilter($request);

        $eventListQuery->when($request->has('sort'), function ($query) use ($request) {
            $sort = $request->input('sort');
            foreach ($sort as $key => $value) {
                if (! empty(trim($key)) && $value !== 'none') {
                    if ($key === 'startDate') {
                        $query->orderBy('startDate', $value)->orderBy('startTime', $value);
                    } elseif ($key === 'prize') {
                        $query
                            ->join('event_tier', 'event_details.event_tier_id', '=', 'event_tier.id')
                            ->orderBy('event_tier.tierPrizePool', $value);
                    } else {
                        $query->orderBy($key, $value);
                    }
                }
            }

            return $query;
        });

        $eventListQuery->when($request->has('filter'), function ($query) use ($request) {
            $filter = $request->input('filter');
            if (array_key_exists('eventTier[]', $filter)) {
                if (isset($filter['eventTier[]'][0])) {
                    $query->whereIn('event_tier_id', $filter['eventTier[]']);
                }
            }

            if (array_key_exists('eventType[]', $filter)) {
                if (isset($filter['eventType[]'][0])) {
                    $query->whereIn('event_type_id', $filter['eventType[]']);
                }
            }

            if (array_key_exists('gameTitle[]', $filter)) {
                if (isset($filter['gameTitle[]'][0])) {
                    $query->whereIn('event_category_id', $filter['gameTitle[]']);
                }
            }

            if (array_key_exists('date[]', $filter)) {
                $dates = $filter['date[]'];

                if (isset($dates[0]) && isset($dates[1]) && $dates[0] !== '' && $dates[1] !== '') {
                    $dates[0] = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
                    $dates[1] = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');
                    
                    $query->whereBetween('created_at', [$dates[0], $dates[1]]);
                }
            }

            return $query;
        });

        return $eventListQuery;
    }

    public static function generateParticipantFullQueryForFilter(Request $request)
    {
        $eventListQuery = self::query();
        $currentDateTime = Carbon::now()->utc();
        $eventListQuery->where('status', '<>', 'DRAFT')
            ->whereNotNull('payment_transaction_id')
            ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime])
            ->where('sub_action_private', '<>', 'private')
            ->where(function ($query) use ($currentDateTime) {
                $query
                    ->whereRaw('CONCAT(sub_action_public_time, " ", sub_action_public_date) < ?', [$currentDateTime])
                    ->orWhereNull('sub_action_public_time')
                    ->orWhereNull('sub_action_public_date');
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                if (empty($search)) {
                    return $query;
                }
                return $query->where(function ($q) use ($search) {
                    return $q
                        ->orWhere('eventDescription', 'LIKE', "%{$search}%")
                        ->orWhere('eventName', 'LIKE', "%{$search}%")
                        ->orWhere('eventTags', 'LIKE', "%{$search}%")
                        ->orWhereHas('game', function ($query) use ($search) {
                            $query->where('gameTitle', 'LIKE', "%{$search}%");
                        });
                });
            });

        return $eventListQuery;
    }

   

    public static function storeLogic(EventDetail $eventDetail, Request $request): mixed
    {
        try {
            if ($request->hasFile('eventBanner')) {
                if ($eventDetail->eventBanner) {
                    self::destroyEventBanner($eventDetail->eventBanner);
                }

                $eventDetail->eventBanner = self::storeEventBanner($request->file('eventBanner'));
            }
        } catch (Exception $e) {
            throw new TimeGreaterException($e->getMessage());
        }

        $isEditMode = $eventDetail->id !== null;
        $isDraftMode = $request->launch_visible === 'DRAFT';
        $isTimeSame = true;
        $isPreviewMode = $isEditMode ? false : $request->livePreview === 'true';
        $carbonStartDateTime = $carbonEndDateTime = $carbonPublishedDateTime = null;
        $eventDetail->event_type_id = $request->eventTypeId;
        $eventDetail->event_tier_id = $request->eventTierId;
        $eventDetail->event_category_id = $request->gameTitleId;

        $startDate = $request->startDate;
        $startTime = $eventDetail->fixTimeToRemoveSeconds($request->startTime);
        $endDate = $request->endDate;
        $endTime = $eventDetail->fixTimeToRemoveSeconds($request->endTime);

        if ($startDate && $startTime) {
            $carbonStartDateTime = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime)->utc();
            if ($isEditMode) {
                $eventDetailStartDateTime = $eventDetail->createCarbonDateTime($eventDetail->startDate, $eventDetail->startTime);
                $isTimeSame = $carbonStartDateTime->eq($eventDetailStartDateTime);
            }

            $eventDetail->startDate = $carbonStartDateTime->format('Y-m-d');
            $eventDetail->startTime = $carbonStartDateTime->format('H:i');
        } elseif ($isPreviewMode && ! $isEditMode) {
            $eventDetail->startDate = null;
            $eventDetail->startTime = null;
        } elseif (! $isDraftMode) {
            throw new TimeGreaterException('Start date and time must be greater than current date and time.');
        } elseif ($isDraftMode) {
            $eventDetail->startDate = $request->startDate;
            $eventDetail->startTime = $request->startTime;
        }

        if ($endDate && $endTime) {
            $carbonEndDateTime = Carbon::createFromFormat('Y-m-d H:i', $endDate.' '.$endTime)->utc();
            if ($isEditMode) {
                $eventEndDateTime = $eventDetail->createCarbonDateTime( $eventDetail->endDate, $eventDetail->endTime);
                $isTimeSame = $isTimeSame && $carbonEndDateTime->eq($eventEndDateTime);
            }
            if ($startDate && $startTime && $carbonEndDateTime > $carbonStartDateTime) {
                $eventDetail->endDate = $carbonEndDateTime->format('Y-m-d');
                $eventDetail->endTime = $carbonEndDateTime->format('H:i');
            } elseif ($isPreviewMode && ! $isEditMode) {
                $eventDetail->endDate = null;
                $eventDetail->endTime = null;
            } elseif (! $isDraftMode) {
                throw new TimeGreaterException('End date and time must be greater than start date and time.');
            } elseif ($isDraftMode) {
                $eventDetail->endDate = $request->endDate;
                $eventDetail->endTime = $request->endTime;
            }
        }

        if (!isset($request->eventName)){
            throw new TimeGreaterException('An event name is required, but you have entered no name.');

        }

        $eventDetail->eventName = $request->eventName;
        $eventDetail->eventDescription = $request->eventDescription;
        $eventDetail->eventTags = $request->eventTags;

        if ($request->launch_visible === 'DRAFT') {
            $eventDetail->status = 'DRAFT';
            $eventDetail->sub_action_public_date = null;
            $eventDetail->sub_action_public_time = null;
            $eventDetail->sub_action_private = 'private';
        } else {
            $launch_date = null;
            $launch_time = null;
            $eventDetail->sub_action_private = $request->launch_visible;
            if ($request->launch_visible === 'public') {
                $launch_date = $request->launch_date_public;
                $launch_time = $eventDetail->fixTimeToRemoveSeconds($request->launch_time_public);
            } else {
                $launch_date = $request->launch_date_private;
                $launch_time = $eventDetail->fixTimeToRemoveSeconds($request->launch_time_private);
            }

            if ($request->launch_schedule === 'schedule' && $launch_date && $launch_time) {
                $carbonPublishedDateTime = Carbon::createFromFormat('Y-m-d H:i', $launch_date.' '.$launch_time)->utc();
                // @phpstan-ignore-next-line
                if ($launch_date && $launch_time && $carbonPublishedDateTime < $carbonStartDateTime && $carbonPublishedDateTime < $carbonEndDateTime) {
                    $eventDetail->status = 'SCHEDULED';
                    $eventDetail->sub_action_public_date = $carbonPublishedDateTime->format('Y-m-d');
                    $eventDetail->sub_action_public_time = $carbonPublishedDateTime->format('H:i');
                } else {
                    throw new TimeGreaterException('Published time must be before start time and end time.');
                }
            } elseif ($request->launch_schedule === 'now') {
                $eventDetail->status = 'UPCOMING';
                $eventDetail->sub_action_public_date = null;
                $eventDetail->sub_action_public_time = null;
            } else {
                throw new TimeGreaterException('Scheduled event without date and time.');
            }
        }

        $eventDetail->status = $eventDetail->statusResolved();
        $eventDetail->willNotify = true;
        $eventDetail->slug = Str::slug($eventDetail->eventName);

        return [$eventDetail, $isTimeSame];
    }

    public static function findEventWithRelationsAndThrowError(
        string| int | null $userId,
        string| int $id,
        array| null $where = null,
        string | array| null $relations = null,
        string | null | array $relationCount = null
    ): EventDetail {
        $relations ??= ['type', 'tier', 'game'];
        $eventQuery = self::with($relations);
        if ($where !== null) {
            $eventQuery->where($where);
        }

        if ($relationCount) {
            $eventQuery->withCount($relationCount);
        }

        $event = $eventQuery->find($id);

        if (is_null($event)) {
            throw new ModelNotFoundException("Event not found with id: {$id}");
        }

        if ($userId && $event->user_id !== $userId) {
            throw new UnauthorizedException('You cannot view an event of another organizer!');
        }
        
        return $event;
    }

    public static function findEventAndThrowError(string| int $eventId, string| int $userId): EventDetail
    {
        $event = self::find($eventId);

        if (! $event) {
            throw new ModelNotFoundException("Event not found with id: {$eventId}");
        }
        if ($event->user_id !== $userId) {
            throw new UnauthorizedException('You cannot view an event of another organizer!');
        }
        return $event;
    }

    private static function storeEventBanner(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        if (! $extension) {
            throw new Exception('File extension not retrieved!');
        }

        $fileNameInitial = 'eventBanner-'.time().'.'.$extension;
        $fileNameFinal = "images/events/{$fileNameInitial}";
        $file->storeAs('images/events/', $fileNameInitial);

        return $fileNameFinal;
    }

    public function scopeWithEventTierAndFilteredMatches($query, $bracketDeadlines)
    {
        return $query->with(['tier', 'matches' => function($query) use ($bracketDeadlines) {
            $query->filterByDeadlines($bracketDeadlines);
        }]);
    }
}
