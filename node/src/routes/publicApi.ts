import { Router } from 'express';
import {
  getActivityLogs,
  getConnections,
  storeEventInvitation,
  destroyEventInvitation,
  uploadMedia,
  streamMedia,
  deleteMedia,
  registerInterest,
} from '../controllers/publicApiController';

const router = Router();

/**
 * Public API Routes (no authentication required)
 * Converted from Laravel's api.php public routes
 */

// User activity and connections
router.get('/user/:id/logs', getActivityLogs);
router.get('/user/:id/connections', getConnections);

// Event invitations
router.post('/event/:id/invitation', storeEventInvitation);
router.post('/event/:id/inviteDestroy', destroyEventInvitation);

// Media endpoints
router.post('/media', uploadMedia);
router.get('/media/stream/:media', streamMedia);
router.delete('/media/:media', deleteMedia);

// Beta interest
router.put('/interest', registerInterest);

export default router;
