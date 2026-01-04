import { Router } from 'express';
import { authenticateJWT } from '../middleware/auth';
import { participantOrAdmin } from '../middleware/roleCheck';
import {
  getEvents,
  followOrganizer,
  editProfile,
  editTeam,
  inviteMember,
  makeCaptain,
  removeCaptain,
  updateTeamMember,
  withdrawInvite,
  rejectInvite,
} from '../controllers/participantApiController';

const router = Router();

/**
 * Participant API Routes
 * Converted from Laravel's api.php participant routes
 * Requires: participant|admin role
 */

// Apply authentication and role check to all routes
router.use(authenticateJWT);
router.use(participantOrAdmin);

// Events
router.post('/events', getEvents);

// Social
router.post('/organizer/follow', followOrganizer);

// Profile
router.post('/profile', editProfile);

// Team management
router.post('/team', editTeam);

// Team member invitations
router.post('/team/:id/user/:userId/invite', inviteMember);
router.post('/team/:id/member/:memberId/captain', makeCaptain);
router.post('/team/:id/member/:memberId/deleteCaptain', removeCaptain);

// Team member updates
router.post('/team/member/:id/update', updateTeamMember);
router.post('/team/member/:id/deleteInvite', withdrawInvite);
router.post('/team/member/:id/rejectInvite', rejectInvite);

export default router;
