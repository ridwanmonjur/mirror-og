<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDetail;
use Illuminate\Http\Request;

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        
        $count = 3;
        $events = EventDetail::paginate($count);
        $output = ['events' => $events, 'mappingEventState' => EventDetail::mappingEventStateResolve()];
        if ($request->ajax()) {
            $view = view(
                'Participant.HomeScroll',
                $output
            )->render();

            return response()->json(['html' => $view]);
        }
        return view(
            'Participant.Home',
            $output
        );
    }


    public function eventDetails()
    {
        $eventDetail = EventDetail::all();
        return view('Participant.Layout.HeadTag', compact('eventDetail'));
    }

}
