import { Router } from 'express';
import { authenticateJWT } from '../middleware/auth';
import { organizerOrAdmin } from '../middleware/roleCheck';
import {
  searchEvents,
  destroyEvent,
  storeResults,
  storeNotification,
  upsertBracket,
  storeAward,
  destroyAward,
  storeAchievements,
  destroyAchievements,
  editProfile,
} from '../controllers/organizerApiController';

const router = Router();

/**
 * Organizer API Routes
 * Converted from Laravel's api.php organizer routes
 * Requires: organizer|admin role
 */

// Apply authentication and role check to all routes
router.use(authenticateJWT);
router.use(organizerOrAdmin);

// Event search and management
router.post('/events/search', searchEvents);
router.post('/event/:id/destroy', destroyEvent);

// Event results and notifications
router.post('/event/:id/results', storeResults);
router.post('/event/:id/notifications', storeNotification);

// Bracket/match management
router.post('/event/:id/matches', upsertBracket);

// Awards
router.post('/event/:id/awards', storeAward);
router.delete('/event/:id/awards/:awardId', destroyAward);

// Achievements
router.post('/event/:id/achievements', storeAchievements);
router.delete('/event/achievements/:achievementId', destroyAchievements);

// Profile
router.post('/profile', editProfile);

export default router;
