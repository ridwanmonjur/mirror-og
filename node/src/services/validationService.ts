import { prisma } from '../config/database';
import { Logger } from '../utils/logger';
import {
  Match,
  ValidationContext,
  ValidationResult,
} from '../models/types';

/**
 * Validation Service (Prisma Version)
 *
 * Replicates validation logic from ValidateBracketUpdateRequest.php
 * Using Prisma ORM instead of raw SQL queries
 */
export class ValidationService {
  /**
   * Validate match exists in brackets table
   */
  async validateMatchExists(context: ValidationContext): Promise<Match | null> {
    try {
      const match = await prisma.brackets.findFirst({
        where: {
          team1_id: context.team1Id,
          team1_position: context.team1Position,
          team2_id: context.team2Id,
          team2_position: context.team2Position,
          event_details_id: parseInt(context.eventId),
        },
      });

      return match as Match | null;
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
      const event = await prisma.event_details.findFirst({
        where: {
          id: parseInt(eventId),
          user_id: parseInt(userId),
        },
        select: {
          id: true,
        },
      });

      return event !== null;
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
      const bracketDeadline = await prisma.bracketDeadline.findUnique({
        where: {
          event_details_id: parseInt(eventId),
        },
        select: {
          deadlines: true,
        },
      });

      if (!bracketDeadline) {
        Logger.warn('No bracket deadlines found for event', { eventId });
        return false;
      }

      // Parse JSON deadlines
      const deadlines = bracketDeadline.deadlines as Record<string, Record<string, any>>;

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
      const membership = await prisma.team_members.findFirst({
        where: {
          user_id: parseInt(userId),
          team_id: parseInt(teamId),
          status: 'accepted',
        },
        select: {
          id: true,
        },
      });

      return membership !== null;
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
