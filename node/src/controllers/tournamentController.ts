import { Request, Response } from 'express';
import * as admin from 'firebase-admin';
import { DeadlineService } from '../services/deadlineService';
import { Logger } from '../utils/logger';

interface RoomBlockRequest {
  user1?: string | number;
  user2?: string | number;
  action?: string;
  blocked_by?: string | number;
}

interface BatchReportsRequest {
  event_id?: string | number;
  count?: number;
  custom_values_array?: any[];
  specific_ids?: string[];
  games_per_match?: number;
}

interface BatchDisputesRequest {
  event_id?: string | number;
  count?: number;
  custom_values_array?: any[];
  specific_ids?: string[];
}

interface DeadlineTasksRequest {
  detail_id?: string | number;
  matches?: any[];
  bracket_info?: any;
  tier_id?: string | number;
  is_league?: boolean;
  games_per_match?: number;
}

interface MatchResultRequest {
  event_id: string | number;
  match_id: string;
}

interface AllMatchResultsRequest {
  event_id: string | number;
}

/**
 * Handle room blocking/unblocking operations
 * POST /room/block
 */
export async function handleRoomBlock(req: Request, res: Response): Promise<void> {
  try {
    const roomRequest: RoomBlockRequest = req.body;

    if (!roomRequest.action || !['block', 'unblock'].includes(roomRequest.action)) {
      res.status(400).json({ detail: 'action must be "block" or "unblock"' });
      return;
    }

    if (roomRequest.action === 'block' && !roomRequest.blocked_by) {
      res.status(400).json({ detail: 'blocked_by is required for block action' });
      return;
    }

    const db = admin.firestore();
    const roomCollection = db.collection('room');

    const user1 = String(roomRequest.user1);
    const user2 = String(roomRequest.user2);

    // Query both possible room combinations
    const query1 = roomCollection.where('user1', '==', user1).where('user2', '==', user2);
    const query2 = roomCollection.where('user2', '==', user1).where('user1', '==', user2);

    let roomsUpdated = 0;

    // Update rooms from first query
    const snapshot1 = await query1.get();
    for (const doc of snapshot1.docs) {
      if (roomRequest.action === 'block') {
        await doc.ref.update({ blocked_by: String(roomRequest.blocked_by) });
      } else {
        await doc.ref.update({ blocked_by: null });
      }
      roomsUpdated++;
    }

    // Update rooms from second query
    const snapshot2 = await query2.get();
    for (const doc of snapshot2.docs) {
      if (roomRequest.action === 'block') {
        await doc.ref.update({ blocked_by: String(roomRequest.blocked_by) });
      } else {
        await doc.ref.update({ blocked_by: null });
      }
      roomsUpdated++;
    }

    res.status(200).json({
      success: true,
      action: roomRequest.action,
      rooms_updated: roomsUpdated,
      message: `Successfully ${roomRequest.action}ed ${roomsUpdated} room(s)`,
    });
  } catch (error) {
    Logger.error('Failed to handle room block', error);
    const action = (req.body as RoomBlockRequest)?.action || 'unknown';
    res.status(500).json({ detail: `Failed to ${action} room` });
  }
}

/**
 * Create batch reports
 * POST /batch/reports
 */
export async function createBatchReports(req: Request, res: Response): Promise<void> {
  try {
    const batchRequest: BatchReportsRequest = req.body;
    const result = await executeBatchReports(
      batchRequest.event_id!,
      batchRequest.count!,
      batchRequest.custom_values_array,
      batchRequest.specific_ids,
      batchRequest.games_per_match || 3
    );
    res.status(200).json(result);
  } catch (error) {
    Logger.error('Failed to create batch reports', error);
    res.status(500).json({ detail: 'Failed to create batch reports' });
  }
}

/**
 * Create batch disputes
 * POST /batch/disputes
 */
export async function createBatchDisputes(req: Request, res: Response): Promise<void> {
  try {
    const batchRequest: BatchDisputesRequest = req.body;
    const result = await executeBatchDisputes(
      batchRequest.event_id!,
      batchRequest.count!,
      batchRequest.custom_values_array,
      batchRequest.specific_ids
    );
    res.status(200).json(result);
  } catch (error) {
    Logger.error('Failed to create batch disputes', error);
    res.status(500).json({ detail: 'Failed to create batch disputes' });
  }
}

/**
 * Handle started tournament tasks
 * POST /deadline/started
 */
export async function handleStartedTasks(req: Request, res: Response): Promise<void> {
  try {
    const tasksRequest: DeadlineTasksRequest = req.body;
    const trait = new DeadlineService();
    const eventData = await trait.fetchAllEventData(tasksRequest.detail_id!);
    const results: any[] = [];

    // Handle case where there are no brackets or disputes to process
    if (eventData.brackets_count === 0 && eventData.disputes_count === 0) {
      res.status(200).json({
        status: 'success',
        message: 'No data to process - event has 0 brackets and 0 disputes',
        results: [],
      });
      return;
    }

    // Handle case where there are no matches to process
    if (!tasksRequest.matches || tasksRequest.matches.length === 0) {
      res.status(200).json({
        status: 'success',
        message: 'No matches to process - empty matches array',
        results: [],
      });
      return;
    }

    const db = admin.firestore();
    const gamesPerMatch = tasksRequest.games_per_match || 3;

    for (const match of tasksRequest.matches) {
      const matchStatusPath = `${match.team1_position}.${match.team2_position}`;

      // Check if bracket exists in our hashmap
      if (trait.getBracket(matchStatusPath)) {
        const docRef = db
          .collection('event')
          .doc(String(match.event_details_id))
          .collection('brackets')
          .doc(matchStatusPath);

        const startedStatusArray = ['ONGOING', ...Array(gamesPerMatch - 1).fill('UPCOMING')];
        await docRef.update({
          matchStatus: startedStatusArray,
          completeMatchStatus: 'ONGOING',
        });

        results.push({
          match_id: matchStatusPath,
          status: 'success',
          message: 'Match status updated to started',
        });
      }
    }

    res.status(200).json({
      status: 'success',
      message: `Processed ${results.length} started matches`,
      results: results,
    });
  } catch (error) {
    Logger.error('Failed to handle started tasks', error);
    res.status(500).json({ detail: 'Failed to handle started tasks' });
  }
}

/**
 * Handle ended tournament tasks
 * POST /deadline/ended
 */
export async function handleEndedTasks(req: Request, res: Response): Promise<void> {
  try {
    const tasksRequest: DeadlineTasksRequest = req.body;
    const trait = new DeadlineService();
    const eventData = await trait.fetchAllEventData(tasksRequest.detail_id!);
    const results: any[] = [];
    const nextStageDataList: any[] = [];

    // Handle case where there are no brackets or disputes to process
    if (eventData.brackets_count === 0 && eventData.disputes_count === 0) {
      res.status(200).json({
        status: 'success',
        message: 'No data to process - event has 0 brackets and 0 disputes',
        results: [],
        next_stage_data: [],
      });
      return;
    }

    // Handle case where there are no matches to process
    if (!tasksRequest.matches || tasksRequest.matches.length === 0) {
      res.status(200).json({
        status: 'success',
        message: 'No matches to process - empty matches array',
        results: [],
        next_stage_data: [],
      });
      return;
    }

    const db = admin.firestore();
    const gamesPerMatch = tasksRequest.games_per_match || 3;

    for (const match of tasksRequest.matches) {
      const extraBracket =
        tasksRequest.bracket_info?.[match.stage_name]?.[match.inner_stage_name]?.[match.order] || {};
      const endedStatusArray = Array(gamesPerMatch).fill('ENDED');

      const matchStatusPath = `${match.team1_position}.${match.team2_position}`;

      // Use hashmap lookup instead of individual Firestore query
      const matchStatusData = trait.getBracket(matchStatusPath);
      if (matchStatusData) {
        const initialUpdateValues = {
          matchStatus: endedStatusArray,
          completeMatchStatus: 'ENDED',
        };

        const result = trait.interpretDeadlines(
          matchStatusData,
          initialUpdateValues,
          match,
          extraBracket,
          tasksRequest.tier_id!,
          false,
          tasksRequest.is_league || false,
          gamesPerMatch
        );

        // Update Firestore document
        if (Object.keys(result.update_values).length > 0) {
          const docRef = db
            .collection('event')
            .doc(String(match.event_details_id))
            .collection('brackets')
            .doc(matchStatusPath);
          await docRef.update(result.update_values);
        }

        // Update dispute documents
        for (let i = 0; i < result.dispute_ref_list.length; i++) {
          const disputeRef = result.dispute_ref_list[i];
          const disputeUpdate = result.update_dispute_values[i];
          if (disputeRef && disputeUpdate) {
            await disputeRef.update(disputeUpdate);
          }
        }

        // Collect next stage data for PHP to process
        if (result.next_stage_data) {
          nextStageDataList.push(result.next_stage_data);
        }

        results.push({
          match_id: matchStatusPath,
          status: 'success',
          message: 'Match ended and processed',
        });
      }
    }

    res.status(200).json({
      status: 'success',
      message: `Processed ${results.length} ended matches`,
      results: results,
      next_stage_data: nextStageDataList,
    });
  } catch (error) {
    Logger.error('Failed to handle ended tasks', error);
    res.status(500).json({ detail: 'Failed to handle ended tasks' });
  }
}

/**
 * Handle organizer deadline tasks
 * POST /deadline/org
 */
export async function handleOrgTasks(req: Request, res: Response): Promise<void> {
  try {
    const tasksRequest: DeadlineTasksRequest = req.body;
    const trait = new DeadlineService();
    const eventData = await trait.fetchAllEventData(tasksRequest.detail_id!);
    const results: any[] = [];
    const nextStageDataList: any[] = [];

    // Handle case where there are no brackets or disputes to process
    if (eventData.brackets_count === 0 && eventData.disputes_count === 0) {
      res.status(200).json({
        status: 'success',
        message: 'No data to process - event has 0 brackets and 0 disputes',
        results: [],
        next_stage_data: [],
      });
      return;
    }

    // Handle case where there are no matches to process
    if (!tasksRequest.matches || tasksRequest.matches.length === 0) {
      res.status(200).json({
        status: 'success',
        message: 'No matches to process - empty matches array',
        results: [],
        next_stage_data: [],
      });
      return;
    }

    const db = admin.firestore();
    const gamesPerMatch = tasksRequest.games_per_match || 3;

    for (const match of tasksRequest.matches) {
      const extraBracket =
        tasksRequest.bracket_info?.[match.stage_name]?.[match.inner_stage_name]?.[match.order] || {};

      const matchStatusPath = `${match.team1_position}.${match.team2_position}`;

      // Use hashmap lookup instead of individual Firestore query
      const matchStatusData = trait.getBracket(matchStatusPath);
      if (matchStatusData) {
        const result = trait.interpretDeadlines(
          matchStatusData,
          {},
          match,
          extraBracket,
          tasksRequest.tier_id!,
          true,
          tasksRequest.is_league || false,
          gamesPerMatch
        );

        // Update Firestore document
        if (Object.keys(result.update_values).length > 0) {
          const docRef = db
            .collection('event')
            .doc(String(match.event_details_id))
            .collection('brackets')
            .doc(matchStatusPath);
          await docRef.update(result.update_values);
        }

        // Update dispute documents
        for (let i = 0; i < result.dispute_ref_list.length; i++) {
          const disputeRef = result.dispute_ref_list[i];
          const disputeUpdate = result.update_dispute_values[i];
          if (disputeRef && disputeUpdate) {
            await disputeRef.update(disputeUpdate);
          }
        }

        // Collect next stage data for PHP to process
        if (result.next_stage_data) {
          nextStageDataList.push(result.next_stage_data);
        }

        results.push({
          match_id: matchStatusPath,
          status: 'success',
          message: 'Organizer deadline processed',
        });
      }
    }

    res.status(200).json({
      status: 'success',
      message: `Processed ${results.length} organizer tasks`,
      results: results,
      next_stage_data: nextStageDataList,
    });
  } catch (error) {
    Logger.error('Failed to handle organizer tasks', error);
    res.status(500).json({ detail: 'Failed to handle organizer tasks' });
  }
}

/**
 * Get a single match result from Firestore
 * POST /match/result
 */
export async function getMatchResult(req: Request, res: Response): Promise<void> {
  try {
    const matchRequest: MatchResultRequest = req.body;
    const eventId = String(matchRequest.event_id);
    const matchId = matchRequest.match_id;
    const db = admin.firestore();

    // Get the match document
    const matchDocRef = db.collection('event').doc(eventId).collection('brackets').doc(matchId);
    const matchDoc = await matchDocRef.get();

    if (!matchDoc.exists) {
      res.status(200).json({
        status: 'not_found',
        message: `Match ${matchId} not found for event ${eventId}`,
        data: null,
      });
      return;
    }

    const matchData = matchDoc.data();

    Logger.log(`Successfully fetched match result for event ${eventId}, match ${matchId}`);

    res.status(200).json({
      status: 'success',
      message: `Successfully fetched match result for match ${matchId}`,
      data: matchData,
    });
  } catch (error) {
    Logger.error('Failed to get match result', error);
    res.status(500).json({ detail: 'Failed to get match result' });
  }
}

/**
 * Get all match results for an event from Firestore
 * POST /match/results/all
 */
export async function getAllMatchResults(req: Request, res: Response): Promise<void> {
  try {
    const resultsRequest: AllMatchResultsRequest = req.body;
    const eventId = String(resultsRequest.event_id);
    const db = admin.firestore();

    // Fetch all brackets for this event
    const bracketsCollection = db.collection('event').doc(eventId).collection('brackets');
    const allMatchResults: { [key: string]: any } = {};

    const snapshot = await bracketsCollection.get();
    snapshot.forEach((doc) => {
      allMatchResults[doc.id] = doc.data();
    });

    Logger.log(`Successfully fetched all match results for event ${eventId}. Total matches: ${Object.keys(allMatchResults).length}`);

    res.status(200).json({
      status: 'success',
      message: `Successfully fetched all match results for event ${eventId}`,
      total_matches: Object.keys(allMatchResults).length,
      data: allMatchResults,
    });
  } catch (error) {
    Logger.error('Failed to get all match results', error);
    res.status(500).json({ detail: 'Failed to get all match results' });
  }
}

/**
 * Health check endpoint
 * GET /health
 */
export async function handleTournamentHealthCheck(req: Request, res: Response): Promise<void> {
  try {
    // Verify Firebase is initialized and accessible
    if (admin.apps.length === 0) {
      res.status(503).json({
        status: 'unhealthy',
        service: 'driftwood-api',
        error: 'Firebase not initialized',
        timestamp: new Date().toISOString(),
      });
      return;
    }

    // Try to access Firestore client to ensure it's responsive
    const db = admin.firestore();

    res.status(200).json({
      status: 'healthy',
      service: 'driftwood-api',
      firebase_initialized: true,
      timestamp: new Date().toISOString(),
    });
  } catch (error) {
    Logger.error('Health check failed', error);
    res.status(503).json({
      status: 'unhealthy',
      service: 'driftwood-api',
      error: String(error),
      timestamp: new Date().toISOString(),
    });
  }
}

// Helper functions

async function executeBatchReports(
  eventId: string | number,
  count: number,
  customValuesArray: any[] = [],
  specificIds: string[] = [],
  gamesPerMatch: number = 3
): Promise<any> {
  const results: any[] = [];

  try {
    const db = admin.firestore();
    const batch = db.batch();

    for (let i = 0; i < count; i++) {
      const reportId = specificIds[i];
      const customValues = i < customValuesArray.length ? customValuesArray[i] : {};

      const defaultReport = {
        completeMatchStatus: 'UPCOMING',
        defaultWinners: Array(gamesPerMatch).fill(null),
        disputeResolved: Array(gamesPerMatch).fill(null),
        disqualified: false,
        matchStatus: Array(gamesPerMatch).fill('UPCOMING'),
        organizerWinners: Array(gamesPerMatch).fill(null),
        position: null,
        randomWinners: Array(gamesPerMatch).fill(null),
        realWinners: Array(gamesPerMatch).fill(null),
        score: [0, 0],
        team1Id: null,
        team1Winners: Array(gamesPerMatch).fill(null),
        team2Id: null,
        team2Winners: Array(gamesPerMatch).fill(null),
      };

      // Merge custom values with defaults
      const reportData = { ...defaultReport, ...customValues };

      const docRef = db.collection('event').doc(String(eventId)).collection('brackets').doc(reportId);
      batch.set(docRef, reportData);

      results.push({
        statusReport: 'pending',
        reportId: reportId,
      });
    }

    // Commit the batch
    await batch.commit();

    // Update results to success after commit
    for (const result of results) {
      result.statusReport = 'success';
      result.messageReport = 'Report created or overwritten successfully';
    }

    return {
      statusReport: 'success',
      messageReport: 'Batch operation completed - all reports created or overwritten',
      resultsReport: results,
    };
  } catch (error) {
    Logger.error('Error in executeBatchReports', error);
    return {
      statusReport: 'error',
      messageReport: String(error),
      resultsReport: results,
    };
  }
}

async function executeBatchDisputes(
  eventId: string | number,
  count: number,
  customValuesArray: any[] = [],
  specificIds: string[] = []
): Promise<any> {
  const results: any[] = [];

  try {
    const db = admin.firestore();

    for (let i = 0; i < count; i++) {
      const disputeId = specificIds[i];
      const customValues = i < customValuesArray.length ? customValuesArray[i] : {};

      const defaultDispute = {
        created_at: admin.firestore.FieldValue.serverTimestamp(),
        dispute_description: null,
        dispute_image_videos: [],
        dispute_reason: null,
        dispute_teamId: null,
        dispute_teamNumber: null,
        dispute_userId: null,
        event_id: String(eventId),
        match_number: null,
        report_id: null,
        resolution_resolved_by: null,
        resolution_winner: null,
        response_explanation: null,
        response_teamId: null,
        response_teamNumber: null,
        response_userId: null,
        updated_at: admin.firestore.FieldValue.serverTimestamp(),
      };

      const disputeData = { ...defaultDispute, ...customValues };

      const docRef = db.collection('event').doc(String(eventId)).collection('disputes').doc(disputeId);
      await docRef.set(disputeData);

      results.push({
        statusDispute: 'success',
        disputeId: disputeId,
        messageDispute: 'Dispute created or overwritten successfully',
      });
    }

    return {
      statusDispute: 'success',
      messageDispute: 'Individual operation completed - all disputes created or overwritten',
      resultsDispute: results,
    };
  } catch (error) {
    Logger.error('Error in executeBatchDisputes', error);
    return {
      statusDispute: 'error',
      messageDispute: String(error),
      resultsDispute: results,
    };
  }
}
