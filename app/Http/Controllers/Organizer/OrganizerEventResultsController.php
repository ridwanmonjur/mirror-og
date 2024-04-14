<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\AwardResults;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use Exception;
use Illuminate\Database\QueryException;
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
        $joinEventAndTeamList = DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->leftJoin('teams', function ($join) use ($id) {
                $join->on('join_events.team_id', '=', 'teams.id');
            })
            ->leftJoin('event_join_results', function ($join) use ($id) {
                $join->on('join_events.id', '=', 'event_join_results.join_events_id');
            })
            ->select('join_events.id as id1', 
                'join_events.event_details_id', 
                'join_events.team_id',
                'teams.*', 
                'event_join_results.position',
        )->get();
        
        $subquery = DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->leftJoin('teams', function ($join) use ($id) {
                $join->on('join_events.team_id', '=', 'teams.id');
            })
            ->leftJoin('awards_results', function ($join) use ($id) {
                $join->on('join_events.id', '=', 'awards_results.join_events_id');
            })
            ->select('join_events.id as id1', 
                'join_events.event_details_id', 
                'join_events.team_id',
                'teams.*',
                'awards_results.award_id',
            );

        $awardAndTeamList = DB::table(DB::raw("({$subquery->toSql()}) as join_events2"))
            ->mergeBindings($subquery)
            ->leftJoin('awards', function ($join) {
                $join->on('join_events2.award_id', '=', 'awards.id'); 
            })
            ->select(
                'join_events2.*', 
                'awards.title as awards_title', 
                'awards.image as awards_image'
            )
            ->get();
        // dd($joinEventAndTeamList, $awardAndTeamList, $awardList);
            

        return view('Organizer.EventResults', compact(
            'event', 'awardList', 'joinEventAndTeamList', 'awardAndTeamList'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $joinEventsId = $request->join_events_id;
        $position = $request->position;

        $existingRow = DB::table('event_join_results')->where('join_events_id', $joinEventsId)->first();

        if ($existingRow) {
            DB::table('event_join_results')
                ->where('join_events_id', $joinEventsId)
                ->update([
                    'position' => $position,
                ]);
        } else {
            DB::table('event_join_results')->insert([
                'join_events_id' => $joinEventsId,
                'position' => $position,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Position updated successfully'], 200);
    }

     /**
     * Store a newly created award in storage.
     */
    public function storeAward(Request $request)
    {
        try {
            $awardId = $request->input('award_id');
            $joinEventId = $request->input('join_events_id');
            $teamId = $request->input('team_id');
            $joinEventExists = DB::table('join_events')->where('id', $joinEventId)->exists();
            $awardExists = DB::table('awards')->where('id', $awardId)->exists();
            $teamExists = DB::table('teams')->where('id', $teamId)->exists();
            if ($joinEventExists && $awardExists && $teamExists) {
                DB::table('awards_results')->insert([
                    'team_id' => $teamId,
                    'award_id' => $awardId,
                    'join_events_id' => $joinEventId,
                ]);
            
                return response()->json(['success' => true, 'message' => 'Award given successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Join event, team or event details not found'], 404);
            }
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { 
                return response()->json(['success' => false, 'message' => 'Award already exists'], 422);
            } else {
                return response()->json(['success' => false, 'message' => 'Database error'], 500);
            }
        }
        
    }


    /**
     * Remove the specified award from storage.
     */
    public function destroy($id)
    {
        try {
            DB::table('awards')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Award deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Award not found'], 404);
        }
    }
}
