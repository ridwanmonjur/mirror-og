<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Exception;
use Illuminate\Support\Facades\DB;
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

                if (! $user->hasBlocked($authenticatedUser)) {
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
        $event = EventDetail::with(['game', 'type'])->findOrFail($eventId);
        $gamesPerMatch = $event->game->games_per_match;
        $isLeague = $event?->type?->eventType === 'League';
        
        $originalDocumentSpecs = [
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

        $documentSpecs = [];
        $indexCounter = 1; 
        
        foreach ($originalDocumentSpecs as $documentId => $specs) {
            $newDocumentId = $documentId;
            
            if ($isLeague) {
                preg_match_all('/\d+/', $documentId, $matches);
                if (count($matches[0]) >= 2) {
                    $newDocumentId = "P{$indexCounter}.P" . ($indexCounter + 1);
                    $indexCounter += 2;
                }
            }
            
            $newSpecs = [];
            foreach ($specs as $key => $winners) {
                $newSpecs[$key] = array_slice($winners, 0, $gamesPerMatch);
            }
            
            $documentSpecs[$newDocumentId] = $newSpecs;
        }

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

}
