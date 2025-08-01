<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Match\MatchUpsertRequest;
use App\Jobs\HandleResults;
use App\Models\Achievements;
use App\Models\Award;
use App\Models\AwardResults;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\JoinEvent;
use App\Models\Brackets;
use App\Models\Team;
use App\Models\User;
use App\Services\EventMatchService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizerEventResultsController extends Controller
{
    private $eventMatchService;

    public function __construct(EventMatchService $eventMatchService)
    {
        $this->eventMatchService = $eventMatchService;
    }

    public function index(Request $request, $id)
    {
        $event = EventDetail::with(['tier', 'user'])
            ->select(['id', 'eventBanner', 'eventName', 'eventDescription', 'event_tier_id', 'user_id', 'slug'])
            ->where('id', $id)
            ->firstOrFail();

        $awardList = Award::all();

        $joinEventAndTeamList = EventJoinResults::getEventJoinResults($id);
        $awardAndTeamList = AwardResults::getTeamAwardResults($id);
        $achievementsAndTeamList = Achievements::getTeamAchievements($id);

        return view('Organizer.EventResults', compact('event', 'awardList', 'joinEventAndTeamList', 'awardAndTeamList', 'achievementsAndTeamList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $joinEventsId = $request->join_events_id;
        $existingRow = DB::table('event_join_results')->where('join_events_id', $joinEventsId)->first();

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

        return response()->json(['success' => true, 'message' => 'Position updated successfully'], 200);
    }

    /**
     * Store a newly created award in storage.
     */
    public function storeAward(Request $request)
    {
        try {
            [$team, $memberUserIds] = Team::getResultsTeamMemberIds($request->team_id);

            $joinEvent = DB::table('join_events')->where('team_id', $request->team_id)->where('event_details_id', $request->input('event_details_id'))->select('id', 'team_id', 'slug')->first();
            $awardExists = DB::table('awards')->where('id', $request->input('award_id'))->exists();
            $teamExists = DB::table('teams')->where('id', $request->team_id)->exists();
            if ($joinEvent && $awardExists && $teamExists) {
                $rowId = DB::table('awards_results')->insertGetId([
                    'team_id' => $request->team_id,
                    'award_id' => $request->input('award_id'),
                    'join_events_id' => $joinEvent->id,
                ]);

                dispatch(
                    new HandleResults('AddAward', [
                        'subject_type' => User::class,
                        'object_type' => AwardResults::class,
                        'subject_id' => $memberUserIds,
                        'object_id' => $rowId,
                        'action' => 'Award',
                        'eventId' => $request->input('event_details_id'),
                        'award' => $request->award,
                        'teamName' => $team->teamName,
                        'teamId' => $team->id,
                        'image' => $team->teamBanner,
                    ]),
                );

                return response()->json(['success' => true, 'message' => 'Award given successfully'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Join event, team or event details not found'], 400);
        } catch (Exception $e) {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return response()->json(['success' => false, 'message' => 'Award already exists'], 422);
            }

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created award in storage.
     */
    public function storeAchievements(Request $request)
    {
        try {
            [$team, $memberUserIds] = Team::getResultsTeamMemberIds($request->team_id);

            $joinEvent = DB::table('join_events')->select('id', 'team_id', 'slug')->where('team_id', $request->team_id)->where('event_details_id', $request->input('event_details_id'))->first();

            $teamExists = DB::table('teams')->where('id', $request->team_id)->exists();
            if ($joinEvent && $teamExists) {
                $rowId = DB::table('achievements')->insertGetId([
                    'join_event_id' => $joinEvent->id,
                    'title' => $request->title,
                    'description' => $request->description,
                ]);

                dispatch(
                    new HandleResults('AddAchievement', [
                        'subject_type' => User::class,
                        'object_type' => Achievements::class,
                        'subject_id' => $memberUserIds,
                        'object_id' => $rowId,
                        'action' => 'Achievement',
                        'achievement' => $request->title,
                        'eventId' => $request->input('event_details_id'),
                        'teamName' => $team->teamName,
                        'teamId' => $team->id,
                        'image' => $team->teamBanner,
                    ]),
                );

                return response()->json(['success' => true, 'message' => 'Achievement given successfully'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Join event or team not found'], 400);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return response()->json(['success' => false, 'message' => 'Achievement already exists'], 422);
            }

            return response()->json(['success' => false, 'message' => 'Database error'], 500);
        }
    }

    /**
     * Remove the specified award from storage.
     */
    public function destroyAward(Request $request, $id, $awardId)
    {
        try {
            $row = DB::table('awards_results')->where('id', $awardId)->first();

            if ($row) {
                [, $memberUserIds] = Team::getResultsTeamMemberIds($row->team_id);

                DB::table('awards_results')->where('id', $awardId)->delete();

                dispatch(
                    new HandleResults('DeleteAward', [
                        'subject_type' => User::class,
                        'object_type' => AwardResults::class,
                        'subject_id' => $memberUserIds,
                        'object_id' => $row->id,
                        'action' => 'Award',
                        'teamName' => $request->teamName,
                    ]),
                );
            }

            return response()->json(['success' => true, 'message' => 'Award deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroyAchievements(Request $request, $achievementId)
    {
        try {
            $row = DB::table('achievements')->where('id', $achievementId)->first();
            $join = JoinEvent::where('id', $row->join_event_id)
                ->select(['id', 'team_id'])
                ->first();
            [, $memberUserIds] = Team::getResultsTeamMemberIds($join->team_id);

            if ($row) {
                DB::table('achievements')->where('id', $achievementId)->delete();
            }

            dispatch(
                new HandleResults('DeleteAchievement', [
                    'subject_type' => User::class,
                    'object_type' => Achievements::class,
                    'subject_id' => $memberUserIds,
                    'object_id' => $row->id,
                    'action' => 'Achievement',
                    'teamName' => $request->teamName,
                ]),
            );

            return response()->json(['success' => true, 'message' => 'Achievement deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function viewBrackets(Request $request, $id)
    {
        $event = EventDetail::with(['type'])->findOrFail($id);

        if (! $event->type->eventType) {
            return $this->showErrorOrganizer("Event with id: {$id} cannot be of this type: {$event->type->eventType}");
        }

        $event->load([
            'tier',
            'type',
            'joinEvents.team' => function ($query) {
                $query->select('teams.id', 'teamName', 'teamBanner', 'slug');
            },
        ]);

        $bracket = $this->eventMatchService->generateBrackets($event, true, null);

        return view('Organizer.Brackets', [
            'id' => $id,
            'eventType' => $event->type->eventType,
            'event' => $event,
            ...$bracket,
        ]);
    }

    public function upsertBracket(MatchUpsertRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $match = isset($validatedData['id']) ? Brackets::findOrFail($validatedData['id']) : new Brackets;

            if (! $match->id) {
                Brackets::where([
                    'team1_position' => $match->team1_position,
                    'team2_position' => $match->team2_position,
                    'event_details_id' => $match->event_details_id,
                ])->doesntExistOr(function () {
                    throw new \ErrorException('There is already an event with this ID!');
                });
            }

            $team1 = $validatedData['team1_id'] ? Team::find($validatedData['team1_id']) : null;
            $team2 = $validatedData['team2_id'] ? Team::find($validatedData['team2_id']) : null;

            $event = EventDetail::findOrFail($validatedData['event_details_id']);
            $match->fill($validatedData);
            $match->event_details_id = $event->id;
            $match->save();

            if ($team1 && $team2) {
                $joinEventId = JoinEvent::where('event_details_id', $event->id)
                    ->whereIn('team_id', [$team1?->id, $team2?->id])
                    ->get()
                    ->pluck(value: 'id');
            } else {
                $joinEventId = null;
            }

            // TODO
            // find order
            // $bracketSetup = DB::table('brackets_setup')
            // ->where('event_tier_id', $tierId)
            // ->where(function($query) use ($next_position) {
            //     $query->where('team1_position', $next_position)
            //         ->orWhere('team2_position', $next_position);
            // })
            // ->first();
            // also stage_name, inner_stage & order

            if ($joinEventId && $team1) {
                // TODO only for first brackets
                $team1->load([
                    'roster' => function ($query) use ($joinEventId) {
                        $query->whereIn('join_events_id', $joinEventId)->with('user');
                    },
                ]);
            }

            if ($joinEventId && $team2) {
                // TODO only for first brackets
                $team2?->load([
                    'roster' => function ($query) use ($joinEventId) {
                        $query->whereIn('join_events_id', $joinEventId)->with('user');
                    },
                ]);
            }

            $message = isset($validatedData['id']) ? 'Match updated successfully' : 'Match created successfully';

            return response()->json([
                'success' => true,
                'data' => [
                    'match' => $match,
                    'team1' => $team1,
                    'team2' => $team2,
                ],
                'message' => $message,
            ]);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => null,
                    'message' => 'An error occurred: '.$e->getMessage(),
                ],
                500,
            );
        }
    }
}
