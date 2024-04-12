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
        $event = EventDetail
            ::with(['tier'])
            ->select(['id', 'eventBanner', 'eventName', 'eventDescription', 'event_tier_id' ])
            ->findOrFail($id);
    
        // dd($event);
        $awardList = Award::all();
        $awardsResult = AwardResults::where('event_details_id', $id)->get();
        $awardsResultMap = [];
        foreach ($awardsResult as $item) {
            $awardsResultMap[$item->id] = $item;
        }
        // dd($awardsResultMap);

        $joinEventAndTeamList = DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->leftJoin('teams', function ($join) use ($id) {
                $join->on('join_events.team_id', '=', 'teams.id');
            })
            ->leftJoin('event_join_results', function ($join) use ($id) {
                $join->on('join_events.id', '=', 'event_join_results.join_events_id');
            })
            ->leftJoin('awards_results', function ($join) use ($id) {
                $join->on('join_events.id', '=', 'awards_results.join_events_id');
            })
            ->select('join_events.id as id1', 
                'join_events.event_details_id', 
                'join_events.team_id',
                'teams.*', 
                'event_join_results.position',
                'awards_results.award_id',
        );

        $joinEventAndTeamList = DB::table(DB::raw("({$joinEventAndTeamList->toSql()}) as join_events2"))
            ->mergeBindings($joinEventAndTeamList)
            ->leftJoin('awards', function ($join) {
                $join->on('join_events2.award_id', '=', 'awards.id'); // Corrected the join condition
            })
            ->select(
                'join_events2.*', 
                'awards.title as awards_title', 
                'awards.image as awards_image'
            )
            ->get();
            // dd($joinEventAndTeamList);
            

        return view('Organizer.EventResults', compact(
            'event', 'awardList', 'awardsResultMap', 'joinEventAndTeamList'
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
