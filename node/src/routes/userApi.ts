import { Router } from 'express';
import { authenticateJWT } from '../middleware/auth';
import { anyAuthenticatedRole } from '../middleware/roleCheck';
import {
  getCurrentUser,
  viewNotifications,
  createNotification,
  markNotificationAsRead,
  changeSettings,
  replaceBackground,
  toggleStar,
  reportUser,
  getReports,
  searchParticipants,
  unlinkBankAccount,
} from '../controllers/userApiController';
import { likeEvent, validateBracket, searchTeams, getTeamList } from '../controllers/participantApiController';

const router = Router();

/**
 * User API Routes (authenticated, any role)
 * Converted from Laravel's api.php authenticated routes
 * Requires: participant|organizer|admin role
 */

// Apply authentication and role check to all routes
router.use(authenticateJWT);
router.use(anyAuthenticatedRole);

// Current user
router.get('/user', getCurrentUser);

// Teams
router.get('/teams/search', searchTeams);
router.post('/teams/list', getTeamList);

// Event bracket validation
router.post('/event/:id/brackets', validateBracket);

// User reports
router.get('/user/:id/reports', getReports);

// Notifications
router.get('/user/notifications', viewNotifications);
router.post('/user/notifications', createNotification);
router.post('/user/notifications/:id', markNotificationAsRead);

// User settings and profile
router.post('/user/settings', changeSettings);
router.post('/user/:id/background', replaceBackground);

// Social actions
router.post('/user/:id/star', toggleStar);
router.post('/user/:id/report', reportUser);
router.post('/user/likes', likeEvent);

// User search
router.post('/user/participants', searchParticipants);

// Bank account
router.post('/user/unlink', unlinkBankAccount);

// Note: The following endpoints are placeholders and would need additional implementation:
// - POST /user/withdraw - StripeController::processWithdrawal
// - POST /user/:id/block - FirebaseController::toggleBlock
// - POST /user/firebase - ChatController::getFirebaseUsers
// - POST /card/intent - StripeController::stripeCardIntentCreate

export default router;
