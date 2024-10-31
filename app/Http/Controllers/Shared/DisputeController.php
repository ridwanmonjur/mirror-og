<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DisputeCreateRequest;
use App\Http\Requests\DisputeResolveRequest;
use App\Http\Requests\DisputeResponseRequest;
use App\Http\Requests\DisputeRetrieveRequest;
use App\Models\Dispute;
use App\Models\ImageVideoDispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DisputeController extends Controller
{
  
    /**
     * Handle all dispute actions through a single endpoint
     */
    public function handleDisputes(Request $request): JsonResponse
    {
        $action = $request->input('action');
        $userId = $request->attributes->get('user')->id;
        $request->merge(['userId' => $userId]);

        return match($action) {
            'retrieve' => $this->retrieveDispute(new DisputeRetrieveRequest($request->all())),
            'create' => $this->createDispute(new DisputeCreateRequest($request->all())),
            'respond' => $this->respondToDispute(new DisputeResponseRequest($request->all())),
            'resolve' => $this->resolveDispute(new DisputeResolveRequest($request->all())),
            'delete' => $this->deleteDispute($request->disputeId),
            default => response()->json([
                'success' => false,
                'status' => 'error', 
                'message' => 'Invalid action'
            ], 400)
        };
    }

    public function retrieveDispute(DisputeRetrieveRequest $request) {
        $report_id = $request->report_id;
        $disputesByKey = Dispute::where('report_id', $report_id)
            ->where('event_id', $request->event_id)
            ->orderBy('match_number')
            ->get()
            ->keyBy('match_number');

        $mapByKey = [null, null, null];
        $disputeIdList = [null, null, null];
        $resolvedWinner = [null, null, null];
        $resolverList = [null, null, null];
        for ($i = 0; $i < 3; $i++) {
            if (isset($disputesByKey[$i])) {                
                $mapByKey[$i] = $disputesByKey[$i];
                $disputeIdList[$i] = $disputesByKey[$i]->id;
                $resolvedWinner[$i] = $disputesByKey[$i]->resolution_winner;
                $resolverList[$i] = $disputesByKey[$i]->resolution_resolved_by;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'dispute' => $mapByKey,
                'resolvedWinner' => $resolvedWinner,
                'resolverList' => $resolverList
            ]
        ]);
    }

    /**
     * Create a new dispute
     */
    private function createDispute(DisputeCreateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $dispute = Dispute::create([
                'match_number' => $request->match_number,
                'report_id' => $request->report_id,
                'dispute_userId' => $request->userId,
                'dispute_teamId' => $request->dispute_teamId,
                'dispute_teamNumber' => $request->dispute_teamNumber,
                'dispute_reason' => $request->dispute_reason,
                'dispute_description' => $request->dispute_description,
                'event_id' => $request->event_id
            ]);

            // ImageVideoDispute::handleMediaUploads($request, $dispute, 'dispute');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dispute created successfully',
                'data' => $dispute
                // 'dispute' => $dispute->load('disputeMedia')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleErrorJson($e);
        }
    }

    /**
     * Respond to a dispute
     */
    private function respondToDispute(DisputeResponseRequest $request): JsonResponse
    {
        try {
            $dispute = $request->getDispute();

            DB::beginTransaction();

            $dispute->update([
                'response_userId' => $request->userId,
                'response_teamId' => $request->response_teamId,
                'response_teamNumber' => $request->response_teamNumber,
                'response_explanation' => $request->response_explanation
            ]);

            // ImageVideoDispute::handleMediaUploads($request, $dispute, 'response');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Response submitted successfully',
                'dispute' => $dispute->load(['disputeMedia', 'responseMedia'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleErrorJson($e);
        }
    }

    /**
     * Resolve a dispute
     */
    private function resolveDispute(DisputeResolveRequest $request): JsonResponse
    {
        try {
            $dispute = $request->getDispute();

            $dispute->update([
                'resolution_winner' => $request->resolution_winner,
                'resolution_resolved_by' => $request->resolution_resolved_by
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dispute resolved successfully',
                'dispute' => $dispute->load(['disputeMedia', 'responseMedia'])
            ]);

        } catch (\Exception $e) {
            return $this->handleErrorJson($e);
        }
    }

    private function deleteDispute($disputeId): JsonResponse
    {
        try {
            Dispute::where('id', $disputeId)->delete($disputeId);

            return response()->json([
                'success' => true,
                'message' => 'Dispute deleted successfully',
            ]);

        } catch (\Exception $e) {
            return $this->handleErrorJson($e);
        }
    }
}
