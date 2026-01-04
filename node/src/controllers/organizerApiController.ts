import { Response } from 'express';
import { Logger } from '../utils/logger';
import { prisma } from '../config/database';
import { AuthenticatedRequest } from '../middleware/roleCheck';

/**
 * Organizer API Controller (Prisma Version)
 * Converted from Laravel's OrganizerController, OrganizerEventController, and OrganizerEventResultsController
 * These endpoints require organizer or admin role
 */

/**
 * Search events
 * POST /api/organizer/events/search
 */
export async function searchEvents(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { search_term, filters, page = 1, limit = 20 } = req.body;
    const skip = (page - 1) * limit;

    const where: any = {
      user_id: parseInt(userId!),
    };

    if (search_term) {
      where.OR = [
        { eventName: { contains: search_term } },
        { description: { contains: search_term } },
      ];
    }

    if (filters?.game_id) {
      where.game_id = parseInt(filters.game_id);
    }

    if (filters?.status) {
      where.status = filters.status;
    }

    const events = await prisma.eventDetail.findMany({
      where,
      orderBy: {
        start_date: 'desc',
      },
      skip,
      take: limit,
    });

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
 */
export async function destroyEvent(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const userId = parseInt(req.user?.id!);

    // Verify user owns this event
    const event = await prisma.eventDetail.findFirst({
      where: {
        id: eventId,
        user_id: userId,
      },
    });

    if (!event) {
      res.status(404).json({
        success: false,
        error: 'Event not found or you do not have permission to delete it',
      });
      return;
    }

    await prisma.eventDetail.delete({
      where: { id: eventId },
    });

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
 */
export async function storeResults(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { team_id, placement, prize_amount } = req.body;

    const result = await prisma.eventResult.upsert({
      where: {
        event_id_team_id: {
          event_id: eventId,
          team_id: parseInt(team_id),
        },
      },
      update: {
        placement: parseInt(placement),
        prize_amount: prize_amount ? parseFloat(prize_amount) : null,
      },
      create: {
        event_id: eventId,
        team_id: parseInt(team_id),
        placement: parseInt(placement),
        prize_amount: prize_amount ? parseFloat(prize_amount) : null,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Results stored successfully',
      data: {
        id: result.id,
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
 */
export async function storeNotification(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { title, message, recipient_type } = req.body;

    // Get recipients based on type
    let recipients: { user_id: number }[] = [];

    if (recipient_type === 'all_participants') {
      // Note: This would require an event_participants table
      // For now, we'll create a placeholder implementation
      // In a real scenario, you'd have a relation table for participants
      recipients = [];
    }

    // Create notifications for each recipient
    if (recipients.length > 0) {
      await prisma.notification.createMany({
        data: recipients.map((recipient) => ({
          user_id: recipient.user_id,
          title,
          message,
          type: 'event_notification',
          event_id: eventId,
        })),
      });
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
 */
export async function upsertBracket(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { match_id, team1_id, team2_id, winner_id, status, round } = req.body;

    const result = await prisma.eventMatch.upsert({
      where: {
        event_id_match_id: {
          event_id: eventId,
          match_id: match_id,
        },
      },
      update: {
        team1_id,
        team2_id,
        winner_id,
        status,
        round: round ? parseInt(round) : null,
      },
      create: {
        event_id: eventId,
        match_id,
        team1_id,
        team2_id,
        winner_id,
        status,
        round: round ? parseInt(round) : null,
      },
    });

    res.status(200).json({
      success: true,
      message: 'Bracket updated successfully',
      data: {
        id: result.id,
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
 */
export async function storeAward(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { team_id, user_id, award_type, award_name, award_value } = req.body;

    const result = await prisma.eventAward.create({
      data: {
        event_id: eventId,
        team_id: team_id ? parseInt(team_id) : null,
        user_id: user_id ? parseInt(user_id) : null,
        award_type,
        award_name,
        award_value: award_value ? parseFloat(award_value) : null,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Award created successfully',
      data: {
        id: result.id,
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
 */
export async function destroyAward(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const awardId = parseInt(req.params.awardId);

    await prisma.eventAward.delete({
      where: {
        id: awardId,
        event_id: eventId,
      },
    });

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
 */
export async function storeAchievements(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const eventId = parseInt(req.params.id);
    const { user_id, achievement_type, achievement_name, achievement_data } = req.body;

    const result = await prisma.userAchievement.create({
      data: {
        user_id: parseInt(user_id),
        event_id: eventId,
        achievement_type,
        achievement_name,
        achievement_data: achievement_data || null,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Achievement created successfully',
      data: {
        id: result.id,
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
 */
export async function destroyAchievements(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const achievementId = parseInt(req.params.achievementId);

    await prisma.userAchievement.delete({
      where: { id: achievementId },
    });

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
 */
export async function editProfile(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);
    const { name, bio, company_name, website, avatar_url, social_links } = req.body;

    await prisma.user.update({
      where: { id: userId },
      data: {
        name,
        bio,
        company_name,
        website,
        avatar_url,
        social_links: social_links || null,
      },
    });

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
