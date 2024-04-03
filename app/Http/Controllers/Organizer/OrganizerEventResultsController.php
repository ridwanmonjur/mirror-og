<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\AwardResults;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizerEventResultsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        $event = EventDetail::select(['id', 'eventBanner', 'eventName', 'eventDescription'])
            ->findOrFail($id);
    
        // dd($event);
        $awardList = Award::all();
        $awardsResultList = AwardResults::where('event_details_id', $id)->get();
        $joinEventList = DB::table('join_events')
            ->join('teams', 'join_events.team_id', '=', 'teams.id')
            ->where('join_events.event_details_id', '=', $id)
            ->select('join_events.event_details_id', 'join_events.team_id', 'teams.*')
            ->get();
        
        return view('Organizer.EventResults', compact(
            'event', 'awardList', 'awardsResultList', 'joinEventList'
        ));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

     /**
     * Store a newly created award in storage.
     */
    public function storeAward(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified award from storage.
     */
    public function destroyAward(string $id)
    {
        //
    }
}
