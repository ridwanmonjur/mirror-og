import { Response } from 'express';
import { Logger } from '../utils/logger';
import { prisma } from '../config/database';
import { AuthenticatedRequest } from '../middleware/roleCheck';

/**
 * Participant API Controller (Prisma Version)
 * Converted from Laravel's ParticipantController, ParticipantEventController, and ParticipantTeamController
 * These endpoints require participant or admin role
 */

/**
 * Get events list with filters
 * POST /api/participant/events
 */
export async function getEvents(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { filters, page = 1, limit = 20 } = req.body;
    const skip = (page - 1) * limit;

    const where: any = {};

    if (filters?.game_id) {
      where.game_id = parseInt(filters.game_id);
    }

    if (filters?.status) {
      where.status = filters.status;
    }

    const events = await prisma.event_details.findMany({
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
    Logger.error('Failed to get events', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve events',
    });
  }
}

/**
 * Like/unlike an event
 * POST /api/user/likes
 */
export async function likeEvent(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);
    const { event_id } = req.body;
    const eventId = parseInt(event_id);

    // Check if already liked
    const existing = await prisma.eventLike.findUnique({
      where: {
        user_id_event_id: {
          user_id: userId,
          event_id: eventId,
        },
      },
    });

    if (existing) {
      // Unlike
      await prisma.eventLike.delete({
        where: {
          user_id_event_id: {
            user_id: userId,
            event_id: eventId,
          },
        },
      });

      res.status(200).json({
        success: true,
        message: 'Event unliked',
        liked: false,
      });
    } else {
      // Like
      await prisma.eventLike.create({
        data: {
          user_id: userId,
          event_id: eventId,
        },
      });

      res.status(200).json({
        success: true,
        message: 'Event liked',
        liked: true,
      });
    }
  } catch (error) {
    Logger.error('Failed to like event', error);
    res.status(500).json({
      success: false,
      error: 'Failed to like event',
    });
  }
}

/**
 * Follow/unfollow organizer
 * POST /api/participant/organizer/follow
 */
export async function followOrganizer(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);
    const { organizer_id } = req.body;
    const organizerId = parseInt(organizer_id);

    // Check if already following
    const existing = await prisma.organizerFollower.findUnique({
      where: {
        follower_id_organizer_id: {
          follower_id: userId,
          organizer_id: organizerId,
        },
      },
    });

    if (existing) {
      // Unfollow
      await prisma.organizerFollower.delete({
        where: {
          follower_id_organizer_id: {
            follower_id: userId,
            organizer_id: organizerId,
          },
        },
      });

      res.status(200).json({
        success: true,
        message: 'Organizer unfollowed',
        following: false,
      });
    } else {
      // Follow
      await prisma.organizerFollower.create({
        data: {
          follower_id: userId,
          organizer_id: organizerId,
        },
      });

      res.status(200).json({
        success: true,
        message: 'Organizer followed',
        following: true,
      });
    }
  } catch (error) {
    Logger.error('Failed to follow organizer', error);
    res.status(500).json({
      success: false,
      error: 'Failed to follow organizer',
    });
  }
}

/**
 * Edit participant profile
 * POST /api/participant/profile
 */
export async function editProfile(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);
    const { name, bio, avatar_url, social_links } = req.body;

    await prisma.users.update({
      where: { id: userId },
      data: {
        name,
        bio,
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

/**
 * Search teams
 * GET /api/teams/search
 */
export async function searchTeams(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { search_term, limit = 20 } = req.query;

    const teams = await prisma.teams.findMany({
      where: {
        teamName: {
          contains: search_term as string,
        },
      },
      take: parseInt(limit as string),
    });

    res.status(200).json({
      success: true,
      data: teams,
    });
  } catch (error) {
    Logger.error('Failed to search teams', error);
    res.status(500).json({
      success: false,
      error: 'Failed to search teams',
    });
  }
}

/**
 * Get team list
 * POST /api/teams/list
 */
export async function getTeamList(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { user_id, page = 1, limit = 20 } = req.body;
    const skip = (page - 1) * limit;

    const teams = await prisma.teams.findMany({
      where: {
        team_members: {
          some: {
            user_id: parseInt(user_id),
          },
        },
      },
      skip,
      take: limit,
    });

    res.status(200).json({
      success: true,
      data: teams,
    });
  } catch (error) {
    Logger.error('Failed to get team list', error);
    res.status(500).json({
      success: false,
      error: 'Failed to retrieve teams',
    });
  }
}

/**
 * Edit team
 * POST /api/participant/team
 */
export async function editTeam(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = parseInt(req.user?.id!);
    const { team_id, name, description, logo_url } = req.body;
    const teamId = parseInt(team_id);

    // Verify user is team captain
    const member = await prisma.team_members.findFirst({
      where: {
        team_id: teamId,
        user_id: userId,
        is_captain: true,
      },
    });

    if (!member) {
      res.status(403).json({
        success: false,
        error: 'Only team captains can edit the team',
      });
      return;
    }

    await prisma.teams.update({
      where: { id: teamId },
      data: {
        teamName: name,
        description,
        logo_url,
      },
    });

    res.status(200).json({
      success: true,
      message: 'Team updated successfully',
    });
  } catch (error) {
    Logger.error('Failed to edit team', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update team',
    });
  }
}

/**
 * Invite member to team
 * POST /api/participant/team/:id/user/:userId/invite
 */
export async function inviteMember(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const teamId = parseInt(req.params.id);
    const invitedUserId = parseInt(req.params.userId);
    const currentUserId = parseInt(req.user?.id!);

    // Verify current user is team captain
    const member = await prisma.team_members.findFirst({
      where: {
        team_id: teamId,
        user_id: currentUserId,
        is_captain: true,
      },
    });

    if (!member) {
      res.status(403).json({
        success: false,
        error: 'Only team captains can invite members',
      });
      return;
    }

    // Create invitation
    const result = await prisma.teamInvitation.create({
      data: {
        team_id: teamId,
        invited_user_id: invitedUserId,
        invited_by: currentUserId,
        status: 'pending',
      },
    });

    res.status(201).json({
      success: true,
      message: 'Member invited successfully',
      data: {
        id: result.id,
      },
    });
  } catch (error) {
    Logger.error('Failed to invite member', error);
    res.status(500).json({
      success: false,
      error: 'Failed to invite member',
    });
  }
}

/**
 * Make member captain
 * POST /api/participant/team/:id/member/:memberId/captain
 */
export async function makeCaptain(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const teamId = parseInt(req.params.id);
    const memberId = parseInt(req.params.memberId);
    const currentUserId = parseInt(req.user?.id!);

    // Verify current user is team captain
    const currentMember = await prisma.team_members.findFirst({
      where: {
        team_id: teamId,
        user_id: currentUserId,
        is_captain: true,
      },
    });

    if (!currentMember) {
      res.status(403).json({
        success: false,
        error: 'Only team captains can assign captain role',
      });
      return;
    }

    // Make member captain
    await prisma.team_members.updateMany({
      where: {
        team_id: teamId,
        user_id: memberId,
      },
      data: {
        is_captain: true,
      },
    });

    res.status(200).json({
      success: true,
      message: 'Member promoted to captain',
    });
  } catch (error) {
    Logger.error('Failed to make captain', error);
    res.status(500).json({
      success: false,
      error: 'Failed to promote member',
    });
  }
}

/**
 * Remove captain role from member
 * POST /api/participant/team/:id/member/:memberId/deleteCaptain
 */
export async function removeCaptain(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const teamId = parseInt(req.params.id);
    const memberId = parseInt(req.params.memberId);

    await prisma.team_members.updateMany({
      where: {
        team_id: teamId,
        user_id: memberId,
      },
      data: {
        is_captain: false,
      },
    });

    res.status(200).json({
      success: true,
      message: 'Captain role removed',
    });
  } catch (error) {
    Logger.error('Failed to remove captain', error);
    res.status(500).json({
      success: false,
      error: 'Failed to remove captain role',
    });
  }
}

/**
 * Update team member
 * POST /api/participant/team/member/:id/update
 */
export async function updateTeamMember(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const memberId = parseInt(req.params.id);
    const { role, status } = req.body;

    await prisma.team_members.update({
      where: { id: memberId },
      data: {
        role: role || undefined,
        status: status || undefined,
      },
    });

    res.status(200).json({
      success: true,
      message: 'Team member updated successfully',
    });
  } catch (error) {
    Logger.error('Failed to update team member', error);
    res.status(500).json({
      success: false,
      error: 'Failed to update team member',
    });
  }
}

/**
 * Withdraw invitation
 * POST /api/participant/team/member/:id/deleteInvite
 */
export async function withdrawInvite(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const inviteId = parseInt(req.params.id);

    await prisma.teamInvitation.delete({
      where: { id: inviteId },
    });

    res.status(200).json({
      success: true,
      message: 'Invitation withdrawn',
    });
  } catch (error) {
    Logger.error('Failed to withdraw invite', error);
    res.status(500).json({
      success: false,
      error: 'Failed to withdraw invitation',
    });
  }
}

/**
 * Reject invitation
 * POST /api/participant/team/member/:id/rejectInvite
 */
export async function rejectInvite(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const inviteId = parseInt(req.params.id);

    await prisma.teamInvitation.update({
      where: { id: inviteId },
      data: {
        status: 'rejected',
      },
    });

    res.status(200).json({
      success: true,
      message: 'Invitation rejected',
    });
  } catch (error) {
    Logger.error('Failed to reject invite', error);
    res.status(500).json({
      success: false,
      error: 'Failed to reject invitation',
    });
  }
}

/**
 * Validate bracket for event
 * POST /api/event/:id/brackets
 */
export async function validateBracket(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { bracket_data } = req.body;

    // Validate bracket structure
    const isValid = bracket_data && bracket_data.matches && Array.isArray(bracket_data.matches);

    res.status(200).json({
      success: true,
      valid: isValid,
      message: isValid ? 'Bracket is valid' : 'Invalid bracket structure',
    });
  } catch (error) {
    Logger.error('Failed to validate bracket', error);
    res.status(500).json({
      success: false,
      error: 'Failed to validate bracket',
    });
  }
}
