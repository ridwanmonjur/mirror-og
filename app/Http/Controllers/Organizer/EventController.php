<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
        // return view('Organizer.CreateEvent.event');
        return view('Organizer.CreateEvent', ['eventCategory' => $eventCategory, 'mappingTierState' => $this->mappingTierState]);
    }


    public function store(Request $request)
    {
        $path = null;
        if ($request->hasFile('eventBanner')) {
            $file = $request->file('eventBanner');
            $fileName = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/images', $fileName);
        } else {
            $file = null;
        }
        $eventDetail = new EventDetail;
        // step1
        $eventDetail->gameTitle = $request->gameTitle;
        $eventDetail->eventType = $request->eventType;
        $eventDetail->eventTier = $request->eventTier;
        // step2
        $carbonStartDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->startDate . ' ' . $request->startTime)
            ->utc();
        $carbonEndDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->endDate . ' ' . $request->endTime)
            ->utc();
        $eventDetail->startDate = $carbonStartDateTime->format('m/d-Y');
        $eventDetail->endDate = $carbonStartDateTime->format('H:i');
        $eventDetail->startTime =  $carbonEndDateTime->format('Y-m-d');
        $eventDetail->endTime  = $carbonEndDateTime->format('H:i');
        $eventDetail->eventName  = $request->eventName;
        $eventDetail->eventDescription  = $request->eventDescription;
        $eventDetail->eventTags  = $request->eventTags;
        $eventDetail->eventBanner  = $path;
        // launch_visible, launch_schedule, launch_time, launch_date
        if ($request->launch_visible == "draft") {
            $eventDetail->status = "DRAFT";
            $eventDetail->sub_action_public_date  = null;
            $eventDetail->sub_action_public_time  = null;
            $eventDetail->sub_action_private  = null;
            $eventDetail->action  = $request->launch_visible;
        } else {
            if ($request->launch_visible == "") {
                $eventDetail->status = "LIVE";
                $eventDetail->sub_action_public_date = null;
                $eventDetail->sub_action_public_time = null;
            } else {
                $carbonPublishedDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->launch_date . ' ' . $request->launch_time)
                    ->utc();
                $eventDetail->status = "FUTURE_LIVE";
                $eventDetail->sub_action_public_date  = $carbonPublishedDateTime->format('Y-m-d');
                $eventDetail->sub_action_public_time  = $carbonPublishedDateTime->format('H:i');
            }
            $eventDetail->sub_action_private  = $request->launch_visible == "public" ? "public" : "private";
            $eventDetail->action  = $request->launch_visible;
        }

        $eventDetail->user_id  = auth()->user()->id;
        $eventDetail->save();
        return redirect('organizer/event/' . $eventDetail->id);
        // return redirect('organizer/home');
    }

    public function show($id): View
    {
        $event = EventDetail::findOrFail($id);
        $isUser = Auth::user()->id == $event->user_id;
        return view(
            'Organizer.ViewEvent',
            ['event' => $event, 'mappingEventState' => $this->mappingEventState, 'isUser' => $isUser]
        );
    }


    public function edit($id)
    {
        //
    }


    public function update($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    private $mappingTierState = [
        'Dolphin' => [
            'person' => 16, 'prize' => '5000', 'entry' => 10
        ],
        'Turtle' => [
            'person' => 32, 'prize' => '10000', 'entry' => 20
        ],
        'Starfish' => [
            'person' => 64, 'prize' => '15000', 'entry' => 30
        ],
    ];

    private $mappingEventState = [
        'UPCOMING' => [
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
