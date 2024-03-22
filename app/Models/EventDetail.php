<?php

namespace App\Models;

use App\Exceptions\TimeGreaterException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

class EventDetail extends Model
{
    use HasFactory;
    protected $table = 'event_details';

    protected $guarded = [];

    protected $casts = [
        'eventTags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
  
    public function invitationList()
    {
        return $this->hasMany(EventInvitation::class, 'event_id');
    }

    public function tier()
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }

    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function game()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function payment_transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function joinEvent()
    {
        return $this->hasMany(JoinEvent::class);
    }

    public function joinEvents()
    {
        return $this->hasMany(JoinEvent::class, 'event_details_id', 'id');
    }

    private static function storeEventBanner($file)
    {
        $fileNameInitial = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
        $fileNameFinal = "images/events/$fileNameInitial";
        $file->storeAs('images/events/', $fileNameInitial);
        return $fileNameFinal;
    }

    public static function destroyEventBanner($file)
    {
        $fileNameInitial = str_replace('images/events/', '', $file);
        $fileNameFinal = "images/events/$fileNameInitial";
        
        if (file_exists($fileNameFinal)) {
            unlink($fileNameFinal);
        }
    }

    public function isCompleteEvent() {
        $isComplete = true;
        $requiredFields = [
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
            'event_type_id',
            'event_tier_id',
            'event_category_id',
            'payment_transaction_id'
        ];
    
        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                $isComplete = $isComplete && false; 
            }

            if(!$isComplete) {
                return $isComplete;
            }
        }
    
        return $isComplete;
    }

    public function statusResolved()
    {
        $carbonPublishedDateTime = $this->createCarbonDateTimeFromDB(
            $this->sub_action_public_date, $this->sub_action_public_time
        );
        $carbonEndDateTime = $this->createCarbonDateTimeFromDB(
            $this->endDate, $this->endTime
        );
        $carbonStartDateTime = $this->createCarbonDateTimeFromDB(
            $this->startDate, $this->startTime
        );

        $carbonNow = Carbon::now()->utc();
        
        if (in_array($this->status, ['DRAFT', 'PREVEW'])) {
            return "DRAFT";
        } elseif (is_null($this->payment_transaction_id) || $this->status == 'PENDING') {
            return "PENDING";
        } elseif (!$carbonEndDateTime || !$carbonStartDateTime) {
            Log::error("EventDetail.php: statusResolved: EventDetail with id= " . $this->id
                . " and name= " . $this->eventName . " has null end or start date time");
            
            return "ERROR";
        } elseif ($carbonEndDateTime < $carbonNow) {
            return "ENDED";
        } else {
            
            if ($carbonStartDateTime < $carbonNow) {
                return "ONGOING";
            } elseif ($carbonPublishedDateTime && $carbonPublishedDateTime > $carbonNow) {
                return "SCHEDULED";
            } else return "UPCOMING";
        }

    }
    public function fixTimeToRemoveSeconds($time)
    {
        if ($time == null) {
            return null;
        } else if (substr_count($time, ':') === 2) {
            $time = explode(':', $time);
            $time = $time[0] . ':' . $time[1];
            return $time;
        } else {
            return $time;
        }
    }

    public function createCarbonDateTimeFromDB($date, $time)
    {
        if ($date == null || $time == null) {
            return null;
        }

        $carbonDateTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->fixTimeToRemoveSeconds($time))
            ->utc();
        return $carbonDateTime;
    }

    public static function mappingEventStateResolve()
    {
        return config('constants.mappingEventState');
    }

    public function eventTier()
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }

    public static function generateOrganizerPartialQueryForFilter(Request $request) {
        $eventListQuery = self::query();
        $eventListQuery->when($request->has('status'), function ($query) use ($request) {
            $status = $request->input('status')[0];
            if (empty(trim($status))) {
                return $query;
            }

            $currentDateTime = Carbon::now()->utc();
            if ($status == 'ALL') {
                return $query;
            } elseif ($status == 'DRAFT') {
                return $query->whereIn('status', ['DRAFT', 'PREVIEW']);
            } elseif ($status == 'PENDING') {
                return $query->where('status', 'PENDING')->orWhereNull('payment_transaction_id');
            } elseif ($status == 'ENDED') {
                return $query
                    ->whereRaw('CONCAT(endDate, " ", endTime) < ?', [$currentDateTime])
                    ->where('status', '<>', 'PREVIEW')
                    ->whereNotNull('payment_transaction_id')
                    ->where('status', '<>', 'DRAFT');
            } elseif ($status == 'LIVE') {
                return $query
                    ->where(function ($query) use ($currentDateTime) {
                        return $query
                            ->whereNull('sub_action_public_date')
                            ->orWhereNull('sub_action_public_time')
                            ->orWhereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) < ?', [$currentDateTime]);
                    })
                    ->where('status', '<>', 'DRAFT')
                    ->whereNotNull('payment_transaction_id')
                    ->where('status', '<>', 'PREVIEW')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            } elseif ($status == 'SCHEDULED') {
                return $query
                    ->whereNotNull('sub_action_public_date')
                    ->whereNotNull('sub_action_public_time')
                    ->whereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime])
                    ->where('status', '<>', 'DRAFT')
                    ->where('status', '<>', 'PREVIEW')
                    ->whereNotNull('payment_transaction_id')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            } else {
                return $query;
            }
        });

        $eventListQuery->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            } else {
                return $query->where(function ($q) use ($search) {
                    return $q
                        ->orWhere('eventDescription', 'LIKE', "%{$search}%")
                        ->orWhere('eventName', 'LIKE', "%{$search}%")
                        ->orWhere('eventTags', 'LIKE', "%{$search}%")
                        ->orWhereHas('game', function ($query) use ($search) {
                                $query->where('gameTitle', 'LIKE', "%$search%");
                        });
                });
            }
        });

        return $eventListQuery;
    }

    public static function generateOrganizerFullQueryForFilter(Request $request) {
        $eventListQuery = self::generateOrganizerPartialQueryForFilter($request);

        $eventListQuery->when($request->has('sort'), function ($query) use ($request) {
            $sort = $request->input('sort');
            foreach ($sort as $key => $value) {
                if (!empty(trim($key)) && $value != "none") {
                    if ($key ==  'startDate'){
                        $query->orderBy('startDate', $value)->orderBy('startTime', $value);
                    } else if ($key ==  'prize') {
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

            if (array_key_exists('eventTier', $filter)) {
                return $query->where(function ($q) use ($filter) {
                    foreach ($filter['eventTier'] as $element) {
                        $q->orWhere('event_tier_id', $element);
                    }
                });
            } 
            
            if (array_key_exists('eventType', $filter)) {
                return $query->where(function ($q) use ($filter) {
                    foreach ($filter['eventType'] as $element) {
                        $q->orWhere('event_type_id', $element);
                    }
                });
            } 
            
            if (array_key_exists('gameTitle', $filter)) {
                return $query->where(function ($q) use ($filter) {
                    foreach ($filter['gameTitle'] as $element) {
                        $q->orWhere('event_category_id', $element);
                    }
                });
            }

            if (array_key_exists('date', $filter)) {
                return $query->where(function ($q) use ($filter, $request) {
                    foreach ($filter['date'] as $element) {
                        switch ($element)  {
                            case 'today':
                                $q->orWhereBetween('created_at', [now()->startOfDay(), now()]);
                                break;
                            case 'yesterday':
                                $q->orWhereBetween('created_at', [now()->subDay()->startOfDay(), now()->startOfDay()]);
                                break;
                            case 'this-week':
                                $q->orWhereBetween('created_at', [now()->subWeek(), now()]);
                                break;
                            case 'this-month':
                                $q->orWhereBetween('created_at', [now()->subMonth(), now()]);
                                break;
                            case 'this-year':
                                $q->orWhereBetween('created_at', [now()->subYear(), now()]);
                                break;
                            case 'custom-range':
                                $q->orWhereBetween('created_at', [$filter['startDate'][0], $filter['endDate'][0]]);
                                break;
                            default:
                                break;
                        }
                    }
                });
            }

            return $query;
        });

        return $eventListQuery;
    }

    public static function generateParticipantFullQueryForFilter(Request $request) {
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
                } else {
                    return $query->where(function ($q) use ($search) {
                        return $q
                            ->orWhere('eventDescription', 'LIKE', "%{$search}%")
                            ->orWhere('eventName', 'LIKE', "%{$search}%")
                            ->orWhere('eventTags', 'LIKE', "%{$search}%")
                            ->orWhereHas('game', function ($query) use ($search) {
                                    $query->where('gameTitle', 'LIKE', "%$search%");
                            });
                    });
                }
            });
        
        return $eventListQuery;
    }

    public static function storeLogic(EventDetail $eventDetail, Request $request): EventDetail
    {
        try {
            if ($request->hasFile('eventBanner')) {
                if ($eventDetail->eventBanner) {
                    self::destroyEventBanner($eventDetail->eventBanner);
                }

                $eventDetail->eventBanner = self::storeEventBanner($request->file('eventBanner'));
            } 
        } catch (Exception $e){}

        $isEditMode = $eventDetail->id != null;
        $isDraftMode = $request->launch_visible == 'DRAFT';
        
        $isPreviewMode = $isEditMode ? false : $request->livePreview == 'true';
        $carbonStartDateTime = null;
        $carbonEndDateTime = null;
        $carbonPublishedDateTime = null;
        $eventDetail->event_type_id = $request->eventTypeId;
        $eventDetail->event_tier_id = $request->eventTierId;
        $eventDetail->event_category_id = $request->gameTitleId;
        
        $startDate = $request->startDate;
        $startTime = $eventDetail->fixTimeToRemoveSeconds($request->startTime);
        $endDate = $request->endDate;
        $endTime = $eventDetail->fixTimeToRemoveSeconds($request->endTime);

        if ($startDate && $startTime) {
            $carbonStartDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->startDate . ' ' . $startTime)->utc();
            $eventDetail->startDate = $carbonStartDateTime->format('Y-m-d');
            $eventDetail->startTime = $carbonStartDateTime->format('H:i');
        } elseif ($isPreviewMode && !$isEditMode) {
            $eventDetail->startDate = null;
            $eventDetail->startTime = null;
        } elseif (!$isDraftMode) {
            throw new TimeGreaterException('Start date and time must be greater than current date and time.');
        } elseif ($isDraftMode) {
            $eventDetail->startDate = $request->startDate;
            $eventDetail->startTime = $request->startTime;
        }

        if ($endDate && $endTime) {
            $carbonEndDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->endDate . ' ' . $endTime)->utc();
            if ($startDate && $startTime && $carbonEndDateTime > $carbonStartDateTime) {
                $eventDetail->endDate = $carbonEndDateTime->format('Y-m-d');
                $eventDetail->endTime = $carbonEndDateTime->format('H:i');
            } elseif ($isPreviewMode && !$isEditMode) {
                $eventDetail->endDate = null;
                $eventDetail->endTime = null;
            } elseif (!$isDraftMode) {
                throw new TimeGreaterException('End date and time must be greater than start date and time.');
            } elseif ($isDraftMode) {
                $eventDetail->endDate = $request->endDate;
                $eventDetail->endTime = $request->endTime;
            }
        }

        $eventDetail->eventName = $request->eventName;
        $eventDetail->eventDescription = $request->eventDescription;
        $eventDetail->eventTags = $request->eventTags;
        $transaction = $eventDetail->payment_transaction;

        if ($transaction && $transaction->payment_id && $transaction->status == 'SUCCESS') {
        } elseif ($request->isPaymentDone == 'true' && $request->paymentMethod) {
            $transaction = new PaymentTransaction();
            $transaction->payment_id = $request->paymentMethod;
            $transaction->payment_status = 'SUCCESS';
            $transaction->save();
            $eventDetail->payment_transaction_id = $transaction->id;
        } 

        if ($request->launch_visible == 'DRAFT') {
            $eventDetail->status = 'DRAFT';
            $eventDetail->sub_action_public_date = null;
            $eventDetail->sub_action_public_time = null;
        } else {
            
            if ($request->launch_visible == 'public') {
                $launch_date = $request->launch_date_public;
                $launch_time = $eventDetail->fixTimeToRemoveSeconds($request->launch_time_public);
            } elseif ($request->launch_visible == 'private') {
                $launch_date = $request->launch_date_private;
                $launch_time = $eventDetail->fixTimeToRemoveSeconds($request->launch_time_private);
            }

            if ($request->launch_schedule == 'schedule' && $launch_date && $launch_time) {
                $carbonPublishedDateTime = Carbon::createFromFormat('Y-m-d H:i', $launch_date . ' ' . $launch_time)->utc();
                
                if ($launch_date && $launch_time && $carbonPublishedDateTime < $carbonStartDateTime && $carbonPublishedDateTime < $carbonEndDateTime) {
                    $eventDetail->status = 'SCHEDULED';
                    $eventDetail->sub_action_public_date = $carbonPublishedDateTime->format('Y-m-d');
                    $eventDetail->sub_action_public_time = $carbonPublishedDateTime->format('H:i');
                } else {
                    throw new TimeGreaterException('Published time must be before start time and end time.');
                }

            } elseif ($request->launch_schedule == 'now') {
                $eventDetail->status = 'UPCOMING';
                $eventDetail->sub_action_public_date = null;
                $eventDetail->sub_action_public_time = null;
            } else {
                $eventDetail->status = 'DRAFT';
                
                if ($launch_date && $launch_time) {
                    $carbonPublishedDateTime = Carbon::createFromFormat('Y-m-d H:i', $launch_date . ' ' . $launch_time)->utc();
                    $eventDetail->sub_action_public_date = $carbonPublishedDateTime->format('Y-m-d');
                    $eventDetail->sub_action_public_time = $carbonPublishedDateTime->format('H:i');
                } else {
                    $eventDetail->sub_action_public_date = null;
                    $eventDetail->sub_action_public_time = null;
                }
            }

            if ($transaction && $transaction->payment_id && $transaction->status == 'SUCCESS') {
            } elseif ($request->isPaymentDone == 'true' && $request->paymentMethod) {
            } else {
                if ($eventDetail->status != 'DRAFT') { 
                    $eventDetail->status = 'PENDING';
                }
            }
        }

        $eventDetail->sub_action_private = $request->launch_visible;
        
        if ($request->launch_visible == 'DRAFT') {
            $eventDetail->sub_action_private = 'private';
        }

        $eventDetail->status = $eventDetail->statusResolved();
        return $eventDetail;
    }

    public static function findEventWithRelationsAndThrowError($userId, $id, $relations = null, $relationCount = null): EventDetail
    {
        $relations ??= ['type','tier','game'];
        $eventQuery = self::with($relations);

        if ($relationCount) {
            $eventQuery->withCount($relationCount);
        }

        $event = $eventQuery->find($id);
        
        if (is_null($event)) {
            throw new ModelNotFoundException("Event not found with id: $id");
        } else if ($event->user_id != $userId) {
            throw new UnauthorizedException('You cannot view an event of another organizer!');
        } else {
            return $event;
        }
    }
    public static function findEventAndThrowError($eventId, $userId): EventDetail {
        $event = self::find($eventId);

        if (!$event) {
            throw new ModelNotFoundException("Event not found with id: $eventId");
        } else if ($event->user_id != $userId) {
            throw new UnauthorizedException('You cannot view an event of another organizer!');
        } else {
            return $event;
        }
    }
    
}
