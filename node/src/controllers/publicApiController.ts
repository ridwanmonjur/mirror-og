import { Request, Response } from 'express';
import { Logger } from '../utils/logger';
import { query } from '../config/database';

/**
 * Public API Controller
 * Converted from Laravel's Open and Shared controllers
 * These endpoints don't require authentication
 */

/**
 * Get activity logs for a user
 * GET /api/user/:id/logs
 * Converted from ParticipantController::getActivityLogs
 */
export async function getActivityLogs(req: Request, res: Response): Promise<void> {
  try {
    const userId = req.params.id;

    // Query activity logs from database
    const logs = await query(
      `SELECT * FROM activity_logs
       WHERE user_id = ?
       ORDER BY created_at DESC
       LIMIT 50`,
      [userId]
    );

    res.status(200).json({
      success: true,
      data: logs,
    });
  } catch (error) {
    Logger.error('Failed to get activity logs', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve activity logs',
    });
  }
}

/**
 * Get user connections (followers/following)
 * GET /api/user/:id/connections
 * Converted from SocialController::getConnections
 */
export async function getConnections(req: Request, res: Response): Promise<void> {
  try {
    const userId = req.params.id;

    // Get followers
    const followers = await query(
      `SELECT u.* FROM users u
       INNER JOIN user_followers uf ON u.id = uf.follower_id
       WHERE uf.user_id = ?`,
      [userId]
    );

    // Get following
    const following = await query(
      `SELECT u.* FROM users u
       INNER JOIN user_followers uf ON u.id = uf.user_id
       WHERE uf.follower_id = ?`,
      [userId]
    );

    res.status(200).json({
      success: true,
      data: {
        followers,
        following,
      },
    });
  } catch (error) {
    Logger.error('Failed to get connections', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve connections',
    });
  }
}

/**
 * Store event invitation
 * POST /api/event/:id/invitation
 * Converted from OrganizerInvitationController::store
 */
export async function storeEventInvitation(req: Request, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { team_id, user_id, message } = req.body;

    // Insert invitation
    const result = await query(
      `INSERT INTO event_invitations (event_id, team_id, user_id, message, created_at, updated_at)
       VALUES (?, ?, ?, ?, NOW(), NOW())`,
      [eventId, team_id, user_id, message]
    );

    res.status(201).json({
      success: true,
      message: 'Invitation sent successfully',
      data: {
        id: result.insertId,
      },
    });
  } catch (error) {
    Logger.error('Failed to store event invitation', error);
    res.status(500).json({
      success: false,
      error: 'Failed to send invitation',
    });
  }
}

/**
 * Delete event invitation
 * POST /api/event/:id/inviteDestroy
 * Converted from OrganizerInvitationController::destroy
 */
export async function destroyEventInvitation(req: Request, res: Response): Promise<void> {
  try {
    const eventId = req.params.id;
    const { invitation_id } = req.body;

    await query(
      `DELETE FROM event_invitations
       WHERE id = ? AND event_id = ?`,
      [invitation_id, eventId]
    );

    res.status(200).json({
      success: true,
      message: 'Invitation deleted successfully',
    });
  } catch (error) {
    Logger.error('Failed to delete event invitation', error);
    res.status(500).json({
      success: false,
      error: 'Failed to delete invitation',
    });
  }
}

/**
 * Upload media (image/video)
 * POST /api/media
 * Converted from ImageVideoController::upload
 */
export async function uploadMedia(req: Request, res: Response): Promise<void> {
  try {
    const { file_path, file_type, user_id } = req.body;

    const result = await query(
      `INSERT INTO image_videos (file_path, file_type, user_id, created_at, updated_at)
       VALUES (?, ?, ?, NOW(), NOW())`,
      [file_path, file_type, user_id]
    );

    res.status(201).json({
      success: true,
      message: 'Media uploaded successfully',
      data: {
        id: result.insertId,
        file_path,
        file_type,
      },
    });
  } catch (error) {
    Logger.error('Failed to upload media', error);
    res.status(500).json({
      success: false,
      error: 'Failed to upload media',
    });
  }
}

/**
 * Stream media file
 * GET /api/media/stream/:media
 * Converted from ImageVideoController::stream
 */
export async function streamMedia(req: Request, res: Response): Promise<void> {
  try {
    const mediaId = req.params.media;

    const [media] = await query(
      `SELECT * FROM image_videos WHERE id = ?`,
      [mediaId]
    );

    if (!media) {
      res.status(404).json({
        success: false,
        error: 'Media not found',
      });
      return;
    }

    // In a real implementation, you would stream the file from storage
    // For now, return the media info
    res.status(200).json({
      success: true,
      data: media,
    });
  } catch (error) {
    Logger.error('Failed to stream media', error);
    res.status(500).json({
      success: false,
      error: 'Failed to stream media',
    });
  }
}

/**
 * Delete media
 * DELETE /api/media/:media
 * Converted from ImageVideoController::destroy
 */
export async function deleteMedia(req: Request, res: Response): Promise<void> {
  try {
    const mediaId = req.params.media;

    await query(`DELETE FROM image_videos WHERE id = ?`, [mediaId]);

    res.status(200).json({
      success: true,
      message: 'Media deleted successfully',
    });
  } catch (error) {
    Logger.error('Failed to delete media', error);
    res.status(500).json({
      success: false,
      error: 'Failed to delete media',
    });
  }
}

/**
 * Register beta interest
 * PUT /api/interest
 * Converted from BetaController::interestedAction
 */
export async function registerInterest(req: Request, res: Response): Promise<void> {
  try {
    const { email, name, interest_type } = req.body;

    await query(
      `INSERT INTO beta_interests (email, name, interest_type, created_at, updated_at)
       VALUES (?, ?, ?, NOW(), NOW())
       ON DUPLICATE KEY UPDATE updated_at = NOW()`,
      [email, name, interest_type]
    );

    res.status(200).json({
      success: true,
      message: 'Interest registered successfully',
    });
  } catch (error) {
    Logger.error('Failed to register interest', error);
    res.status(500).json({
      success: false,
      error: 'Failed to register interest',
    });
  }
}
