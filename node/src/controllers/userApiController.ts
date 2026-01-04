import { Response } from 'express';
import { Logger } from '../utils/logger';
import { query } from '../config/database';
import { AuthenticatedRequest } from '../middleware/roleCheck';

/**
 * User API Controller
 * Converted from Laravel's UserController and SocialController
 * These endpoints require authentication
 */

/**
 * Get current authenticated user
 * GET /api/user
 */
export async function getCurrentUser(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;

    const [user] = await query(
      `SELECT * FROM users WHERE id = ?`,
      [userId]
    );

    if (!user) {
      res.status(404).json({
        success: false,
        error: 'User not found',
      });
      return;
    }

    res.status(200).json(user);
  } catch (error) {
    Logger.error('Failed to get current user', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve user',
    });
  }
}

/**
 * View user notifications
 * GET /api/user/notifications
 * Converted from UserController::viewNotifications
 */
export async function viewNotifications(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;

    const notifications = await query(
      `SELECT * FROM notifications
       WHERE user_id = ?
       ORDER BY created_at DESC
       LIMIT 50`,
      [userId]
    );

    res.status(200).json({
      success: true,
      data: notifications,
    });
  } catch (error) {
    Logger.error('Failed to view notifications', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve notifications',
    });
  }
}

/**
 * Create notification
 * POST /api/user/notifications
 * Converted from UserController::createNotification
 */
export async function createNotification(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { user_id, title, message, type } = req.body;

    const result = await query(
      `INSERT INTO notifications (user_id, title, message, type, read_at, created_at, updated_at)
       VALUES (?, ?, ?, ?, NULL, NOW(), NOW())`,
      [user_id, title, message, type]
    );

    res.status(201).json({
      success: true,
      message: 'Notification created successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to create notification', error);
    res.status(500).json({
      success: false,
      error: 'Failed to create notification',
    });
  }
}

/**
 * Mark notification as read
 * POST /api/user/notifications/:id
 * Converted from UserController::markAsRead
 */
export async function markNotificationAsRead(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const notificationId = req.params.id;
    const userId = req.user?.id;

    await query(
      `UPDATE notifications
       SET read_at = NOW()
       WHERE id = ? AND user_id = ?`,
      [notificationId, userId]
    );

    res.status(200).json({
      success: true,
      message: 'Notification marked as read',
    });
  } catch (error) {
    Logger.error('Failed to mark notification as read', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update notification',
    });
  }
}

/**
 * Change user settings
 * POST /api/user/settings
 * Converted from UserController::changeSettings
 */
export async function changeSettings(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { settings } = req.body;

    await query(
      `UPDATE users
       SET settings = ?, updated_at = NOW()
       WHERE id = ?`,
      [JSON.stringify(settings), userId]
    );

    res.status(200).json({
      success: true,
      message: 'Settings updated successfully',
    });
  } catch (error) {
    Logger.error('Failed to change settings', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update settings',
    });
  }
}

/**
 * Replace user background
 * POST /api/user/:id/background
 * Converted from UserController::replaceBackground
 */
export async function replaceBackground(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.params.id;
    const { background_url } = req.body;

    await query(
      `UPDATE users
       SET background_url = ?, updated_at = NOW()
       WHERE id = ?`,
      [background_url, userId]
    );

    res.status(200).json({
      success: true,
      message: 'Background updated successfully',
    });
  } catch (error) {
    Logger.error('Failed to replace background', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update background',
    });
  }
}

/**
 * Toggle star/favorite user
 * POST /api/user/:id/star
 * Converted from SocialController::toggleStar
 */
export async function toggleStar(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const targetUserId = req.params.id;
    const userId = req.user?.id;

    // Check if already starred
    const [existing] = await query(
      `SELECT * FROM user_stars WHERE user_id = ? AND starred_user_id = ?`,
      [userId, targetUserId]
    );

    if (existing) {
      // Unstar
      await query(
        `DELETE FROM user_stars WHERE user_id = ? AND starred_user_id = ?`,
        [userId, targetUserId]
      );

      res.status(200).json({
        success: true,
        message: 'User unstarred',
        starred: false,
      });
    } else {
      // Star
      await query(
        `INSERT INTO user_stars (user_id, starred_user_id, created_at, updated_at)
         VALUES (?, ?, NOW(), NOW())`,
        [userId, targetUserId]
      );

      res.status(200).json({
        success: true,
        message: 'User starred',
        starred: true,
      });
    }
  } catch (error) {
    Logger.error('Failed to toggle star', error);
    res.status(500).json({
      success: false,
      error: 'Failed to toggle star',
    });
  }
}

/**
 * Report user
 * POST /api/user/:id/report
 * Converted from SocialController::report
 */
export async function reportUser(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const targetUserId = req.params.id;
    const reporterId = req.user?.id;
    const { reason, description } = req.body;

    const result = await query(
      `INSERT INTO user_reports (reporter_id, reported_user_id, reason, description, created_at, updated_at)
       VALUES (?, ?, ?, ?, NOW(), NOW())`,
      [reporterId, targetUserId, reason, description]
    );

    res.status(201).json({
      success: true,
      message: 'User reported successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to report user', error);
    res.status(500).json({
      success: false,
      error: 'Failed to report user',
    });
  }
}

/**
 * Get user reports
 * GET /api/user/:id/reports
 * Converted from SocialController::getReports
 */
export async function getReports(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.params.id;

    const reports = await query(
      `SELECT ur.*, u.name as reporter_name
       FROM user_reports ur
       INNER JOIN users u ON ur.reporter_id = u.id
       WHERE ur.reported_user_id = ?
       ORDER BY ur.created_at DESC`,
      [userId]
    );

    res.status(200).json({
      success: true,
      data: reports,
    });
  } catch (error) {
    Logger.error('Failed to get reports', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve reports',
    });
  }
}

/**
 * Search participants
 * POST /api/user/participants
 * Converted from ParticipantController::searchParticipant
 */
export async function searchParticipants(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { search_term, limit = 20 } = req.body;

    const participants = await query(
      `SELECT * FROM users
       WHERE role = 'PARTICIPANT'
       AND (name LIKE ? OR email LIKE ?)
       LIMIT ?`,
      [`%${search_term}%`, `%${search_term}%`, limit]
    );

    res.status(200).json({
      success: true,
      data: participants,
    });
  } catch (error) {
    Logger.error('Failed to search participants', error);
    res.status(500).json({
      success: false,
      error: 'Failed to search participants',
    });
  }
}

/**
 * Unlink bank account
 * POST /api/user/unlink
 * Converted from UserController::unlinkBankAccount
 */
export async function unlinkBankAccount(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;

    await query(
      `UPDATE users
       SET bank_account_id = NULL, updated_at = NOW()
       WHERE id = ?`,
      [userId]
    );

    res.status(200).json({
      success: true,
      message: 'Bank account unlinked successfully',
    });
  } catch (error) {
    Logger.error('Failed to unlink bank account', error);
    res.status(500).json({
      success: false,
      error: 'Failed to unlink bank account',
    });
  }
}
