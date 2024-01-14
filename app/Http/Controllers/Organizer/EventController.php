<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\EventCategory;
use App\Models\EventTier;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Organizer;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\TimeGreaterException;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

class EventController extends Controller
{
    private function getEventAndUser($id)
    {
        $authUser = Auth::user();
        $event = EventDetail::with('type', 'tier', 'game')
            ->where('user_id', $authUser->id)
            ->find($id);
        $isUserSameAsAuth = true;
        if (!$event) {
            throw new ModelNotFoundException("Event not found with id: $id");
        }

        $checkIfUserIsOrganizerOfEvent = $event->user_id == $authUser->id;
        if (!$checkIfUserIsOrganizerOfEvent) {
            throw new UnauthorizedException('You cannot view a scheduled event');
        }

        return [$event, $isUserSameAsAuth, $authUser];
    }


    public function show404($error): View
    {
        return view('Organizer.EventNotFound', compact('error'));
    }

    public function home(): View
    {
        return view('Organizer.Home');
    }

    function combineParams($queryParams)
    {
        $combinedParams = [];
        foreach ($queryParams as $key => $value) {
            if (is_array($value) && count($value) > 1) {
                $combinedParams[$key] = $value;
            } else {
                $combinedParams[$key] = is_array($value) ? $value[0] : [$value];
            }
        }
        return $combinedParams;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $eventListQuery = EventDetail::query();
        $organizer = Organizer::where('user_id', $user->id)->first();
        $eventListQuery->when($request->has('status'), function ($query) use ($request) {
            $status = $request->input('status');
            if (!$status) return $query;
            $currentDateTime = Carbon::now()->utc();
            if ($status == 'ALL') {
                return $query;
            } elseif ($status == 'DRAFT') {
                return $query->where('status', 'DRAFT');
            } 
            elseif ($status == 'ENDED') {
                return $query
                    ->whereRaw('CONCAT(endDate, " ", endTime) < ?', [$currentDateTime])
                    ->where('status', '<>', 'PREVIEW')
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
                    ->where('status', '<>', 'PREVIEW')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            } elseif ($status == 'SCHEDULED') {
                $query
                    ->whereNotNull('sub_action_public_date')
                    ->whereNotNull('sub_action_public_time')
                    ->whereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime])
                    ->where('status', '<>', 'DRAFT')
                    ->where('status', '<>', 'PREVIEW')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
                // dd($query);
                return $query;
            }
            else return $query;
        });
        $eventListQuery->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            }
            return $query->where(function($q) use ($search) {
                return $q
                ->where('gameTitle', 'LIKE', "%{$search}%")
                ->orWhere('eventDescription', 'LIKE', "%{$search}%")
                ->orWhere('eventDefinitions', 'LIKE', "%{$search}%");
            });
        });
        $count = 8;
        $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);
        $mappingEventState = EventDetail::mappingEventStateResolve();
        $eventListQuery->with('tier'); // Eager load the eventTier relationship
        $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);
        foreach ($eventList as $event) {
            $tierEntryFee = $event->eventTier->tierEntryFee ?? null;
        }
        
        $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);

        foreach ($eventList as $eventDetail) {
        $eventDetail->joinEventCount = $eventDetail->joinEvents()->count();
        }

        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        if ($request->ajax()) {
            $view = view('Organizer.ManageEventScroll', $outputArray)->render();

            return response()->json(['html' => $view]);
        }
        return view('Organizer.ManageEvent', $outputArray);
    }

    public function search(Request $request)
    {
        $userId = $request->userId;
        $user = User::find($userId);
        $organizer = Organizer::where('user_id', $user->id)->first();
        $eventListQuery = EventDetail::query();
        $eventListQuery->when($request->has('status'), function ($query) use ($request) {
            $status = $request->input('status');
            if (!$status) return $query;
            $status = $status[0];
            $currentDateTime = Carbon::now()->utc();
            if ($status == 'ALL') {
                return $query;
            } elseif ($status == 'DRAFT') {
                return $query->where('status', 'DRAFT');
            } 
            elseif ($status == 'ENDED') {
                return $query
                    ->whereRaw('CONCAT(endDate, " ", endTime) < ?', [$currentDateTime])
                    ->where('status', '<>', 'PREVIEW')
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
                    ->where('status', '<>', 'PREVIEW')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            } elseif ($status == 'SCHEDULED') {
                return $query
                    ->whereNotNull('sub_action_public_date')
                    ->whereNotNull('sub_action_public_time')
                    ->whereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime])
                    ->where('status', '<>', 'DRAFT')
                    ->where('status', '<>', 'PREVIEW')
                    ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
            }
            else return $query;
        });
        $eventListQuery->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            }
            return $query->where(function($q) use ($search) {
                return $q
                ->where('eventDescription', 'LIKE', "%{$search}%")
                ->orWhere('eventName', 'LIKE', "%{$search}%");
            });
        });
        $eventListQuery->when($request->has('sort'), function ($query) use ($request) {
            $sort = $request->input('sort');
            if (!$sort) return $query;
            foreach ($sort as $key => $value) {
                $query->orderBy($key, $value);
            }
            return $query;
        });
        $eventListQuery->when($request->has('filter'), function ($query) use ($request) {
            $filter = $request->input('filter');
            if (!$filter) return $query;
            if (array_key_exists('eventTier', $filter)) {
                // User::whereHas('posts', function ($query) use ($postTitle) {
                //     $query->where('title', 'like', '%' . $postTitle . '%');
                // })->get();
                $query->where('eventTier', $filter['eventTier']);
            } 
            if (array_key_exists('eventType', $filter)) {
                $query->where('eventType', $filter['eventType']);
            }
            if (array_key_exists('gameTitle', $filter)) {
                $query->where('gameTitle', $filter['gameTitle']);
            }
            return $query;
        });
        $count = 8;
        $eventList = $eventListQuery->where('user_id', $userId)->paginate($count);
        $mappingEventState = EventDetail::mappingEventStateResolve();

        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        $view = view('Organizer.ManageEventScroll', $outputArray)->render();
        // dd($eventListQuery);
        return response()->json(['html' => $view]);
    }

    public function create(): View
    {
        $eventCategory = EventCategory::all();
        $eventTierList = EventTier::all();
        $eventTypeList = EventType::all();
        // return view('Organizer.CreateEvent.event');
        return view('Organizer.CreateEvent', [
            'eventCategory' => $eventCategory,
            'event' => null,
            'editMode' => 0,
            'eventTierList' => $eventTierList,
            'eventTypeList' => $eventTypeList,
        ]);
    }

    public function storeLogic(EventDetail $eventDetail, Request $request): EventDetail
    {
        $isEditMode = $eventDetail->id != null;
        $isDraftMode = $request->launch_visible == 'DRAFT';
        // disable preview mode checjs if edit mode
        $isPreviewMode = $isEditMode ? false : $request->livePreview == 'true';
        $carbonStartDateTime = null;
        $carbonEndDateTime = null;
        $carbonPublishedDateTime = null;
        // step1
        $eventDetail->gameTitle = $request->gameTitle;
        $eventDetail->eventType = $request->eventType;
        $eventDetail->eventTier = $request->eventTier;
        $eventDetail->event_type_id = $request->eventTypeId;
        $eventDetail->event_tier_id = $request->eventTierId;
        $eventDetail->event_category_id = $request->gameTitleId;
        // step2
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
        } else if (!$isDraftMode){
            throw new TimeGreaterException('Start date and time must be greater than current date and time.');
        }
        if ($endDate && $endTime) {
            $carbonEndDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->endDate . ' ' . $endTime)->utc();
            if ($startDate && $startTime && $carbonEndDateTime > $carbonStartDateTime) {
                $eventDetail->endDate = $carbonEndDateTime->format('Y-m-d');
                $eventDetail->endTime = $carbonEndDateTime->format('H:i');
            } elseif ($isPreviewMode && !$isEditMode) {
                $eventDetail->endDate = null;
                $eventDetail->endTime = null;
            } else if (!$isDraftMode){
                throw new TimeGreaterException('End date and time must be greater than start date and time.');
            }
        }
        $eventDetail->eventName = $request->eventName;
        $eventDetail->eventDescription = $request->eventDescription;
        $eventDetail->eventTags = $request->eventTags;
        // payment
        $transaction = $eventDetail->payment_transaction;
        if ($transaction && $transaction->payment_id && $transaction->status == 'SUCCESS') {
        } elseif ($request->isPaymentDone == 'true' && $request->paymentMethod) {
            $transaction = new PaymentTransaction();
            $transaction->payment_id = $request->paymentMethod;
            $transaction->payment_status = 'SUCCESS';
            $transaction->save();
            $eventDetail->payment_transaction_id = $transaction->id;
        } elseif ($isPreviewMode && !$isEditMode && !$isDraftMode) {
            throw new TimeGreaterException('Payment is not done.');
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
            } else if ($request->launch_schedule == 'now') {
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
        }
        $eventDetail->sub_action_private = $request->launch_visible;
        if ($request->launch_visible == 'DRAFT') { 
            $eventDetail->sub_action_private = 'private';
        }
        $eventDetail->action = $request->launch_visible;
        // dd($eventDetail, $request);
        return $eventDetail;
    }

    public function storeEventBanner($file)
    {
        $fileNameInitial = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
        $fileNameFinal = "images/events/$fileNameInitial";
        $file->storeAs('images/events/', $fileNameInitial);
        return $fileNameFinal;
    }

    public function destroyEventBanner($file)
    {
        $fileNameInitial = str_replace('images/events/', '', $file);
        $fileNameFinal = "images/events/$fileNameInitial";
        if (file_exists($fileNameFinal)) {
            unlink($fileNameFinal);
        }
    }

    public function store(Request $request)
    {
        // try {
        $fileNameFinal = null;
        if ($request->hasFile('eventBanner')) {
            $fileNameFinal = $this->storeEventBanner($request->file('eventBanner'));
        }
        $eventDetail = new EventDetail();
        try {
            $eventDetail = $this->storeLogic($eventDetail, $request);
        } catch (TimeGreaterException $e) {
            return back()->with('error', $e->getMessage());
        }
        $eventDetail->user_id = auth()->user()->id;
        $eventDetail->eventBanner = $fileNameFinal;
        $eventDetail->save();
        if ($request->livePreview == 'true') {
            return redirect('organizer/event/' . $eventDetail->id . '/live');
        }
        return redirect('organizer/event/' . $eventDetail->id . '/success');
    }
 

    public function showLive($id): View
    {
        try {
            [$event, $isUserSameAsAuth, $user] = $this->getEventAndUser($id);
            $count = 8;
            $eventListQuery = EventDetail::query();
            $eventListQuery->with('tier'); 
            $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);
            $mappingEventState = EventDetail::mappingEventStateResolve();
            foreach ($eventList as $eventItem) {
                $tierEntryFee = $eventItem->tier?->tierEntryFee ?? null;
            }
    
            foreach ($eventList as $eventDetail) {
                $eventDetail->joinEventCount = $eventDetail->joinEvents()?->count();
            }

            $livePreview = true;
            $outputArray = compact('eventList', 'event', 'count', 'user', 'livePreview', 'mappingEventState');
            return view('Organizer.ViewEvent', $outputArray);
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->show404("Event not found with id: $id");
        }
    }

    public function showSuccess($id): View
    {
        try {
            [$event, $isUserSameAsAuth] = $this->getEventAndUser($id);
        } catch (Exception $e) {
            return $this->show404("Event not found with id: $id");
        }
        return view('Organizer.CreateEventSuccess', [
            'event' => $event,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'isUser' => $isUserSameAsAuth,
            'livePreview' => 1,
        ]);
    }

    public function show($id): View
    {
        try {
            [$event, $isUserSameAsAuth, $user] = $this->getEventAndUser($id);
            
            $count = 8;
            $eventListQuery = EventDetail::query();
            $eventListQuery->with('tier');
            $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);

            foreach ($eventList as $eventItem) {
                $tierEntryFee = $eventItem->tier->tierEntryFee ?? null;
            }
    
            foreach ($eventList as $eventDetail) {
                $eventDetail->joinEventCount = $eventDetail->joinEvents()->count();
            }
            
        } catch (Exception $e) {
            return $this->show404("Event not found with id: $id");
        }
        
        return view('Organizer.ViewEvent', [
            'event' => $event,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'isUser' => $isUserSameAsAuth,
            'livePreview' => 0,
            'eventList' => $eventList, // Add the eventList variable to the view data
        ]);
    }

    public function edit($id)
    {
        try {
            [$event] = $this->getEventAndUser($id);
        } catch (Exception $e) {
            return $this->show404("Event not found for id: $id");
        }
        // dd($event, $event->tier, $event->type, $event->game);
        $status = $event->statusResolved();
        if ($status != 'UPCOMING' && $status != 'DRAFT') {
            return $this->show404("Event has already gone live for id: $id");
        }
        $eventCategory = EventCategory::all();
        $eventTierList = EventTier::all();
        $eventTypeList = EventType::all();
        return view('Organizer.EditEvent', [
            'eventCategory' => $eventCategory,
            'event' => $event,
            'eventTierList' => $eventTierList,
            'eventTypeList' => $eventTypeList,
            'editMode' => 1,
        ]);
    }

    public function updateForm($id, Request $request)
    {
        try {
            $eventId = $id;
            $eventDetail = EventDetail::find($eventId);
            if ($eventId) {
                $fileNameFinal = null;
                if ($request->hasFile('eventBanner')) {
                    $fileNameFinal = $this->storeEventBanner($request->file('eventBanner'));
                    if ($eventDetail->eventBanner) {
                        $this->destroyEventBanner($eventDetail->eventBanner);
                    }
                } else {
                    $fileNameFinal = $eventDetail->eventBanner;
                }
                $eventDetail = EventDetail::find($eventId);
                try {
                    $eventDetail = $this->storeLogic($eventDetail, $request);
                } catch (TimeGreaterException $e) {
                    return back()->with('error', $e->getMessage());
                }
                $status = $eventDetail->statusResolved();
                if ($status != 'ONGOING' && $status != 'DRAFT' && $status != 'SCHEDULED') {
                    return $this->show404("Event has already gone live for id: $id");
                }
                $eventDetail->user_id = auth()->user()->id;
                $eventDetail->eventBanner = $fileNameFinal;
                $eventDetail->save();

                if ($request->livePreview == 'true') {
                    return redirect('organizer/event/' . $eventDetail->id . '/live');
                }
                return redirect('organizer/event/' . $eventDetail->id . '/success');
            } else {
                return $this->show404("Event not found for id: $id");
            }
        } catch (Exception $e) {
            dd($e);
            return back()->with('error', 'Something went wrong with saving data!');
        }
    }

    public function destroy($id)
    {
        [$event] = $this->getEventAndUser($id);
        $event->delete();
        return redirect('organizer/event');
    }
}
