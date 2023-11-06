<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        $count = 4;
        $events = Event::paginate($count);
        if ($request->ajax()) {
            $view = view(
                'Participant.HomeScroll',
                compact('events')
            )->render();

            return response()->json(['html' => $view]);
        }
        return view(
            'Participant.Home',
            compact('events')
        );
    }
}
