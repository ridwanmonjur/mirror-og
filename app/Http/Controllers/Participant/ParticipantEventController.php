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
        $output = compact("events");
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
}
