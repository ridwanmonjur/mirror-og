import { Request, Response } from 'express';
import {
  submitDisputeSchema,
  respondDisputeSchema,
  resolveDisputeSchema,
  SubmitDisputeRequest,
  RespondDisputeRequest,
  ResolveDisputeRequest,
} from '../schemas/validators';
import validationService from '../services/validationService';
import firestoreService from '../services/firestoreService';
import { Logger } from '../utils/logger';
import { ValidationContext } from '../models/types';

/**
 * Dispute Controller
 *
 * Handles dispute submission, response, and resolution
 */
export class DisputeController {
  /**
   * Submit dispute
   *
   * POST /api/brackets/:eventId/disputes
   *
   * Replicates submitDisputeForm() from BracketData.js
   */
  async submitDispute(req: Request, res: Response): Promise<void> {
    try {
      const { eventId } = req.params;
      const validatedData: SubmitDisputeRequest = submitDisputeSchema.parse(req.body);
      const user = req.user!;

      // Validate user is member of dispute team
      const isMember = await validationService.validateTeamMembership(
        user.id,
        validatedData.dispute_teamId
      );

      if (!isMember) {
        res.status(403).json({
          success: false,
          message: 'You are not a member of the disputing team',
        });
        return;
      }

      // Build validation context to check match and deadline
      const validationContext: ValidationContext = {
        eventId,
        team1Id: null, // Will be validated by match existence
        team2Id: null,
        team1Position: validatedData.team1_position,
        team2Position: validatedData.team2_position,
        myTeamId: validatedData.dispute_teamId,
        userId: user.id,
        userRole: user.role,
        willCheckDeadline: true,
      };

      // Validate match exists and deadline
      const validationResult = await validationService.validateBracketUpdate(validationContext);

      if (!validationResult.valid) {
        res.status(403).json({
          success: false,
          message: validationResult.error || 'Validation failed',
        });
        return;
      }

      // Submit dispute to Firestore
      const disputeId = await firestoreService.submitDispute(
        eventId,
        validatedData.team1_position,
        validatedData.team2_position,
        validatedData.match_number,
        {
          report_id: validatedData.report_id,
          match_number: validatedData.match_number,
          event_id: eventId,
          dispute_userId: validatedData.dispute_userId,
          dispute_teamId: validatedData.dispute_teamId,
          dispute_teamNumber: validatedData.dispute_teamNumber,
          dispute_reason: validatedData.dispute_reason,
          dispute_description: validatedData.dispute_description || null,
          dispute_image_videos: validatedData.dispute_image_videos,
          response_userId: null,
          response_teamId: null,
          response_teamNumber: null,
          response_explanation: null,
          response_image_videos: null,
          resolution_winner: null,
          resolution_resolved_by: null,
        }
      );

      res.json({
        success: true,
        message: 'Dispute submitted successfully',
        disputeId,
      });

      Logger.log('Dispute submitted', {
        eventId,
        disputeId,
        userId: user.id,
      });
    } catch (error) {
      Logger.error('Error in submitDispute', error);
      throw error;
    }
  }

  /**
   * Respond to dispute
   *
   * PATCH /api/brackets/:eventId/disputes/:disputeId/respond
   *
   * Replicates respondDisputeForm() from BracketData.js
   */
  async respondToDispute(req: Request, res: Response): Promise<void> {
    try {
      const { eventId, disputeId } = req.params;
      const validatedData: RespondDisputeRequest = respondDisputeSchema.parse(req.body);
      const user = req.user!;

      // Validate user is member of response team
      const isMember = await validationService.validateTeamMembership(
        user.id,
        validatedData.response_teamId
      );

      if (!isMember) {
        res.status(403).json({
          success: false,
          message: 'You are not a member of the responding team',
        });
        return;
      }

      // Check dispute exists
      const dispute = await firestoreService.getDispute(eventId, disputeId);

      if (!dispute) {
        res.status(404).json({
          success: false,
          message: 'Dispute not found',
        });
        return;
      }

      // Respond to dispute
      await firestoreService.respondToDispute(eventId, disputeId, {
        response_teamId: validatedData.response_teamId,
        response_teamNumber: validatedData.response_teamNumber,
        response_explanation: validatedData.response_explanation || null,
        response_userId: validatedData.response_userId,
        response_image_videos: validatedData.response_image_videos,
      });

      res.json({
        success: true,
        message: 'Dispute response submitted successfully',
      });

      Logger.log('Dispute response submitted', {
        eventId,
        disputeId,
        userId: user.id,
      });
    } catch (error) {
      Logger.error('Error in respondToDispute', error);
      throw error;
    }
  }

  /**
   * Resolve dispute
   *
   * PATCH /api/brackets/:eventId/disputes/:disputeId/resolve
   *
   * Replicates resolveDisputeForm() from BracketData.js
   * Only organizers can resolve disputes
   */
  async resolveDispute(req: Request, res: Response): Promise<void> {
    try {
      const { eventId, disputeId } = req.params;
      const validatedData: ResolveDisputeRequest = resolveDisputeSchema.parse(req.body);
      const user = req.user!;

      // Only organizers can resolve disputes
      if (user.role !== 'ORGANIZER') {
        res.status(403).json({
          success: false,
          message: 'Only organizers can resolve disputes',
        });
        return;
      }

      // Validate organizer owns the event
      const ownsEvent = await validationService.validateOrganizerPermission(user.id, eventId);

      if (!ownsEvent) {
        res.status(403).json({
          success: false,
          message: 'You do not own this event',
        });
        return;
      }

      // Check dispute exists
      const dispute = await firestoreService.getDispute(eventId, disputeId);

      if (!dispute) {
        res.status(404).json({
          success: false,
          message: 'Dispute not found',
        });
        return;
      }

      // Prepare bracket update if provided
      let bracketUpdate;
      if (validatedData.reportData && validatedData.team1_position && validatedData.team2_position) {
        bracketUpdate = {
          team1Position: validatedData.team1_position,
          team2Position: validatedData.team2_position,
          reportData: validatedData.reportData,
        };
      }

      // Resolve dispute
      await firestoreService.resolveDispute(
        eventId,
        disputeId,
        {
          resolution_winner: validatedData.resolution_winner,
          resolution_resolved_by: validatedData.resolution_resolved_by,
          match_number: validatedData.match_number,
        },
        bracketUpdate
      );

      res.json({
        success: true,
        message: 'Dispute resolved successfully',
      });

      Logger.log('Dispute resolved', {
        eventId,
        disputeId,
        userId: user.id,
        winner: validatedData.resolution_winner,
      });
    } catch (error) {
      Logger.error('Error in resolveDispute', error);
      throw error;
    }
  }
}

export default new DisputeController();
