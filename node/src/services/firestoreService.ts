import {
  getFirestore,
  getServerTimestamp,
  getBracketCollection,
  getDisputeCollection,
} from '../config/firebase';
import { Logger } from '../utils/logger';
import { BracketReport, Dispute } from '../models/types';

/**
 * Firestore Service
 *
 * Handles all Firestore write operations for brackets and disputes
 * Replicates operations from BracketData.js
 */
export class FirestoreService {
  /**
   * Update bracket report
   *
   * Replicates writeReportDB() from BracketData.js
   * Document ID format: {team1Position}.{team2Position} (e.g., "W1.W2")
   */
  async updateBracketReport(
    eventId: string,
    team1Position: string,
    team2Position: string,
    data: BracketReport
  ): Promise<void> {
    try {
      const db = getFirestore();
      const docId = `${team1Position}.${team2Position}`;
      const docRef = db.doc(`event/${eventId}/brackets/${docId}`);

      // Prepare document data (matches structure from BracketData.js line 364-380)
      const firestoreDoc = {
        score: data.score || [0, 0],
        stageName: data.stageName,
        realWinners: data.realWinners,
        organizerWinners: data.organizerWinners,
        team1Id: data.team1Id,
        team2Id: data.team2Id,
        position: data.position,
        completeMatchStatus: data.completeMatchStatus,
        randomWinners: data.randomWinners,
        defaultWinners: data.defaultWinners,
        disqualified: data.disqualified,
        disputeResolved: data.disputeResolved,
        team1Winners: data.team1Winners,
        team2Winners: data.team2Winners,
        matchStatus: data.matchStatus,
      };

      // Use setDoc to create or update
      await docRef.set(firestoreDoc);

      Logger.log('Bracket report updated successfully', {
        eventId,
        docId,
        position: data.position,
      });
    } catch (error) {
      Logger.error('Error updating bracket report in Firestore', error);
      throw error;
    }
  }

  /**
   * Submit dispute
   *
   * Replicates submitDisputeForm() from BracketData.js (line 574-639)
   * Document ID format: {team1Position}.{team2Position}.{matchNumber} (e.g., "W1.W2.0")
   */
  async submitDispute(
    eventId: string,
    team1Position: string,
    team2Position: string,
    matchNumber: number,
    data: Omit<Dispute, 'created_at' | 'updated_at'>
  ): Promise<string> {
    try {
      const db = getFirestore();
      const disputeId = `${team1Position}.${team2Position}.${matchNumber}`;
      const docRef = db.doc(`event/${eventId}/disputes/${disputeId}`);

      // Add timestamps
      const disputeDoc = {
        ...data,
        created_at: getServerTimestamp(),
        updated_at: getServerTimestamp(),
      };

      await docRef.set(disputeDoc);

      Logger.log('Dispute submitted successfully', {
        eventId,
        disputeId,
        match_number: matchNumber,
      });

      return disputeId;
    } catch (error) {
      Logger.error('Error submitting dispute to Firestore', error);
      throw error;
    }
  }

  /**
   * Respond to dispute
   *
   * Replicates respondDisputeForm() from BracketData.js (line 642-705)
   */
  async respondToDispute(
    eventId: string,
    disputeId: string,
    responseData: {
      response_teamId: string;
      response_teamNumber: number;
      response_explanation: string | null;
      response_userId: string;
      response_image_videos: string[];
    }
  ): Promise<void> {
    try {
      const db = getFirestore();
      const docRef = db.doc(`event/${eventId}/disputes/${disputeId}`);

      const updateData = {
        ...responseData,
        updated_at: getServerTimestamp(),
        status: 'responded',
      };

      await docRef.update(updateData);

      Logger.log('Dispute response updated successfully', {
        eventId,
        disputeId,
      });
    } catch (error) {
      Logger.error('Error responding to dispute in Firestore', error);
      throw error;
    }
  }

  /**
   * Resolve dispute
   *
   * Replicates resolveDisputeForm() from BracketData.js (line 266-317)
   * Also updates the associated bracket report
   */
  async resolveDispute(
    eventId: string,
    disputeId: string,
    resolutionData: {
      resolution_winner: string;
      resolution_resolved_by: string;
      match_number: number;
    },
    bracketUpdate?: {
      team1Position: string;
      team2Position: string;
      reportData: BracketReport;
    }
  ): Promise<void> {
    try {
      const db = getFirestore();
      const disputeRef = db.doc(`event/${eventId}/disputes/${disputeId}`);

      // Update dispute
      const disputeUpdateData = {
        resolution_winner: resolutionData.resolution_winner,
        resolution_resolved_by: resolutionData.resolution_resolved_by,
        updated_at: getServerTimestamp(),
      };

      await disputeRef.update(disputeUpdateData);

      // Also update bracket report if provided
      if (bracketUpdate) {
        await this.updateBracketReport(
          eventId,
          bracketUpdate.team1Position,
          bracketUpdate.team2Position,
          bracketUpdate.reportData
        );
      }

      Logger.log('Dispute resolved successfully', {
        eventId,
        disputeId,
        winner: resolutionData.resolution_winner,
      });
    } catch (error) {
      Logger.error('Error resolving dispute in Firestore', error);
      throw error;
    }
  }

  /**
   * Get dispute document
   */
  async getDispute(eventId: string, disputeId: string): Promise<Dispute | null> {
    try {
      const db = getFirestore();
      const docRef = db.doc(`event/${eventId}/disputes/${disputeId}`);
      const doc = await docRef.get();

      if (!doc.exists) {
        return null;
      }

      return doc.data() as Dispute;
    } catch (error) {
      Logger.error('Error getting dispute from Firestore', error);
      throw error;
    }
  }

  /**
   * Update report dispute status
   *
   * Helper method to update disputeResolved array in bracket report
   */
  async updateDisputeStatus(
    eventId: string,
    team1Position: string,
    team2Position: string,
    matchNumber: number,
    isResolved: boolean,
    reportData: BracketReport
  ): Promise<void> {
    try {
      // Update the disputeResolved array
      reportData.disputeResolved[matchNumber] = isResolved;

      await this.updateBracketReport(eventId, team1Position, team2Position, reportData);

      Logger.log('Dispute status updated in bracket report', {
        eventId,
        matchNumber,
        isResolved,
      });
    } catch (error) {
      Logger.error('Error updating dispute status', error);
      throw error;
    }
  }
}

export default new FirestoreService();
