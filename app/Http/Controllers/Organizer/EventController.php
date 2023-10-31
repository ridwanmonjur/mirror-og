<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{


    public function home(): View
    {
        return view('Organizer.Home');
    }

    public function index()
    {
        $user = Auth::user();
        $eventList = Event::with('eventDetail', 'eventCategory')
            ->where('user_id', $user->id)
            ->get();
        $organizer = Organizer::where('user_id', $user->id)->first();
        $count = $eventList->count();
        return view(
            'Organizer.ManageEvent',
            [
                'eventList' => $eventList,
                'count' => $count,
                'user' => $user,
                'organizer' => $organizer,
                'mappingEventState' => $this->mappingEventState
            ]
        );
    }

    public function create(): View
    {
        $eventCategory = EventCategory::all();
        // return view('Organizer.CreateEvent.event');
        return view('Organizer.CreateEvent', ['eventCategory' => $eventCategory ]);
    }


    public function store(Request $request)
    {

        // dd($request->all());
        // return view('Organizer.CreateEvent');

        $eventDetail = new EventDetail;
        $eventDetail->startDate = $request->startDate;
        $eventDetail->endDate = $request->endDate;
        $eventDetail->startTime = $request->startTime;
        $eventDetail->endTime  = $request->endTime;
        $eventDetail->eventName  = $request->eventName;
        $eventDetail->eventDescription  = $request->eventDescription;
        $eventDetail->eventTags  = $request->eventTags;
        $eventDetail->eventBanner  = $request->eventBanner;
        $eventDetail->save();

        $event = new Event;
        $event->eventName = $request->eventName;
        $event->status = $request->status;
        $event->venue = $request->venue;
        $event->sub_action_public_date  = $request->sub_action_public_date;
        $event->sub_action_public_time  = $request->sub_action_public_time;
        $event->sub_action_private  = $request->sub_action_private;
        $event->action  = $request->action;
        $event->save();

        return redirect('event.index');



    }

    public function show($id): View
    {
        $event = Event::findOrFail($id);
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
