<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\CloudFunctionAuthService;

class FirebaseController extends Controller
{
    protected $authService;
    
    public function __construct(CloudFunctionAuthService $authService)
    {
        $this->authService = $authService;
    }
   


    private function updateRoomBlockStatus($user1Id, $user2Id, $action, $blockedBy = null)
    {
        try {
            Log::info("Starting room block status update with cached identity token");
    
            $cloudFunctionUrl = config('services.cloud_server_functions.url');
            
            // Use server-side cache for identity token (NOT session)
            $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);
            
            Log::info("Making request to Cloud Run service");
            
            $response = Http::timeout(30)
                ->contentType('application/json')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $identityToken,
                    'User-Agent' => 'Laravel-App/1.0'
                ])
                ->post($cloudFunctionUrl . '/room/block', [
                    'user1' => $user1Id,
                    'user2' => $user2Id,
                    'action' => $action,
                    'blocked_by' => $blockedBy
                ]);
    
            if (!$response->successful()) {
                // Clear cache on authentication errors
                if ($response->status() === 401 || $response->status() === 403) {
                    Log::warning("Authentication failed, clearing token cache");
                    $this->authService->clearIdentityTokenCache($cloudFunctionUrl);
                    
                    // Retry once with fresh token
                    $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);
                    $response = Http::timeout(30)
                        ->contentType('application/json')
                        ->withHeaders(['Authorization' => 'Bearer ' . $identityToken])
                        ->post($cloudFunctionUrl . '/room/block', [
                            'user1' => $user1Id,
                            'user2' => $user2Id,
                            'action' => $action,
                            'blocked_by' => $blockedBy
                        ]);
                }
    
                if (!$response->successful()) {
                    Log::error('Cloud Run request failed', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    throw new Exception('Cloud Run request failed: ' . $response->body());
                }
            }
            
            $responseData = $response->json();
            if (!isset($responseData['success']) || !$responseData['success']) {
                throw new Exception('Cloud Run returned error: ' . ($responseData['message'] ?? 'Unknown error'));
            }
            
            Log::info("Room block status updated successfully", $responseData);
            return $responseData;
            
        } catch (Exception $e) {
            Log::error('Failed to update room block status', [
                'error' => $e->getMessage(),
                'user1' => $user1Id,
                'user2' => $user2Id,
                'action' => $action
            ]);
            throw new Exception('Failed to update room block status: ' . $e->getMessage());
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
                $authenticatedUser->save();
                $this->updateRoomBlockStatus($user->id, $authenticatedUser->id, 'unblock');
                
                return response()->json([
                    'message' => 'User unblocked successfully',
                    'is_blocked' => false,
                ]);
            } else {
                $authenticatedUser->blocks()->attach($user);
                $authenticatedUser->save();

                    $this->updateRoomBlockStatus($user->id, $authenticatedUser->id, 'block', $authenticatedUser->id);
                
                return response()->json([
                    'message' => 'User blocked successfully',
                    'is_blocked' => true,
                ]);
            }
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
        $originalDocumentSpecs = null;

        if (!$isLeague) {
            $originalDocumentSpecs = [
                'W1.W2' => [
                    'team1Winners' => ['0', null, null], // DEFAULT WINNERS
                    'stageName' => 'U'
                ],
                'W3.W4' => [
                    'team1Winners' => ['0', '1', null], // DEFAULT WINNERS
                    'stageName' => 'U'
                ],
                'W5.W6' => [
                    'team1Winners' => ['0', '1', '1'],
                    'stageName' => 'U'
                ],
                'W7.W8' => [
                    'team1Winners' => ['1', '1', '1'], // RANDOM WINNERS
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'U'
                ],
                'W9.W10' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'U'
                ],
                'W11.W12' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'U'
                ],
                'W13.W14' => [
                    'team1Winners' => [null, null, null],
                    'team2Winners' => [null, null, null],
                    'stageName' => 'U'
                ],
                'L3.L4' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'L'
                ],
                'L5.L6' => [
                    'team1Winners' => ['0', '1', '1'],
                    'stageName' => 'L'
                ],
                'L7.L8' => [
                    'team1Winners' => ['1', '1', '1'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'L'
                ],
                'L9.L10' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'L'
                ],
            ];
        } else {
            $originalDocumentSpecs = [
                'P1.P2' => [
                    'team1Winners' => ['0', null, null], // DEFAULT WINNERS
                    'stageName' => 'R1'
                ],
                'P3.P4' => [
                    'team1Winners' => ['0', '1', null], // DEFAULT WINNERS
                    'stageName' => 'R1'
                ],
                'P5.P6' => [
                    'team1Winners' => ['0', '1', '1'],
                    'stageName' => 'R1'
                ],
                'P7.P8' => [
                    'team1Winners' => ['1', '1', '1'], // RANDOM WINNERS
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'R1'
                ],
                'P9.P10' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'R1'
                ],
                'P11.P12' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'R1'
                ],
                'P13.P14' => [
                    'team1Winners' => [null, null, null],
                    'team2Winners' => [null, null, null],
                    'stageName' => 'R1'
                ],
                'P15.P16' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'R1'
                ],
                'P17.P18' => [
                    'team1Winners' => ['0', '1', '1'],
                    'stageName' => 'R1'
                ],
                'P19.P20' => [
                    'team1Winners' => ['1', '1', '1'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'R1'
                ],
                'P21.P22' => [
                    'team1Winners' => ['0', '1', '0'],
                    'team2Winners' => ['1', '0', '1'],
                    'stageName' => 'R1'
                ],
            ];
        }

        $documentSpecs = [];

        foreach ($originalDocumentSpecs as $documentId => $specs) {
            $newDocumentId = $documentId;
            
            
            $newSpecs = [];
            foreach ($specs as $key => $winners) {
                $newSpecs[$key] = is_array($winners) ? array_slice($winners, 0, $gamesPerMatch) : $winners;
            }
            
            $documentSpecs[$newDocumentId] = $newSpecs;
        }

        $customValuesArray = [];
        $specificIds = [];

        foreach ($documentSpecs as $documentId => $customValues) {
            $specificIds[] = $documentId;
            $customValuesArray[] = $customValues;
        }


        $reports = $this->callCloudFunctionBatchReports($eventId, count($specificIds), $customValuesArray, $specificIds, $gamesPerMatch);
        
        $customDisputeValuesArray = [];
        $specificIds = [];

        $originalDisputeSpecs = null;
        if ($isLeague) {
            $originalDisputeSpecs = [
                'P1.P2.0' => [
                    'dispute_image_videos' => ['media/img/dispute_evidence_3.jpg'],
                    'dispute_reason' => 'There is suspected compromises to match integrity (e.g. match-fixing).',
                    'dispute_teamId' => '24',
                    'dispute_teamNumber' => '0',
                    'dispute_userId' => '86',
                    'match_number' => '0',
                    'report_id' => 'P1.P2',
                ],
                'P1.P2.1' => [
                    'dispute_description' => 'Team 24 is disputing the result of match 1',
                    'dispute_reason' => 'Disputed game result due to server lag/disconnection.',
                    'dispute_teamId' => '24',
                    'dispute_teamNumber' => '0',
                    'dispute_userId' => '86',
                    'match_number' => '1',
                    'report_id' => 'P1.P2',
                ],
                'P1.P2.2' => [
                    'dispute_description' => 'Both teams using restricted characters',
                    'dispute_image_videos' => ['media/img/dispute_evidence_1.jpg', 'media/img/dispute_evidence_2.jpg'],
                    'dispute_reason' => 'Disputed game result due to use of prohibited characters/hero/content.',
                    'dispute_teamId' => '24',
                    'dispute_teamNumber' => '0',
                    'dispute_userId' => '86',
                    'match_number' => '2',
                    'report_id' => 'P1.P2',
                ],
            ];
        } else {
            $originalDisputeSpecs = [
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
        }

        foreach ($originalDisputeSpecs as $disputeId => $customValues) {
            $specificIds[] = $disputeId;
            $customDisputeValuesArray[] = $customValues;
        }

        $disputes = $this->callCloudFunctionBatchDisputes($eventId, count($specificIds), $customDisputeValuesArray, $specificIds);

        return [...$disputes, ...$reports];
    }

    /**
     * Call Cloud Function to handle batch reports creation
     */
    private function callCloudFunctionBatchReports($eventId, $count, $customValuesArray, $specificIds, $gamesPerMatch)
    {
        try {
            $cloudFunctionUrl = config('services.cloud_server_functions.url');
            $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);
            
            $response = Http::contentType('application/json')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $identityToken
                ])
                ->post($cloudFunctionUrl . '/batch/reports', [
                    'event_id' => $eventId,
                    'count' => $count,
                    'custom_values_array' => $customValuesArray,
                    'specific_ids' => $specificIds,
                    'games_per_match' => $gamesPerMatch
                ]);

            if (!$response->successful()) {
                throw new Exception('Cloud function call failed: ' . $response->body());
            }
            
            $responseData = $response->json();
            if (!isset($responseData['statusReport']) || $responseData['statusReport'] !== 'success') {
                throw new Exception('Cloud function returned error: ' . ($responseData['messageReport'] ?? 'Unknown error'));
            }
            
            return $responseData;
        } catch (Exception $e) {
            throw new Exception('Failed to create batch reports: ' . $e->getMessage());
        }
    }

    /**
     * Call Cloud Function to handle batch disputes creation
     */
    private function callCloudFunctionBatchDisputes($eventId, $count, $customValuesArray, $specificIds)
    {
        try {
            $cloudFunctionUrl = config('services.cloud_server_functions.url');
            $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);

            $cloudFunctionUrl = config('services.cloud_server_functions.url');
            $response = Http::contentType('application/json')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $identityToken
                ])
                ->post($cloudFunctionUrl . '/batch/disputes', [
                    'event_id' => $eventId,
                    'count' => $count,
                    'custom_values_array' => $customValuesArray,
                    'specific_ids' => $specificIds
                ]);

            if (!$response->successful()) {
                throw new Exception('Cloud function call failed: ' . $response->body());
            }
            
            $responseData = $response->json();
            if (!isset($responseData['statusDispute']) || $responseData['statusDispute'] !== 'success') {
                throw new Exception('Cloud function returned error: ' . ($responseData['messageDispute'] ?? 'Unknown error'));
            }
            
            return $responseData;
        } catch (Exception $e) {
            throw new Exception('Failed to create batch disputes: ' . $e->getMessage());
        }
    }

}
