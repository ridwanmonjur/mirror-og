import { Response } from 'express';
import { Logger } from '../utils/logger';
import { query } from '../config/database';
import { AuthenticatedRequest } from '../middleware/roleCheck';

/**
 * Participant API Controller
 * Converted from Laravel's ParticipantController, ParticipantEventController, and ParticipantTeamController
 * These endpoints require participant or admin role
 */

/**
 * Get events list with filters
 * POST /api/participant/events
 * Converted from ParticipantEventController::index
 */
export async function getEvents(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { filters, page = 1, limit = 20 } = req.body;
    const offset = (page - 1) * limit;

    let whereClause = 'WHERE 1=1';
    const params: any[] = [];

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
 * Converted from ParticipantEventController::likeEvent
 */
export async function likeEvent(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { event_id } = req.body;

    // Check if already liked
    const [existing] = await query(
      `SELECT * FROM event_likes WHERE user_id = ? AND event_id = ?`,
      [userId, event_id]
    );

    if (existing) {
      // Unlike
      await query(
        `DELETE FROM event_likes WHERE user_id = ? AND event_id = ?`,
        [userId, event_id]
      );

      res.status(200).json({
        success: true,
        message: 'Event unliked',
        liked: false,
      });
    } else {
      // Like
      await query(
        `INSERT INTO event_likes (user_id, event_id, created_at, updated_at)
         VALUES (?, ?, NOW(), NOW())`,
        [userId, event_id]
      );

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
 * Converted from SocialController::followOrganizer
 */
export async function followOrganizer(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { organizer_id } = req.body;

    // Check if already following
    const [existing] = await query(
      `SELECT * FROM organizer_followers WHERE follower_id = ? AND organizer_id = ?`,
      [userId, organizer_id]
    );

    if (existing) {
      // Unfollow
      await query(
        `DELETE FROM organizer_followers WHERE follower_id = ? AND organizer_id = ?`,
        [userId, organizer_id]
      );

      res.status(200).json({
        success: true,
        message: 'Organizer unfollowed',
        following: false,
      });
    } else {
      // Follow
      await query(
        `INSERT INTO organizer_followers (follower_id, organizer_id, created_at, updated_at)
         VALUES (?, ?, NOW(), NOW())`,
        [userId, organizer_id]
      );

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
 * Converted from ParticipantController::editProfile
 */
export async function editProfile(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { name, bio, avatar_url, social_links } = req.body;

    await query(
      `UPDATE users
       SET name = ?, bio = ?, avatar_url = ?, social_links = ?, updated_at = NOW()
       WHERE id = ?`,
      [name, bio, avatar_url, JSON.stringify(social_links), userId]
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

/**
 * Search teams
 * GET /api/teams/search
 * Converted from ParticipantTeamController::search
 */
export async function searchTeams(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { search_term, limit = 20 } = req.query;

    const teams = await query(
      `SELECT * FROM teams
       WHERE name LIKE ?
       LIMIT ?`,
      [`%${search_term}%`, limit]
    );

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
 * Converted from ParticipantTeamController::teamList
 */
export async function getTeamList(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const { user_id, page = 1, limit = 20 } = req.body;
    const offset = (page - 1) * limit;

    const teams = await query(
      `SELECT t.* FROM teams t
       INNER JOIN team_members tm ON t.id = tm.team_id
       WHERE tm.user_id = ?
       LIMIT ? OFFSET ?`,
      [user_id, limit, offset]
    );

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
 * Converted from ParticipantTeamController::editTeam
 */
export async function editTeam(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const userId = req.user?.id;
    const { team_id, name, description, logo_url } = req.body;

    // Verify user is team captain
    const [member] = await query(
      `SELECT * FROM team_members
       WHERE team_id = ? AND user_id = ? AND is_captain = 1`,
      [team_id, userId]
    );

    if (!member) {
      res.status(403).json({
        success: false,
        error: 'Only team captains can edit the team',
      });
      return;
    }

    await query(
      `UPDATE teams
       SET name = ?, description = ?, logo_url = ?, updated_at = NOW()
       WHERE id = ?`,
      [name, description, logo_url, team_id]
    );

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
 * Converted from ParticipantTeamController::inviteMember
 */
export async function inviteMember(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const teamId = req.params.id;
    const invitedUserId = req.params.userId;
    const currentUserId = req.user?.id;

    // Verify current user is team captain
    const [member] = await query(
      `SELECT * FROM team_members
       WHERE team_id = ? AND user_id = ? AND is_captain = 1`,
      [teamId, currentUserId]
    );

    if (!member) {
      res.status(403).json({
        success: false,
        error: 'Only team captains can invite members',
      });
      return;
    }

    // Create invitation
    const result = await query(
      `INSERT INTO team_invitations (team_id, invited_user_id, invited_by, status, created_at, updated_at)
       VALUES (?, ?, ?, 'pending', NOW(), NOW())`,
      [teamId, invitedUserId, currentUserId]
    );

    res.status(201).json({
      success: true,
      message: 'Member invited successfully',
      data: {
        id: result.insertId,
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
 * Converted from ParticipantTeamController::captainMember
 */
export async function makeCaptain(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const teamId = req.params.id;
    const memberId = req.params.memberId;
    const currentUserId = req.user?.id;

    // Verify current user is team captain
    const [currentMember] = await query(
      `SELECT * FROM team_members
       WHERE team_id = ? AND user_id = ? AND is_captain = 1`,
      [teamId, currentUserId]
    );

    if (!currentMember) {
      res.status(403).json({
        success: false,
        error: 'Only team captains can assign captain role',
      });
      return;
    }

    // Make member captain
    await query(
      `UPDATE team_members
       SET is_captain = 1, updated_at = NOW()
       WHERE team_id = ? AND user_id = ?`,
      [teamId, memberId]
    );

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
 * Converted from ParticipantTeamController::deleteCaptain
 */
export async function removeCaptain(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const teamId = req.params.id;
    const memberId = req.params.memberId;

    await query(
      `UPDATE team_members
       SET is_captain = 0, updated_at = NOW()
       WHERE team_id = ? AND user_id = ?`,
      [teamId, memberId]
    );

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
 * Converted from ParticipantTeamController::updateTeamMember
 */
export async function updateTeamMember(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const memberId = req.params.id;
    const { role, status } = req.body;

    await query(
      `UPDATE team_members
       SET role = ?, status = ?, updated_at = NOW()
       WHERE id = ?`,
      [role, status, memberId]
    );

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
 * Converted from ParticipantTeamController::withdrawInviteMember
 */
export async function withdrawInvite(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const inviteId = req.params.id;

    await query(
      `DELETE FROM team_invitations WHERE id = ?`,
      [inviteId]
    );

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
 * Converted from ParticipantTeamController::rejectInviteMember
 */
export async function rejectInvite(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    const inviteId = req.params.id;

    await query(
      `UPDATE team_invitations
       SET status = 'rejected', updated_at = NOW()
       WHERE id = ?`,
      [inviteId]
    );

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
 * Converted from ParticipantEventController::validateBracket
 */
export async function validateBracket(req: AuthenticatedRequest, res: Response): Promise<void> {
  try {
    // const eventId = req.params.id; // Reserved for future use
    const { bracket_data } = req.body;

    // Validate bracket structure
    // This is a placeholder - actual validation logic would be more complex
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
