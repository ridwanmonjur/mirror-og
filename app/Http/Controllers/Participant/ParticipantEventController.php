<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class ParticipantEventController extends Controller
{
    public function home()
    {
        $events = Event::all();

        return view('Participant.Home', ['events' => $events]);
    }
}
