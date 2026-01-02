import { Request, Response } from 'express';
import { bracketReportSchema, BracketReportRequest } from '../schemas/validators';
import validationService from '../services/validationService';
import firestoreService from '../services/firestoreService';
import { Logger } from '../utils/logger';
import { ValidationContext } from '../models/types';

/**
 * Bracket Controller
 *
 * Handles bracket report endpoints
 */
export class BracketController {
  /**
   * Report match results
   *
   * POST /api/brackets/:eventId/report
   *
   * Replicates the flow from BracketData.js writeReportDB()
   * with server-side validation from ValidateBracketUpdateRequest.php
   */
  async reportMatchResults(req: Request, res: Response): Promise<void> {
    try {
      const { eventId } = req.params;

      // Validate request body with Zod
      const validatedData: BracketReportRequest = bracketReportSchema.parse(req.body);

      // Get authenticated user from JWT middleware
      const user = req.user!;

      // Build validation context
      const validationContext: ValidationContext = {
        eventId,
        team1Id: validatedData.team1_id,
        team2Id: validatedData.team2_id,
        team1Position: validatedData.team1_position,
        team2Position: validatedData.team2_position,
        myTeamId: validatedData.my_team_id,
        userId: user.id,
        userRole: user.role,
        willCheckDeadline: validatedData.willCheckDeadline,
      };

      // Validate bracket update
      const validationResult = await validationService.validateBracketUpdate(validationContext);

      if (!validationResult.valid) {
        res.status(403).json({
          success: false,
          message: validationResult.error || 'Validation failed',
        });
        return;
      }

      // Write to Firestore
      await firestoreService.updateBracketReport(
        eventId,
        validatedData.team1_position,
        validatedData.team2_position,
        validatedData.reportData
      );

      // Return success response
      res.json({
        success: true,
        message: 'Report updated successfully',
      });

      Logger.log('Bracket report updated', {
        eventId,
        userId: user.id,
        position: `${validatedData.team1_position}.${validatedData.team2_position}`,
      });
    } catch (error) {
      Logger.error('Error in reportMatchResults', error);
      throw error;
    }
  }
}

export default new BracketController();
