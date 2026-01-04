import { Request, Response } from 'express';
import { Logger } from '../utils/logger';
import { prisma } from '../config/database';

/**
 * Public API Controller (Prisma Version)
 * Converted from Laravel's Open and Shared controllers
 * These endpoints don't require authentication
 */

/**
 * Get activity logs for a user
 * GET /api/user/:id/logs
 */
export async function getActivityLogs(req: Request, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.params.id);

    const logs = await prisma.activityLog.findMany({
      where: { user_id: userId },
      orderBy: { created_at: 'desc' },
      take: 50,
    });

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
 */
export async function getConnections(req: Request, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.params.id);

    // Get followers (users who follow this user as an organizer)
    const followersData = await prisma.organizerFollower.findMany({
      where: { organizer_id: userId },
      include: {
        follower: {
          select: {
            id: true,
            name: true,
            email: true,
            avatar_url: true,
          },
        },
      },
    });

    // Get following (organizers this user follows)
    const followingData = await prisma.organizerFollower.findMany({
      where: { follower_id: userId },
      include: {
        organizer: {
          select: {
            id: true,
            name: true,
            email: true,
            avatar_url: true,
          },
        },
      },
    });

    // Format response to match expected structure
    const followers = followersData.map((f) => f.follower);
    const following = followingData.map((f) => f.organizer);

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
 */
export async function storeEventInvitation(req: Request, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { team_id, user_id, message } = req.body;

    const result = await prisma.eventInvitation.create({
      data: {
        event_id: eventId,
        team_id: team_id ? parseInt(team_id) : null,
        user_id: user_id ? parseInt(user_id) : null,
        message: message || null,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Invitation sent successfully',
      data: {
        id: result.id,
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
 */
export async function destroyEventInvitation(req: Request, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { invitation_id } = req.body;

    await prisma.eventInvitation.deleteMany({
      where: {
        id: parseInt(invitation_id),
        event_id: eventId,
      },
    });

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
 */
export async function uploadMedia(req: Request, res: Response): Promise<void> {
  try {
    const { file_path, file_type, user_id } = req.body;

    const result = await prisma.imageVideo.create({
      data: {
        file_path,
        file_type,
        user_id: user_id ? parseInt(user_id) : null,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Media uploaded successfully',
      data: {
        id: result.id,
        file_path: result.file_path,
        file_type: result.file_type,
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
 */
export async function streamMedia(req: Request, res: Response): Promise<void> {
  try {
    const mediaId = parseInt(req.params.media);

    const media = await prisma.imageVideo.findUnique({
      where: { id: mediaId },
    });

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
 */
export async function deleteMedia(req: Request, res: Response): Promise<void> {
  try {
    const mediaId = parseInt(req.params.media);

    await prisma.imageVideo.delete({
      where: { id: mediaId },
    });

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
 */
export async function registerInterest(req: Request, res: Response): Promise<void> {
  try {
    const { email, name, interest_type } = req.body;

    await prisma.betaInterest.upsert({
      where: { email },
      update: {
        // Just update the timestamp
      },
      create: {
        email,
        name: name || null,
        interest_type: interest_type || null,
      },
    });

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
