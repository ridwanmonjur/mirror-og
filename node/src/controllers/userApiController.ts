import { Response } from 'express';
import { Logger } from '../utils/logger';
import { prisma } from '../config/database';
import { AuthenticatedRequest } from '../middleware/roleCheck';

/**
 * User API Controller (Prisma Version)
 * Converted from Laravel's UserController and SocialController
 * These endpoints require authentication
 */

/**
 * Get current authenticated user
 * GET /api/user
 */
export async function getCurrentUser(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);

    const user = await prisma.user.findUnique({
      where: { id: userId },
    });

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
 */
export async function viewNotifications(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);

    const notifications = await prisma.notification.findMany({
      where: { user_id: userId },
      orderBy: { created_at: 'desc' },
      take: 50,
    });

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
 */
export async function createNotification(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { user_id, title, message, type } = req.body;

    const result = await prisma.notification.create({
      data: {
        user_id: parseInt(user_id),
        title,
        message,
        type,
        read_at: null,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Notification created successfully',
      data: {
        id: result.id,
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
 */
export async function markNotificationAsRead(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const notificationId = parseInt(req.params.id);
    const userId = parseInt(req.user?.id!);

    await prisma.notification.updateMany({
      where: {
        id: notificationId,
        user_id: userId,
      },
      data: {
        read_at: new Date(),
      },
    });

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
 */
export async function changeSettings(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);
    const { settings } = req.body;

    await prisma.user.update({
      where: { id: userId },
      data: {
        settings: settings,
      },
    });

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
 */
export async function replaceBackground(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.params.id);
    const { background_url } = req.body;

    await prisma.user.update({
      where: { id: userId },
      data: {
        background_url,
      },
    });

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
 */
export async function toggleStar(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const targetUserId = parseInt(req.params.id);
    const userId = parseInt(req.user?.id!);

    // Check if already starred
    const existing = await prisma.userStar.findUnique({
      where: {
        user_id_starred_user_id: {
          user_id: userId,
          starred_user_id: targetUserId,
        },
      },
    });

    if (existing) {
      // Unstar
      await prisma.userStar.delete({
        where: {
          user_id_starred_user_id: {
            user_id: userId,
            starred_user_id: targetUserId,
          },
        },
      });

      res.status(200).json({
        success: true,
        message: 'User unstarred',
        starred: false,
      });
    } else {
      // Star
      await prisma.userStar.create({
        data: {
          user_id: userId,
          starred_user_id: targetUserId,
        },
      });

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
 */
export async function reportUser(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const targetUserId = parseInt(req.params.id);
    const reporterId = parseInt(req.user?.id!);
    const { reason, description } = req.body;

    const result = await prisma.userReport.create({
      data: {
        reporter_id: reporterId,
        reported_user_id: targetUserId,
        reason,
        description: description || null,
        status: 'pending',
      },
    });

    res.status(201).json({
      success: true,
      message: 'User reported successfully',
      data: {
        id: result.id,
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
 */
export async function getReports(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.params.id);

    const reports = await prisma.userReport.findMany({
      where: { reported_user_id: userId },
      include: {
        reporter: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
      orderBy: { created_at: 'desc' },
    });

    // Format response to match Laravel format
    const formattedReports = reports.map((report) => ({
      ...report,
      reporter_name: report.reporter.name,
    }));

    res.status(200).json({
      success: true,
      data: formattedReports,
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
 */
export async function searchParticipants(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { search_term, limit = 20 } = req.body;

    const participants = await prisma.user.findMany({
      where: {
        role: 'PARTICIPANT',
        OR: [
          { name: { contains: search_term } },
          { email: { contains: search_term } },
        ],
      },
      take: parseInt(limit as string),
    });

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
 */
export async function unlinkBankAccount(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);

    await prisma.user.update({
      where: { id: userId },
      data: {
        bank_account_id: null,
      },
    });

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
