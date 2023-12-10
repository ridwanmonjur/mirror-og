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

class EventController extends Controller
{
    public function mappingEventStateResolve()
    {
        return config('constants.mappingEventState');
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
        $eventListQuery =  EventDetail::query();
        $organizer = Organizer::where('user_id', $user->id)->first();
        $eventListQuery->when($request->has('status'), function ($query) use ($request) {

            $status = $request->input('status');

            $currentDateTime = Carbon::now();
            if ($status == 'ALL') {
                return $query;
            }
            if ($status == 'LIVE') {
                return $query->where(function ($query) use ($currentDateTime) {
                    $query->whereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) < ?', [$currentDateTime])
                        ->whereRaw('CONCAT(endDate, " ", endTime) < ?', [$currentDateTime]);
                });
            }
            if ($status == 'SCHEDULED') {
                return $query->where(function ($query)  use ($currentDateTime) {
                    $query->whereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime])
                        ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
                });
            }
            if ($status == 'DRAFT') {
                return $query->where('status', 'DRAFT');
            }
            if ($status == 'ENDED') {
                return $query->whereRaw('CONCAT(endDate, " ", endTime) < ?', [$currentDateTime]);
            }
            return $query;
        });
        $eventListQuery->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            }
            return $query->where('gameTitle', 'LIKE', "%{$search}% COLLATE utf8mb4_general_ci")
                ->orWhere('eventDescription', 'LIKE', "%{$search}% COLLATE utf8mb4_general_ci")
                ->orWhere('eventDefinitions', 'LIKE', "%{$search}% COLLATE utf8mb4_general_ci");
        });

        $eventListQuery->when($request->has("sort"), function ($query) use ($request) {
            $sortTypeJSONString = $request->input("sortType");
            $sortKeys = json_decode($sortTypeJSONString, true);
            foreach ($sortKeys as $key => $value) {
                $query->orderBy($key, $value);
            }
            return $query;
        });

        $eventListQuery->when($request->has("eventTier"), function ($query) use ($request) {
            $eventTier = trim($request->input("eventTier"));
            return $query->where('eventTier', $eventTier);
        });
        $eventListQuery->when($request->has("eventType"), function ($query) use ($request) {
            $eventType = trim($request->input("eventType"));
            return $query->where('eventType', $eventType);
        });
        $eventListQuery->when($request->has("gameTitle"), function ($query) use ($request) {
            $gameTitle = $request->input("gameTitle");
            return $query->where('gameTitle', $gameTitle);
        });

        $eventList = $eventListQuery->where('user_id', $user->id)
            ->paginate(4);
        $mappingEventState = $this->mappingEventStateResolve();
        $count = 4;
        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        if ($request->ajax()) {
            $view = view(
                'Organizer.ManageEventScroll',
                $outputArray
            )->render();

            return response()->json(['html' => $view]);
        }
        return view(
            'Organizer.ManageEvent',
            $outputArray
        );
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
            'eventTypeList' => $eventTypeList
        ]);
    }



    public function storeLogic(EventDetail $eventDetail, Request $request): EventDetail
    {
        $carbonStartDateTime = null;
        $carbonEndDateTime = null;
        $carbonPublishedDateTime = null;
        // dd($request);
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
            $carbonStartDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->startDate . ' ' . $startTime)
                ->utc();
            $eventDetail->startDate = $carbonStartDateTime->format('Y-m-d');
            $eventDetail->startTime =  $carbonStartDateTime->format('H:i');
        } else {
            $eventDetail->startDate = null;
            $eventDetail->startTime = null;
        }
        if ($endDate && $endTime) {
            $carbonEndDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->endDate . ' ' . $endTime)
                ->utc();
            if ($startDate && $startTime && $carbonEndDateTime < $carbonStartDateTime) {
                $eventDetail->endDate = $carbonEndDateTime->format('Y-m-d');
                $eventDetail->endTime  = $carbonEndDateTime->format('H:i');
            } else {
                throw new TimeGreaterException("End date and time must be greater than start date and time.");
            }
        }
        $eventDetail->eventName  = $request->eventName;
        $eventDetail->eventDescription  = $request->eventDescription;
        $eventDetail->eventTags  = $request->eventTags;
        // payment
        $transaction = $eventDetail->payment_transaction;
        if ($transaction && $transaction->payment_id && $transaction->status == "SUCCESS") {
        } else {
            if ($request->isPaymentDone == "true" && $request->livePreview == "true") {
                $transaction = new PaymentTransaction();
                $transaction->payment_id = $request->paymentMethod;
                $transaction->payment_status = "SUCCESS";
                $transaction->save();
                $eventDetail->payment_transaction_id = $transaction->id;
            } else {
            }
        }
        if ($request->livePreview == "true") {
            $eventDetail->status = "PREVIEW";
            $eventDetail->sub_action_public_date  = $request->launch_date;
            $eventDetail->sub_action_public_time  = $request->launch_time;
        } else if ($request->launch_visible == "DRAFT") {
            $eventDetail->status = "DRAFT";
            $eventDetail->sub_action_public_date  = null;
            $eventDetail->sub_action_public_time  = null;
        } else {
            $launch_date = $request->launch_date;
            $launch_time = $eventDetail->fixTimeToRemoveSeconds($request->launch_time);
            if ($request->launch_schedule == "schedule" && $launch_date && $launch_time) {
                $carbonPublishedDateTime = Carbon::createFromFormat('Y-m-d H:i', $launch_date . ' ' . $launch_time)
                    ->utc();
                $eventDetail->status = "SCHEDULED";
                $eventDetail->sub_action_public_date  = $carbonPublishedDateTime->format('Y-m-d');
                $eventDetail->sub_action_public_time  = $carbonPublishedDateTime->format('H:i');
            } else if ($request->launch_schedule == "now") {
                $carbonPublishedDateTime = Carbon::now()->utc();
                $eventDetail->status = "UPCOMING";
                $eventDetail->sub_action_public_date = $carbonPublishedDateTime->format('Y-m-d');
                $eventDetail->sub_action_public_time = $carbonPublishedDateTime->format('H:i');
            } else {
                $eventDetail->status = "UPCOMING";
                $launch_date = null;
                $launch_time = null;
            }
        }
        $eventDetail->sub_action_private  = $request->launch_visible == "private" ? "private" : "public";
        $eventDetail->action  = $request->launch_visible;
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
    }



    public function store(Request $request)
    {
        // try {
        $fileNameFinal = null;
        if ($request->hasFile('eventBanner')) {
            $fileNameFinal = $this->storeEventBanner($request->file('eventBanner'));
        }
        $eventDetail = new EventDetail;
        try {
            $eventDetail = $this->storeLogic($eventDetail, $request);
        } catch (TimeGreaterException $e) {
            return back()->with('error', $e->getMessage());
        }
        $eventDetail->user_id  = auth()->user()->id;
        $eventDetail->eventBanner  = $fileNameFinal;
        $eventDetail->save();
        if ($request->livePreview == "true") {
            return redirect('organizer/live/' . $eventDetail->id);
        }
        return redirect('organizer/success/' . $eventDetail->id);
    }


    private function getEventAndUser($id)
    {
        $authUser = Auth::user();
        $event = EventDetail
            ::with('type', 'tier', 'game')
            ->where('user_id', $authUser->id)
            ->findOrFail($id);
        $isUser = true;
        return [$event, $isUser];
    }

    public function showLive($id): View
    {
        [$event, $isUser] = $this->getEventAndUser($id);
        return view(
            'Organizer.ViewEvent',
            [
                'event' => $event,
                'mappingEventState' => $this->mappingEventStateResolve(),
                'isUser' => $isUser,
                'livePreview' => 1
            ]
        );
    }

    public function showSuccess($id): View
    {
        [$event, $isUser] = $this->getEventAndUser($id);
        return view(
            'Organizer.CreateEventSuccess',
            [
                'event' => $event,
                'mappingEventState' => $this->mappingEventStateResolve(),
                'isUser' => $isUser,
                'livePreview' => 1
            ]
        );
    }

    public function show($id): View
    {
        [$event, $isUser] = $this->getEventAndUser($id);
        return view(
            'Organizer.ViewEvent',
            [
                'event' => $event,
                'mappingEventState' => $this->mappingEventStateResolve(),
                'isUser' => $isUser,
                'livePreview' => 0
            ]
        );
    }


    public function edit($id)
    {
        $event = EventDetail
            ::with('type', 'tier', 'game')
            ->findOrFail($id);
        $eventCategory = EventCategory::all();
        $eventTierList = EventTier::all();
        $eventTypeList = EventType::all();
        return view('Organizer.EditEvent', [
            'eventCategory' => $eventCategory,
            'event' => $event,
            'eventTierList' => $eventTierList,
            'eventTypeList' => $eventTypeList,
            'editMode' => 1
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
                $eventDetail->user_id  = auth()->user()->id;
                $eventDetail->eventBanner  = $fileNameFinal;
                $eventDetail->save();
                // dd($eventId, $request, $eventDetail);
                if ($request->livePreview == "true") {
                    return redirect('organizer/live/' . $eventDetail->id);
                }
                return redirect('organizer/success/' . $eventDetail->id);
            } else {
                // return back()->with('error', 'Event id missing!');
                return redirect('organizer/home');
            }
        } catch (\Exception $e) {
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
