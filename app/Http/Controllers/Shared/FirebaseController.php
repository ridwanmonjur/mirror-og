<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Brackets;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Firebase;
use App\Services\FirestoreService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;

class FirebaseController extends Controller
{
    private $auth;
    private $firestore;
    private FirestoreService $firestoreService;

    public function __construct(Auth $auth, Firestore $firestore, FirestoreService $firestoreService)
    {
        $this->auth = $auth;
        $this->firestore = $firestore;
        $this->firestoreService = $firestoreService;
    }

    public function createToken()
    {
        if (session()->has('firebase_token')) {
            $tokenData = session('firebase_token');

            if (time() - $tokenData['created_at'] < 3600) {
                return [
                    'token' => $tokenData['token'],
                    'claims' => $tokenData['claims'],
                    'from_session' => true,
                ];
            }
        }

        try {
            $user = auth()->user();
            $claims = [
                'uid' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ];

            $token = $this->auth->createCustomToken($user->id, $claims)->toString();
            session([
                'firebase_token' => [
                    'token' => $token,
                    'claims' => $claims,
                    'created_at' => time(),
                ],
            ]);

            return [
                'token' => $token,
                'claims' => $claims,
                'from_session' => false,
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleBlock(Request $request, $id): JsonResponse
    {
        try {
            $authenticatedUser = $request->attributes->get('user');
            $user = User::where('id', $id)->select('id')->first();

            if ($authenticatedUser->id == $user->id) {
                return response()->json(
                    [
                        'message' => "Can't block yourself",
                        'is_blocked' => 'False',
                    ],
                    404,
                );
            }
            if ($authenticatedUser->hasBlocked($user)) {
                $authenticatedUser->blocks()->detach($user);
                $message = 'User unblocked successfully';
                $authenticatedUser->save();
                $isBlocked = false;

                $roomCollectionRef = $this->firestore->database()->collection('room');

                $query1 = $roomCollectionRef->where('user1', '==', (string) $user->id)->where('user2', '==', (string) $authenticatedUser->id);

                $query2 = $roomCollectionRef->where('user2', '==', (string) $user->id)->where('user1', '==', (string) $authenticatedUser->id);

                $snapshot1 = $query1->documents();
                $snapshot2 = $query2->documents();

                foreach ($snapshot1 as $document) {
                    $roomRef = $roomCollectionRef->document($document->id());
                    $roomRef->update([['path' => 'blocked_by', 'value' => null]]);
                }

                foreach ($snapshot2 as $document) {
                    $roomRef = $roomCollectionRef->document($document->id());
                    $roomRef->update([['path' => 'blocked_by', 'value' => null]]);
                }
            } else {
                $authenticatedUser->blocks()->attach($user);
                $authenticatedUser->save();
                $message = 'User blocked successfully';
                $isBlocked = true;

                if (!$user->hasBlocked($authenticatedUser)) {
                    $roomCollectionRef = $this->firestore->database()->collection('room');

                    $query1 = $roomCollectionRef->where('user1', '==', (string) $user->id)->where('user2', '==', (string) $authenticatedUser->id);

                    $query2 = $roomCollectionRef->where('user2', '==', (string) $user->id)->where('user1', '==', (string) $authenticatedUser->id);

                    $snapshot1 = $query1->documents();
                    $snapshot2 = $query2->documents();

                    foreach ($snapshot1 as $document) {
                        $roomRef = $roomCollectionRef->document($document->id());
                        $roomRef->update([['path' => 'blocked_by', 'value' => $authenticatedUser->id]]);
                    }

                    foreach ($snapshot2 as $document) {
                        $roomRef = $roomCollectionRef->document($document->id());
                        $roomRef->update([['path' => 'blocked_by', 'value' => $authenticatedUser->id]]);
                    }
                }
            }

            return response()->json([
                'message' => $message,
                'is_blocked' => $isBlocked,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    /**
     * Create specific match documents with predefined winners
     *
     * @return array Response with status and document references
     */
    public function seedResults(Request $request, $eventId)
    {
        // Define the document specifications with document IDs as keys
        $documentSpecs = [
            'W1.W2' => [
                'team1Winners' => ['0', null, null], // DEFAULT WINNERS
            ],
            'W3.W4' => [
                'team1Winners' => ['0', '1', null], // DEFAULT WINNERS
            ],
            'W5.W6' => [
                'team1Winners' => ['0', '1', '1'],
            ],
            'W7.W8' => [
                'team1Winners' => ['1', '1', '1'], // RANDOM WINNERS
                'team2Winners' => ['1', '0', '1'],
            ],
            'W9.W10' => [
                'team1Winners' => ['0', '1', '0'],
                'team2Winners' => ['1', '0', '1'],
            ],
            'W11.W12' => [
                'team1Winners' => ['0', '1', '0'],
                'team2Winners' => ['1', '0', '1'],
            ],
            'W13.W14' => [
                'team1Winners' => [null, null, null],
                'team2Winners' => [null, null, null],
            ],
            'L3.L4' => [
                'team1Winners' => ['0', '1', '0'],
                'team2Winners' => ['1', '0', '1'],
            ],
            'L5.L6' => [
                'team1Winners' => ['0', '1', '1'],
            ],
            'L7.L8' => [
                'team1Winners' => ['1', '1', '1'],
                'team2Winners' => ['1', '0', '1'],
            ],
            'L9.L10' => [
                'team1Winners' => ['0', '1', '0'],
                'team2Winners' => ['1', '0', '1'],
            ],
        ];

        $customValuesArray = [];
        $specificIds = [];

        foreach ($documentSpecs as $documentId => $customValues) {
            $specificIds[] = $documentId;
            $customValuesArray[] = $customValues;
        }

        $reports = $this->firestoreService->createBatchReports($eventId, count($specificIds), $customValuesArray, $specificIds);

        $disputeSpecs = [
            'W11.W12.0' => [
                'dispute_image_videos' => ['media/img/dispute_evidence_3.jpg'],
                'dispute_reason' => 'There is suspected compromises to match integrity (e.g. match-fixing).',
                'dispute_teamId' => '24',
                'dispute_teamNumber' => '0',
                'dispute_userId' => '86',
                'match_number' => '0',
                'report_id' => 'W11.W12',
            ],
            'W11.W12.1' => [
                'dispute_description' => 'Team 24 is disputing the result of match 1',
                'dispute_reason' => 'Disputed game result due to server lag/disconnection.',
                'dispute_teamId' => '24',
                'dispute_teamNumber' => '0',
                'dispute_userId' => '86',
                'match_number' => '1',
                'report_id' => 'W11.W12',
            ],
            'W11.W12.2' => [
                'dispute_description' => 'Both teams using restricted characters',
                'dispute_image_videos' => ['media/img/dispute_evidence_1.jpg', 'media/img/dispute_evidence_2.jpg'],
                'dispute_reason' => 'Disputed game result due to use of prohibited characters/hero/content.',
                'dispute_teamId' => '24',
                'dispute_teamNumber' => '0',
                'dispute_userId' => '86',
                'match_number' => '2',
                'report_id' => 'W11.W12',
            ],
        ];

        $customValuesArray = [];
        $specificIds = [];

        foreach ($disputeSpecs as $disputeId => $customValues) {
            $specificIds[] = $disputeId;
            $customValuesArray[] = $customValues;
        }

        $disputes = $this->firestoreService->createBatchDisputes($eventId, count($specificIds), $customValuesArray, $specificIds);

        return [...$disputes, ...$reports];
    }

    public function showBrackets(Request $request, $eventId)
    {
        $event = EventDetail::with(['tier'])
            ->where('id', $eventId)
            ->firstOrFail()
            ->toArray();
        if (!$event['tier']) {
            return $this->showErrorParticipant('Event Tier has not been chosen for this event!');
        }

        $teams = Team::join('join_events', 'teams.id', '=', 'join_events.team_id')
            ->where('join_events.join_status', 'confirmed')
            ->where('join_events.event_details_id', $eventId)
            ->select(['teams.id', 'teams.teamName', 'teams.teamBanner'])
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item];
            })
            ->toArray();

        $ogBrackets = DB::table('brackets_setup')
            ->leftJoin('brackets', function ($join) use ($eventId) {
                $join->on('brackets.team1_position', '=', 'brackets_setup.team1_position')->on('brackets.team2_position', '=', 'brackets_setup.team2_position')->where('brackets.event_details_id', '=', $eventId);
            })
            ->where('brackets_setup.event_tier_id', '=', $event['tier']['id'])
            ->leftJoin('teams as team1', function ($join) {
                $join->on('brackets.team1_id', '=', 'team1.id')->whereNotNull('brackets.team1_id');
            })
            ->leftJoin('teams as team2', function ($join) {
                $join->on('brackets.team2_id', '=', 'team2.id')->whereNotNull('brackets.team2_id');
            })
            ->select('brackets.id', 'brackets.team1_id', 'brackets.team2_id', DB::raw("COALESCE(brackets.event_details_id, $eventId) as event_details_id"), 'brackets_setup.stage_name', 'brackets_setup.inner_stage_name', 'brackets_setup.order', 'brackets_setup.team2_position', 'brackets_setup.team1_position', 'team1.teamName as team1Name', 'team2.teamName as team2Name', 'team1.teamBanner as team1_banner', 'team2.teamBanner as team2_banner')
            ->get();

        $brackets = $this->firestoreService->generateBrackets($ogBrackets, $event['id']);
        return view('filament.pages.brackets', compact('brackets', 'event', 'teams'));
    }

    public function showDisputes(Request $request, $eventId)
    {
        $event = EventDetail::with(['tier'])
            ->where('id', $eventId)
            ->first()
            ->toArray();
        if (!$event['tier']) {
            return $this->showErrorParticipant('Event Tier has not been chosen for this event!');
        }

        $disputes = $this->firestoreService->generateDisputes($event['id']);
        $teams = Team::join('join_events', 'teams.id', '=', 'join_events.team_id')
            ->where('join_events.join_status', 'confirmed')
            ->where('join_events.event_details_id', $eventId)
            ->select(['teams.id', 'teams.teamName', 'teams.teamBanner'])
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item];
            })
            ->toArray();

        $setup = DB::table('brackets_setup')
            ->select(['team1_position', 'team2_position'])
            ->where('brackets_setup.event_tier_id', '=', $event['tier']['id'])
            ->get()
            ->filter(function ($item) {
                return $item->team1_position != 'F';
            })
            ->map(function ($item) {
                return $item->team1_position . '.' . $item->team2_position;
            })
            ->toArray();

        $users = Team::join('join_events', 'teams.id', '=', 'join_events.team_id')
            ->leftJoin('roster_members', 'join_events.id', '=', 'roster_members.join_events_id')
            ->leftJoin('users', 'roster_members.user_id', '=', 'users.id')
            ->where('join_events.join_status', 'confirmed')
            ->where('join_events.event_details_id', $eventId)
            ->select(['teams.id as team_id', 'teams.teamName', 'teams.teamBanner', 'users.id as user_id', 'users.name', 'users.userBanner'])
            ->get()
            ->toArray();

        $DISPUTTE_ENUMS = config('constants.DISPUTE');
        $disputeRoles = array_flip($DISPUTTE_ENUMS);

        // dd($users);
        return view('filament.pages.reports', compact('disputes', 'event', 'teams', 'users', 'disputeRoles', 'setup'));
    }
}
