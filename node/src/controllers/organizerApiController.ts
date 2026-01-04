import { Response } from 'express';
import { Logger } from '../utils/logger';
import { query } from '../config/database';
import { AuthenticatedRequest } from '../middleware/roleCheck';

/**
 * Organizer API Controller
 * Converted from Laravel's OrganizerController, OrganizerEventController, and OrganizerEventResultsController
 * These endpoints require organizer or admin role
 */

/**
 * Search events
 * POST /api/organizer/events/search
 * Converted from OrganizerEventController::search
 */
export async function searchEvents(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { search_term, filters, page = 1, limit = 20 } = req.body;
    const offset = (page - 1) * limit;

    let whereClause = 'WHERE organizer_id = ?';
    const params: any[] = [userId];

    if (search_term) {
      whereClause += ' AND (name LIKE ? OR description LIKE ?)';
      params.push(`%${search_term}%`, `%${search_term}%`);
    }

    if (filters?.game_id) {
      whereClause += ' AND game_id = ?';
      params.push(filters.game_id);
    }

    if (filters?.status) {
      whereClause += ' AND status = ?';
      params.push(filters.status);
    }

    const events = await query(
      `SELECT * FROM event_details
       ${whereClause}
       ORDER BY start_date DESC
       LIMIT ? OFFSET ?`,
      [...params, limit, offset]
    );

    res.status(200).json({
      success: true,
      data: events,
    });
  } catch (error) {
    Logger.error('Failed to search events', error);
    res.status(500).json({
      success: false,
      error: 'Failed to search events',
    });
  }
}

/**
 * Delete event
 * POST /api/organizer/event/:id/destroy
 * Converted from OrganizerEventController::destroy
 */
export async function destroyEvent(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const userId = req.user?.id;

    // Verify user owns this event
    const [event] = await query(
      `SELECT * FROM event_details WHERE id = ? AND organizer_id = ?`,
      [eventId, userId]
    );

    if (!event) {
      res.status(404).json({
        success: false,
        error: 'Event not found or you do not have permission to delete it',
      });
      return;
    }

    await query(`DELETE FROM event_details WHERE id = ?`, [eventId]);

    res.status(200).json({
      success: true,
      message: 'Event deleted successfully',
    });
  } catch (error) {
    Logger.error('Failed to destroy event', error);
    res.status(500).json({
      success: false,
      error: 'Failed to delete event',
    });
  }
}

/**
 * Store event results
 * POST /api/organizer/event/:id/results
 * Converted from OrganizerEventResultsController::store
 */
export async function storeResults(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { team_id, placement, prize_amount } = req.body;

    const result = await query(
      `INSERT INTO event_results (event_id, team_id, placement, prize_amount, created_at, updated_at)
       VALUES (?, ?, ?, ?, NOW(), NOW())
       ON DUPLICATE KEY UPDATE placement = ?, prize_amount = ?, updated_at = NOW()`,
      [eventId, team_id, placement, prize_amount, placement, prize_amount]
    );

    res.status(201).json({
      success: true,
      message: 'Results stored successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to store results', error);
    res.status(500).json({
      success: false,
      error: 'Failed to store results',
    });
  }
}

/**
 * Send event notifications
 * POST /api/organizer/event/:id/notifications
 * Converted from OrganizerEventController::storeNotify
 */
export async function storeNotification(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { title, message, recipient_type } = req.body;

    // Get recipients based on type (all participants, specific teams, etc.)
    let recipients: any[] = [];

    if (recipient_type === 'all_participants') {
      recipients = await query(
        `SELECT DISTINCT user_id FROM event_participants WHERE event_id = ?`,
        [eventId]
      );
    }

    // Create notifications for each recipient
    for (const recipient of recipients) {
      await query(
        `INSERT INTO notifications (user_id, title, message, type, event_id, created_at, updated_at)
         VALUES (?, ?, ?, 'event_notification', ?, NOW(), NOW())`,
        [recipient.user_id, title, message, eventId]
      );
    }

    res.status(201).json({
      success: true,
      message: `Notifications sent to ${recipients.length} users`,
      count: recipients.length,
    });
  } catch (error) {
    Logger.error('Failed to store notification', error);
    res.status(500).json({
      success: false,
      error: 'Failed to send notifications',
    });
  }
}

/**
 * Upsert bracket/match data
 * POST /api/organizer/event/:id/matches
 * Converted from OrganizerEventResultsController::upsertBracket
 */
export async function upsertBracket(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { match_id, team1_id, team2_id, winner_id, status, round } = req.body;

    const result = await query(
      `INSERT INTO event_matches (event_id, match_id, team1_id, team2_id, winner_id, status, round, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
       ON DUPLICATE KEY UPDATE
         team1_id = ?, team2_id = ?, winner_id = ?, status = ?, round = ?, updated_at = NOW()`,
      [eventId, match_id, team1_id, team2_id, winner_id, status, round, team1_id, team2_id, winner_id, status, round]
    );

    res.status(200).json({
      success: true,
      message: 'Bracket updated successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to upsert bracket', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update bracket',
    });
  }
}

/**
 * Store award
 * POST /api/organizer/event/:id/awards
 * Converted from OrganizerEventResultsController::storeAward
 */
export async function storeAward(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { team_id, user_id, award_type, award_name, award_value } = req.body;

    const result = await query(
      `INSERT INTO event_awards (event_id, team_id, user_id, award_type, award_name, award_value, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      [eventId, team_id, user_id, award_type, award_name, award_value]
    );

    res.status(201).json({
      success: true,
      message: 'Award created successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to store award', error);
    res.status(500).json({
      success: false,
      error: 'Failed to create award',
    });
  }
}

/**
 * Delete award
 * DELETE /api/organizer/event/:id/awards/:awardId
 * Converted from OrganizerEventResultsController::destroyAward
 */
export async function destroyAward(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const awardId = req.params.awardId;

    await query(
      `DELETE FROM event_awards WHERE id = ? AND event_id = ?`,
      [awardId, eventId]
    );

    res.status(200).json({
      success: true,
      message: 'Award deleted successfully',
    });
  } catch (error) {
    Logger.error('Failed to destroy award', error);
    res.status(500).json({
      success: false,
      error: 'Failed to delete award',
    });
  }
}

/**
 * Store achievements
 * POST /api/organizer/event/:id/achievements
 * Converted from OrganizerEventResultsController::storeAchievements
 */
export async function storeAchievements(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { user_id, achievement_type, achievement_name, achievement_data } = req.body;

    const result = await query(
      `INSERT INTO user_achievements (user_id, event_id, achievement_type, achievement_name, achievement_data, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, NOW(), NOW())`,
      [user_id, eventId, achievement_type, achievement_name, JSON.stringify(achievement_data)]
    );

    res.status(201).json({
      success: true,
      message: 'Achievement created successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to store achievements', error);
    res.status(500).json({
      success: false,
      error: 'Failed to create achievement',
    });
  }
}

/**
 * Delete achievement
 * DELETE /api/organizer/event/achievements/:achievementId
 * Converted from OrganizerEventResultsController::destroyAchievements
 */
export async function destroyAchievements(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const achievementId = req.params.achievementId;

    await query(
      `DELETE FROM user_achievements WHERE id = ?`,
      [achievementId]
    );

    res.status(200).json({
      success: true,
      message: 'Achievement deleted successfully',
    });
  } catch (error) {
    Logger.error('Failed to destroy achievements', error);
    res.status(500).json({
      success: false,
      error: 'Failed to delete achievement',
    });
  }
}

/**
 * Edit organizer profile
 * POST /api/organizer/profile
 * Converted from OrganizerController::editProfile
 */
export async function editProfile(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { name, bio, company_name, website, avatar_url, social_links } = req.body;

    await query(
      `UPDATE users
       SET name = ?, bio = ?, company_name = ?, website = ?, avatar_url = ?, social_links = ?, updated_at = NOW()
       WHERE id = ?`,
      [name, bio, company_name, website, avatar_url, JSON.stringify(social_links), userId]
    );

    res.status(200).json({
      success: true,
      message: 'Profile updated successfully',
    });
  } catch (error) {
    Logger.error('Failed to edit profile', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update profile',
    });
  }
}
