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

class EventController extends Controller
{


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
        $mappingEventState = $this->mappingEventState;
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

    public function fixTimeToRemoveSeconds($time)
    {
        // Check if the startTime contains seconds
        if (substr_count($time, ':') === 2) {
            // If it has seconds, omit the seconds part
            $time = explode(':', $time);
            $time = $time[0] . ':' . $time[1];
        }
        return $time;
    }

    public function storeLogic(EventDetail $eventDetail, Request $request): EventDetail
    {
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
        $startTime = $this->fixTimeToRemoveSeconds($request->startTime);
        $endDate = $request->endDate;
        $endTime = $this->fixTimeToRemoveSeconds($request->endTime);
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
            $eventDetail->endDate = $carbonEndDateTime->format('Y-m-d');
            $eventDetail->endTime  = $carbonEndDateTime->format('H:i');
        } else {
            $eventDetail->endDate = null;
            $eventDetail->endTime  = null;
        }
        $eventDetail->eventName  = $request->eventName;
        $eventDetail->eventDescription  = $request->eventDescription;
        $eventDetail->eventTags  = $request->eventTags;
        // payment
        $transaction = $eventDetail->payment_transaction;
        if ($transaction && $transaction->payment_id && $transaction->status == "SUCCESS") {
        } else {
            if ($request->isPaymentDone == "true") {
                $transaction = new PaymentTransaction();
                $transaction->payment_id = $request->paymentMethod;
                $transaction->payment_status = "SUCCESS";
                $transaction->save();
                $eventDetail->payment_transaction_id = $transaction->id;
            } else {
            }
        }
       
        // launch_visible, launch_schedule, launch_time, launch_date
        if ($request->launch_visible == "DRAFT") {
            $eventDetail->status = "DRAFT";
            $eventDetail->sub_action_public_date  = null;
            $eventDetail->sub_action_public_time  = null;
        } else {
            $launch_date = $request->launch_date;
            $launch_time = $this->fixTimeToRemoveSeconds($request->launch_time);
            if ($request->launch_schedule == "schedule" && $launch_date && $launch_time) {
                $carbonPublishedDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->launch_date . ' ' . $launch_time)
                    ->utc();
                $eventDetail->status = "SCHEDULED";
                $eventDetail->sub_action_public_date  = $carbonPublishedDateTime->format('Y-m-d');
                $eventDetail->sub_action_public_time  = $carbonPublishedDateTime->format('H:i');
            } else if ($request->launch_schedule == "now") {
                $carbonPublishedDateTime = Carbon::now()->utc();
                $eventDetail->status = "LIVE";
                $eventDetail->sub_action_public_date = $carbonPublishedDateTime->format('Y-m-d');
                $eventDetail->sub_action_public_time = $carbonPublishedDateTime->format('H:i');
            } else {
                $eventDetail->status = "LIVE";
                $launch_date = null;
                $launch_time = null;
            }
        }
        $eventDetail->sub_action_private  = $request->launch_visible == "private" ? "private" : "public";
        $eventDetail->action  = $request->launch_visible;
        return $eventDetail;
    }


    public function store(Request $request)
    {
        // try {
        $fileNameFinal = null;
        if ($request->hasFile('eventBanner')) {
            $file = $request->file('eventBanner');
            $fileNameInitial = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
            $fileNameFinal = "images/events/$fileNameInitial";
            $file->storeAs('public/images/events/', $fileNameInitial);
        }
        $eventDetail = new EventDetail;
        $eventDetail = $this->storeLogic($eventDetail, $request);

        $eventDetail->user_id  = auth()->user()->id;
        $eventDetail->eventBanner  = $fileNameFinal;
        $eventDetail->save();
        if ($request->livePreview == "true") {
            return redirect('organizer/live/' . $eventDetail->id);
        }
        return redirect('organizer/success/' . $eventDetail->id);
        // } catch (\Exception $e) {
        //     return back()->with('error', 'Something went wrong with saving data!');
        // }
        // return redirect('organizer/home');
    }


    private function showCommonLogic($id)
    {
        $event = EventDetail
            ::with('type', 'tier', 'game')->findOrFail($id);
        $isUser = Auth::user()->id == $event->user_id;
        return [$event, $isUser];
    }

    public function showLive($id): View
    {
        [$event, $isUser] = $this->showCommonLogic($id);
        return view(
            'Organizer.ViewEvent',
            [
                'event' => $event,
                'mappingEventState' => $this->mappingEventState,
                'isUser' => $isUser,
                'livePreview' => 1
            ]
        );
    }

    public function showSuccess($id): View
    {
        [$event, $isUser] = $this->showCommonLogic($id);
        return view(
            'Organizer.CreateEventSuccess',
            [
                'event' => $event,
                'mappingEventState' => $this->mappingEventState,
                'isUser' => $isUser,
                'livePreview' => 1
            ]
        );
    }

    public function show($id): View
    {
        [$event, $isUser] = $this->showCommonLogic($id);
        return view(
            'Organizer.ViewEvent',
            [
                'event' => $event,
                'mappingEventState' => $this->mappingEventState,
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
        // try {
        $eventId = $id;
        $eventDetail = EventDetail::find($eventId);
        if ($eventId) {
            $fileNameFinal = null;
            if ($request->hasFile('eventBanner')) {
                $file = $request->file('eventBanner');
                $fileNameInitial = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
                $fileNameFinal = "images/events/$fileNameInitial";
                $file->storeAs('public/images/events/', $fileNameInitial);
                if ($eventDetail->eventBanner) {
                    // delete please
                    // unlink(storage_path('app/public/' . $eventDetail->eventBanner));
                }
            } else {
                $fileNameFinal = $eventDetail->eventBanner;
            }
            // Fetch the existing event from the database
            $eventDetail = EventDetail::find($eventId);
            $eventDetail = $this->storeLogic($eventDetail, $request);
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
        // } 
        // catch (\Exception $e) {
        // return back()->with('error', 'Something went wrong with saving data!');
        // }
    }

    public function destroy($id)
    {
        //
    }

    private $mappingEventState = [
        'UPCOMING' => [
            'buttonBackgroundColor' => '#43A4D7', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
        'LIVE' => [
            'buttonBackgroundColor' => '#43A4D7', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
        'SCHEDULED' => [
            'buttonBackgroundColor' => '#43A4D7', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
        'ONGOING' => [
            'buttonBackgroundColor' => '#FFFBFB', 'buttonTextColor' => 'black', 'borderColor' => 'black'
        ],
        'DRAFT' => [
            'buttonBackgroundColor' => '#8CCD39', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
        'ENDED' => [
            'buttonBackgroundColor' => '#A6A6A6', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
    ];
}
