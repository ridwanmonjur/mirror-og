<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Replace this with your actual logic to fetch event data from your database or other sources
        $events = Participant::all();

        return view('home', ['events' => $events]);
    }
}
