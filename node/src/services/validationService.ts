import { query } from '../config/database';
import { Logger } from '../utils/logger';
import {
  Match,
  BracketDeadline,
  TeamMember,
  EventDetail,
  ValidationContext,
  ValidationResult,
} from '../models/types';

/**
 * Validation Service
 *
 * Replicates validation logic from ValidateBracketUpdateRequest.php
 */
export class ValidationService {
  /**
   * Validate match exists in brackets table
   */
  async validateMatchExists(context: ValidationContext): Promise<Match | null> {
    try {
      const sql = `
        SELECT *
        FROM brackets
        WHERE team1_id = ?
          AND team1_position = ?
          AND team2_id = ?
          AND team2_position = ?
          AND event_details_id = ?
        LIMIT 1
      `;

      const params = [
        context.team1Id,
        context.team1Position,
        context.team2Id,
        context.team2Position,
        context.eventId,
      ];

      const rows = await query<Match[]>(sql, params);

      if (rows.length === 0) {
        return null;
      }

      return rows[0];
    } catch (error) {
      Logger.error('Error validating match existence', error);
      throw error;
    }
  }

  /**
   * Validate organizer owns the event
   */
  async validateOrganizerPermission(userId: string, eventId: string): Promise<boolean> {
    try {
      const sql = `
        SELECT id
        FROM event_details
        WHERE id = ?
          AND user_id = ?
        LIMIT 1
      `;

      const rows = await query<EventDetail[]>(sql, [eventId, userId]);

      return rows.length > 0;
    } catch (error) {
      Logger.error('Error validating organizer permission', error);
      throw error;
    }
  }

  /**
   * Validate deadline for participant reporting
   *
   * Replicates the deadline check from PHP:
   * - Gets bracket_deadlines record for event
   * - Parses JSON deadlines structure
   * - Checks if current date is within start_date and end_date for the stage
   */
  async validateDeadline(
    eventId: string,
    stageName: string,
    innerStageName: string
  ): Promise<boolean> {
    try {
      const sql = `
        SELECT deadlines
        FROM bracket_deadlines
        WHERE event_details_id = ?
        LIMIT 1
      `;

      const rows = await query<any[]>(sql, [eventId]);

      if (rows.length === 0) {
        Logger.warn('No bracket deadlines found for event', { eventId });
        return false;
      }

      // Parse JSON deadlines
      let deadlines: Record<string, Record<string, any>>;

      if (typeof rows[0].deadlines === 'string') {
        deadlines = JSON.parse(rows[0].deadlines);
      } else {
        deadlines = rows[0].deadlines;
      }

      // Check if deadline exists for this stage
      const stageDeadlines = deadlines[stageName];

      if (!stageDeadlines) {
        Logger.warn('No deadlines for stage', { stageName, eventId });
        return false;
      }

      const deadline = stageDeadlines[innerStageName];

      if (!deadline || !deadline.start_date || !deadline.end_date) {
        Logger.warn('No deadline for inner stage', { stageName, innerStageName, eventId });
        return false;
      }

      // Check if current date is within deadline
      const now = new Date();
      const startDate = new Date(deadline.start_date);
      const endDate = new Date(deadline.end_date);

      // Set time to start/end of day for date comparison
      now.setHours(0, 0, 0, 0);
      startDate.setHours(0, 0, 0, 0);
      endDate.setHours(23, 59, 59, 999);

      const isWithinDeadline = now >= startDate && now <= endDate;

      if (!isWithinDeadline) {
        Logger.debug('Deadline check failed', {
          now: now.toISOString(),
          startDate: startDate.toISOString(),
          endDate: endDate.toISOString(),
          stageName,
          innerStageName,
        });
      }

      return isWithinDeadline;
    } catch (error) {
      Logger.error('Error validating deadline', error);
      throw error;
    }
  }

  /**
   * Validate user is a member of the team with accepted status
   */
  async validateTeamMembership(userId: string, teamId: string): Promise<boolean> {
    try {
      const sql = `
        SELECT id
        FROM team_members
        WHERE user_id = ?
          AND team_id = ?
          AND status = 'accepted'
        LIMIT 1
      `;

      const rows = await query<TeamMember[]>(sql, [userId, teamId]);

      return rows.length > 0;
    } catch (error) {
      Logger.error('Error validating team membership', error);
      throw error;
    }
  }

  /**
   * Main validation function
   *
   * Replicates the authorize() method from ValidateBracketUpdateRequest.php
   */
  async validateBracketUpdate(context: ValidationContext): Promise<ValidationResult> {
    try {
      // 1. Validate match exists
      const match = await this.validateMatchExists(context);

      if (!match) {
        return {
          valid: false,
          error: 'The match is not found in tournament bracket! Are you editing in the right place?',
        };
      }

      // 2. Role-specific validation
      if (context.userRole === 'ORGANIZER') {
        // Validate organizer owns the event
        const ownsEvent = await this.validateOrganizerPermission(context.userId, context.eventId);

        if (!ownsEvent) {
          return {
            valid: false,
            error: 'This is not your event!',
          };
        }
      } else if (context.userRole === 'PARTICIPANT') {
        // 3. Validate deadline (if required)
        if (context.willCheckDeadline) {
          const isWithinDeadline = await this.validateDeadline(
            context.eventId,
            match.stage_name,
            match.inner_stage_name
          );

          if (!isWithinDeadline) {
            return {
              valid: false,
              error: 'Match is not within reporting timeframe!',
            };
          }
        }

        // 4. Validate team membership
        if (!context.myTeamId) {
          return {
            valid: false,
            error: 'No valid team ID provided',
          };
        }

        const isMember = await this.validateTeamMembership(context.userId, context.myTeamId);

        if (!isMember) {
          return {
            valid: false,
            error: 'You are not a member of this team',
          };
        }
      } else if (context.userRole !== 'ADMIN') {
        return {
          valid: false,
          error: 'No valid user role',
        };
      }

      // All validations passed
      return {
        valid: true,
        match,
      };
    } catch (error) {
      Logger.error('Error in bracket validation', error);
      throw error;
    }
  }
}

export default new ValidationService();
