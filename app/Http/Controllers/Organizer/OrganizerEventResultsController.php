<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Jobs\HandleResults;
use App\Models\Achievements;
use App\Models\Award;
use App\Models\AwardResults;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\JoinEvent;
use App\Models\Team;
use App\Models\User;
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
        $event = EventDetail::with(['tier'])
            ->select(['id', 'eventBanner', 'eventName', 'eventDescription', 'event_tier_id'])
            ->findOrFail($id);

        $awardList = Award::all();
        $joinEventAndTeamList = EventJoinResults::getEventJoinResults($id);
        $awardAndTeamList = AwardResults::getTeamAwardResults($id);
        $achievementsAndTeamList = Achievements::getTeamAchievements($id);

        return view('Organizer.EventResults', compact(
            'event', 'awardList', 'joinEventAndTeamList', 'awardAndTeamList', 'achievementsAndTeamList'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $memberUserIds = Team::where('teamName', $request->teamName)
            ->select('id', 'teamName')
            ->with(['members' => function ($q) {
                $q
                    ->where('status', 'accepted')
                    ->select('id', 'user_id', 'team_id', 'status')
                    ->with(['user' => function ($q) {
                        $q->select('id');
                    },
                    ]);
            }])
            ->first()
            ->members
            ->pluck('user.id')
            ->toArray();

        $joinEventsId = $request->join_events_id;
        $joinEvent = JoinEvent::where('id', $request->join_events_id)->first();
        $existingRow = DB::table('event_join_results')
            ->where('join_events_id', $joinEventsId)
            ->first();

        if ($existingRow) {
            DB::table('event_join_results')
                ->where('join_events_id', $joinEventsId)
                ->update(['position' => $request->position]);
        } else {
            DB::table('event_join_results')->insert([
                'join_events_id' => $joinEventsId,
                'position' => $request->position,
            ]);
        }

        dispatch(new HandleResults('ChangePosition', [
            'subject_type' => User::class,
            'object_type' => EventJoinResults::class,
            'subject_id' => $memberUserIds,
            'object_id' => $joinEventsId,
            'action' => 'Position',
            'eventId' => $joinEvent->event_details_id,
            'teamName' => $request->teamName,
            'image' => $request->teamBanner,
            'position' => intval($request->position),
        ]));

        return response()->json(['success' => true, 'message' => 'Position updated successfully'], 200);
    }

    /**
     * Store a newly created award in storage.
     */
    public function storeAward(Request $request)
    {
        try {
            [$team, $memberUserIds] = Team::getResultsTeamMemberIds($request->team_id);

            $joinEvent = DB::table('join_events')
                ->where('team_id', $request->team_id)
                ->where('event_details_id', $request->input('event_details_id'))
                ->select('id', 'team_id')
                ->get()->first();
            $awardExists = DB::table('awards')->where('id', $request->input('award_id'))->exists();
            $teamExists = DB::table('teams')->where('id', $request->team_id)->exists();
            if ($joinEvent && $awardExists && $teamExists) {
                $rowId = DB::table('awards_results')->insertGetId([
                    'team_id' => $request->team_id,
                    'award_id' => $request->input('award_id'),
                    'join_events_id' => $joinEvent->id,
                ]);

                dispatch(new HandleResults('AddAward', [
                    'subject_type' => User::class,
                    'object_type' => AwardResults::class,
                    'subject_id' => $memberUserIds,
                    'object_id' => $rowId,
                    'action' => 'Award',
                    'eventId' => $request->input('event_details_id'),
                    'award' => $request->award,
                    'teamName' => $team->teamName,
                    'image' => $team->teamBanner,
                ]));

                return response()->json(['success' => true, 'message' => 'Award given successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Join event, team or event details not found'], 400);
            }
        } catch (Exception $e) {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return response()->json(['success' => false, 'message' => 'Award already exists'], 422);
            } else {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Store a newly created award in storage.
     */
    public function storeAchievements(Request $request)
    {
        try {
            [$team, $memberUserIds] = Team::getResultsTeamMemberIds($request->team_id);

            $joinEvent = DB::table('join_events')
                ->select('id', 'team_id')
                ->where('team_id', $request->team_id)
                ->where('event_details_id', $request->input('event_details_id'))
                ->get()->first();

            $teamExists = DB::table('teams')->where('id', $request->team_id)->exists();
            if ($joinEvent && $teamExists) {
                $rowId = DB::table('achievements')->insertGetId([
                    'join_event_id' => $joinEvent->id,
                    'title' => $request->title,
                    'description' => $request->description,
                ]);

                dispatch(new HandleResults('AddAchievement', [
                    'subject_type' => User::class,
                    'object_type' => Achievements::class,
                    'subject_id' => $memberUserIds,
                    'object_id' => $rowId,
                    'action' => 'Achievement',
                    'achievement' => $request->title,
                    'eventId' => $request->input('event_details_id'),
                    'teamName' => $team->teamName,
                    'image' => $team->teamBanner,
                ]));

                return response()->json(['success' => true, 'message' => 'Achievement given successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Join event or team not found'], 400);
            }
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return response()->json(['success' => false, 'message' => 'Achievement already exists'], 422);
            } else {
                return response()->json(['success' => false, 'message' => 'Database error'], 500);
            }
        }
    }

    /**
     * Remove the specified award from storage.
     */
    public function destroyAward(Request $request, $id, $awardId)
    {
        try {
            $row = DB::table('awards_results')->where('id', $awardId)->first();
            [, $memberUserIds] = Team::getResultsTeamMemberIds($row->team_id);

            if ($row) {
                DB::table('awards_results')->where('id', $awardId)->delete();
            }

            dispatch(new HandleResults('DeleteAward', [
                'subject_type' => User::class,
                'object_type' => AwardResults::class,
                'subject_id' => $memberUserIds,
                'object_id' => $row->id,
                'action' => 'Award',
                'teamName' => $request->teamName,
            ]));

            return response()->json(['success' => true, 'message' => 'Award deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroyAchievements(Request $request, $id, $achievementId)
    {
        try {
            $row = DB::table('achievements')->where('id', $achievementId)->first();
            $join = JoinEvent::where('id', $row->join_event_id)->select(['id', 'team_id'])->first();
            [, $memberUserIds] = Team::getResultsTeamMemberIds($join->team_id);

            if ($row) {
                DB::table('achievements')->where('id', $achievementId)->delete();
            }
            dispatch(new HandleResults('DeleteAchievement', [
                'subject_type' => User::class,
                'object_type' => Achievements::class,
                'subject_id' => $memberUserIds,
                'object_id' => $row->id,
                'action' => 'Achievement',
                'teamName' => $request->teamName,
            ]));

            return response()->json(['success' => true, 'message' => 'Achievement deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
