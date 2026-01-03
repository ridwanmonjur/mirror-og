import * as admin from 'firebase-admin';
import { Logger } from '../utils/logger';

// Dispute Enums - equivalent to Laravel's enum configuration
const DISPUTE_ENUMS = {
  ORGANIZER: 3,
  DISPUTEE: 4,
  RESPONDER: 5,
  TIME: 6,
  RANDOM: 7,
};

interface BracketData {
  [key: string]: any;
  team1_position?: string;
  team2_position?: string;
  event_details_id?: string;
}

interface DisputeData {
  [key: string]: any;
  dispute_teamNumber?: number;
  response_teamNumber?: number;
  response_teamId?: string;
}

interface MatchStatusData {
  realWinners?: (string | null)[];
  disputeResolved?: (boolean | null)[];
  team1Winners?: (string | null)[];
  team2Winners?: (string | null)[];
  defaultWinners?: (boolean | null)[];
  randomWinners?: (boolean | null)[];
  score?: number[];
}

interface UpdateDisputeValue {
  resolution_winner?: string;
  resolution_resolved_by?: number;
}

interface DeadlineResult {
  dispute_ref_list: (admin.firestore.DocumentReference | null)[];
  update_dispute_values: (UpdateDisputeValue | null)[];
  update_values: { [key: string]: any };
  next_stage_data: any | null;
}

/**
 * TypeScript implementation of the DeadlineTaskTrait functionality
 * Converted from cloud_server_functions/main.py
 */
export class DeadlineService {
  private db: admin.firestore.Firestore;
  private allBrackets: Map<string, any>;
  private allDisputes: Map<string, any>;
  private disputeEnums: typeof DISPUTE_ENUMS;

  constructor() {
    this.db = admin.firestore();
    this.allBrackets = new Map();
    this.allDisputes = new Map();
    this.disputeEnums = DISPUTE_ENUMS;
  }

  /**
   * Fetch all brackets and disputes for an event in bulk
   */
  async fetchAllEventData(eventDetailsId: string | number): Promise<{ brackets_count: number; disputes_count: number }> {
    try {
      const eventId = String(eventDetailsId);

      // Fetch all brackets for this event
      const bracketsCollection = this.db.collection('event').doc(eventId).collection('brackets');
      const bracketDocs = await bracketsCollection.get();

      this.allBrackets.clear();
      bracketDocs.forEach((doc) => {
        this.allBrackets.set(doc.id, doc.data());
      });

      // Fetch all disputes for this event
      const disputesCollection = this.db.collection('event').doc(eventId).collection('disputes');
      const disputeDocs = await disputesCollection.get();

      this.allDisputes.clear();
      disputeDocs.forEach((doc) => {
        this.allDisputes.set(doc.id, doc.data());
      });

      Logger.log(`Fetched all event data for ${eventId}: ${this.allBrackets.size} brackets, ${this.allDisputes.size} disputes`);

      return {
        brackets_count: this.allBrackets.size,
        disputes_count: this.allDisputes.size,
      };
    } catch (error) {
      Logger.error(`Failed to fetch event data for ${eventDetailsId}`, error);
      throw error;
    }
  }

  /**
   * Calculate scores from real winners array
   */
  private calcScores(realWinners: (string | null)[]): number[] {
    let score1 = 0;
    let score2 = 0;

    for (const value of realWinners) {
      if (value === null) {
        continue;
      }
      if (value === '1') {
        score1 += 1;
      } else {
        score2 += 1;
      }
    }

    return [score1, score2];
  }

  /**
   * Handle match dispute resolution
   */
  private handleDisputes(
    matchStatusData: MatchStatusData,
    bracket: BracketData,
    eventId: string,
    willBreakConflicts: boolean = false,
    gamesPerMatch: number = 3
  ): {
    updateReportValues: { [key: string]: any };
    disputeRefList: (admin.firestore.DocumentReference | null)[];
    updateDisputeValues: (UpdateDisputeValue | null)[];
    isUpdatedDispute: boolean;
  } {
    const realWinners = matchStatusData.realWinners || Array(gamesPerMatch).fill(null);
    const disputeResolved = matchStatusData.disputeResolved || Array(gamesPerMatch).fill(null);
    let isUpdatedDispute = false;
    let updateReportValues: { [key: string]: any } = {};
    const updateDisputeValues: (UpdateDisputeValue | null)[] = Array(gamesPerMatch).fill(null);
    const disputeRefList: (admin.firestore.DocumentReference | null)[] = Array(gamesPerMatch).fill(null);

    for (let i = 0; i < gamesPerMatch; i++) {
      if (realWinners[i] === null) {
        if (!disputeResolved[i]) {
          const disputePath = `${bracket.team1_position}.${bracket.team2_position}.${i}`;

          // Use hashmap lookup instead of individual Firestore query
          if (this.allDisputes.has(disputePath)) {
            const data = this.allDisputes.get(disputePath) as DisputeData;
            const disputeRef = this.db.collection('event').doc(eventId).collection('disputes').doc(disputePath);

            // Case 1: One team filed dispute, other hasn't responded
            if (data.dispute_teamNumber !== undefined && data.response_teamId === undefined) {
              isUpdatedDispute = true;
              const winnerChosen = String(data.dispute_teamNumber);
              realWinners[i] = winnerChosen;
              disputeResolved[i] = true;
              updateDisputeValues[i] = {
                resolution_winner: winnerChosen,
                resolution_resolved_by: this.disputeEnums.TIME,
              };
              disputeRefList[i] = disputeRef;
            }
            // Case 2: Both teams filed conflicting claims and we break conflicts
            else if (willBreakConflicts && data.response_teamNumber !== undefined) {
              isUpdatedDispute = true;
              const chosenWinner = Math.random() < 0.5 ? String(data.dispute_teamNumber) : String(data.response_teamNumber);
              realWinners[i] = chosenWinner;
              disputeResolved[i] = true;
              updateDisputeValues[i] = {
                resolution_winner: chosenWinner,
                resolution_resolved_by: this.disputeEnums.RANDOM,
              };
              disputeRefList[i] = disputeRef;
            }
          }
        }
      }
    }

    const scores = this.calcScores(realWinners);

    if (isUpdatedDispute) {
      updateReportValues = {
        realWinners: realWinners,
        score: scores,
        disputeResolved: disputeResolved,
      };
    }

    return { updateReportValues, disputeRefList, updateDisputeValues, isUpdatedDispute };
  }

  /**
   * Resolve winners for matches with incomplete/conflicted/tied submissions
   */
  private handleReports(
    matchStatusData: MatchStatusData,
    gamesPerMatch: number = 3,
    willBreakTiesAndConflicts: boolean = false
  ): {
    newUpdate: { [key: string]: any };
    updated: boolean;
  } {
    const team1Winners = matchStatusData.team1Winners || Array(gamesPerMatch).fill(null);
    const team2Winners = matchStatusData.team2Winners || Array(gamesPerMatch).fill(null);
    const realWinners = matchStatusData.realWinners || Array(gamesPerMatch).fill(null);
    const defaultWinners = matchStatusData.defaultWinners || Array(gamesPerMatch).fill(null);
    const randomWinners = matchStatusData.randomWinners || Array(gamesPerMatch).fill(null);

    let noScores = 0;
    let updated = false;
    let newUpdate: { [key: string]: any } = {};
    let disqualified = false;

    for (let i = 0; i < gamesPerMatch; i++) {
      if (realWinners[i] === null) {
        // Complete but conflict
        if (team2Winners[i] !== null && team1Winners[i] !== null) {
          if (team2Winners[i] === team1Winners[i]) {
            updated = true;
            const winnerChosen = String(team1Winners[i]);
            realWinners[i] = winnerChosen;
          }
          if (willBreakTiesAndConflicts) {
            const disputeResolved = matchStatusData.disputeResolved || Array(gamesPerMatch).fill(null);
            if (disputeResolved[i] === null || disputeResolved[i]) {
              updated = true;
              const winnerChosen = String(Math.floor(Math.random() * 2));
              realWinners[i] = winnerChosen;
              randomWinners[i] = true;
            }
          }
        }
        // Only team 2 submitted
        else if (team2Winners[i] !== null && team1Winners[i] === null) {
          updated = true;
          defaultWinners[i] = true;
          const winnerChosen = String(team2Winners[i]);
          realWinners[i] = winnerChosen;
        }
        // Only team 1 submitted
        else if (team1Winners[i] !== null && team2Winners[i] === null) {
          updated = true;
          defaultWinners[i] = true;
          const winnerChosen = String(team1Winners[i]);
          realWinners[i] = winnerChosen;
        }
        // Neither team submitted
        else {
          noScores += 1;
        }
      }
    }

    const scores = this.calcScores(realWinners);

    // Check for disqualification (no scores submitted for any game)
    if (noScores === gamesPerMatch) {
      updated = true;
      disqualified = true;
    } else if (willBreakTiesAndConflicts) {
      // Break Tie
      if (scores[0] === scores[1]) {
        for (let i = 0; i < gamesPerMatch; i++) {
          if (team2Winners[i] === null && team1Winners[i] === null) {
            if (willBreakTiesAndConflicts) {
              const disputeResolved = matchStatusData.disputeResolved || Array(gamesPerMatch).fill(null);
              if (disputeResolved[i] === null || disputeResolved[i]) {
                updated = true;
                const winnerChosen = String(Math.floor(Math.random() * 2));
                realWinners[i] = winnerChosen;
                randomWinners[i] = true;
              }
            }
          }
        }
      }
    }

    if (updated) {
      newUpdate = {
        realWinners: realWinners,
        score: scores,
        defaultWinners: defaultWinners,
        randomWinners: randomWinners,
        disqualified: disqualified,
      };
    }

    return { newUpdate, updated };
  }

  /**
   * Main deadline interpretation logic
   */
  interpretDeadlines(
    matchStatusData: MatchStatusData,
    updateValues: { [key: string]: any },
    bracket: BracketData,
    extraBracket: any,
    tierId: string | number,
    afterOrganizerDeadline: boolean = false,
    isLeague: boolean = false,
    gamesPerMatch: number = 3
  ): DeadlineResult {
    // Handle disputes
    const { updateReportValues, disputeRefList, updateDisputeValues, isUpdatedDispute } = this.handleDisputes(
      matchStatusData,
      bracket,
      String(bracket.event_details_id),
      afterOrganizerDeadline,
      gamesPerMatch
    );

    if (isUpdatedDispute) {
      Object.assign(updateValues, updateReportValues);
      Object.assign(matchStatusData, updateReportValues);
    }

    // Handle reports
    const { newUpdate, updated } = this.handleReports(matchStatusData, gamesPerMatch, afterOrganizerDeadline);

    if (updated) {
      Object.assign(updateValues, newUpdate);
      Object.assign(matchStatusData, newUpdate);
    }

    // Return data for PHP to handle resolveNextStage
    let nextStageData = null;
    if (!isLeague && matchStatusData.score) {
      nextStageData = {
        bracket: bracket,
        extra_bracket: extraBracket,
        score: matchStatusData.score,
        tier_id: tierId,
      };
    }

    return {
      dispute_ref_list: disputeRefList,
      update_dispute_values: updateDisputeValues,
      update_values: updateValues,
      next_stage_data: nextStageData,
    };
  }

  /**
   * Get bracket data from cache
   */
  getBracket(matchId: string): any | undefined {
    return this.allBrackets.get(matchId);
  }

  /**
   * Get dispute data from cache
   */
  getDispute(disputeId: string): any | undefined {
    return this.allDisputes.get(disputeId);
  }
}
