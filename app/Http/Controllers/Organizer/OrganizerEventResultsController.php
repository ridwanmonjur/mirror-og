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
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('event_join_results', 'join_events.id', '=', 'event_join_results.join_events_id')
            ->select('join_events.id as id1', 
                'join_events.event_details_id', 
                'join_events.team_id',
                'teams.*', 
                'event_join_results.position',
        )->get();
        
        $awardAndTeamList = DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('awards_results', 'join_events.id', '=', 'awards_results.join_events_id')
            ->leftJoin('awards', 'awards_results.award_id', '=', 'awards.id')
            // ->leftJoin('achievements', 'join_events.id', '=', 'achievements.join_event_id')
            ->select(
                'join_events.id as id1', 
                'join_events.event_details_id', 
                'join_events.team_id',
                'teams.*',
                'awards_results.id as results_id',
                'awards_results.award_id',
                'awards.title as awards_title', 
                'awards.image as awards_image',
                // 'achievements.id as achievements_id', 
                // 'achievements.title as achievements_title', 
                // 'achievements.description as achievements_description',
                // 'achievements.created_at as achievements_created_at',
            )
            ->get();

            $achievementsAndTeamList = DB::table('join_events')
            ->where('join_events.event_details_id', '=', $id)
            ->leftJoin('teams', 'join_events.team_id', '=', 'teams.id')
            ->leftJoin('achievements', 'join_events.id', '=', 'achievements.join_event_id')
            ->select(
                'join_events.id as id1', 
                'join_events.event_details_id', 
                'join_events.team_id',
                'teams.*',
                'achievements.id as achievements_id', 
                'achievements.title as achievements_title', 
                'achievements.description as achievements_description',
                'achievements.created_at as achievements_created_at',
            )
            ->get();
        // dd($joinEventAndTeamList, $awardAndTeamList, $awardList);
            

        return view('Organizer.EventResults', compact(
            'event', 'awardList', 'joinEventAndTeamList', 'awardAndTeamList', 'achievementsAndTeamList'
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
            $eventId = $request->input('event_details_id');
            $teamId = $request->input('team_id');
            $joinEvent = DB::table('join_events')
                ->where('team_id', $teamId)
                ->where('event_details_id', $eventId)
                ->select('id', 'team_id')
                ->get()->first();
            $awardExists = DB::table('awards')->where('id', $awardId)->exists();
            $teamExists = DB::table('teams')->where('id', $teamId)->exists();
            if ($joinEvent && $awardExists && $teamExists) {
                DB::table('awards_results')->insert([
                    'team_id' => $teamId,
                    'award_id' => $awardId,
                    'join_events_id' => $joinEvent->id,
                ]);
            
                return response()->json(['success' => true, 'message' => 'Award given successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Join event, team or event details not found'], 400);
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
     * Store a newly created award in storage.
     */
    public function storeAchievements(Request $request)
    {
        try {
            $title = $request->input('title');
            $description = $request->input('description');
            $eventId = $request->input('event_details_id');
            $teamId = $request->input('team_id');
            $joinEvent = DB::table('join_events')
                ->select('id', 'team_id')
                ->where('team_id', $teamId)
                ->where('event_details_id', $eventId)
                ->get()->first();
                
            // dd($joinEvent, $teamId);
            
            $teamExists = DB::table('teams')->where('id', $teamId)->exists();
            if ($joinEvent && $teamExists) {
                DB::table('achievements')->insert([
                    'join_event_id' => $joinEvent->id,
                    'title' => $title,
                    'description' => $description,
                ]);
            
                return response()->json(['success' => true, 'message' => 'Achievement given successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Join event or team not found'], 400);
            }
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { 
                return response()->json(['success' => false, 'message' => 'Achievement already exists'], 422);
            } else {
                return response()->json(['success' => false, 'message' => 'Database error'], 500);
            }
        }
    }


    /**
     * Remove the specified award from storage.
     */
    public function destroyAward($id, $awardId)
    {
        try {
            DB::table('awards_results')->where('id', $awardId)->delete();
            return response()->json(['success' => true, 'message' => 'Award deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Award not found'], 400);
        }
    }

    public function destroyAchievements($id, $achievementId)
    {
        try {
            DB::table('achievements')->where('id', $achievementId)->delete();
            return response()->json(['success' => true, 'message' => 'Achievement deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Achievement not found'], 400);
        }
    }
}
