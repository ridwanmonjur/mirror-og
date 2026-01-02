import { Router } from 'express';
import { authenticateJWT } from '../middleware/auth';
import { asyncHandler } from '../middleware/errorHandler';
import bracketController from '../controllers/bracketController';
import disputeController from '../controllers/disputeController';

const router = Router();

/**
 * Bracket Routes
 *
 * All routes require JWT authentication
 */

// Report match results
// POST /api/brackets/:eventId/report
router.post(
  '/:eventId/report',
  authenticateJWT,
  asyncHandler(bracketController.reportMatchResults.bind(bracketController))
);

// Submit dispute
// POST /api/brackets/:eventId/disputes
router.post(
  '/:eventId/disputes',
  authenticateJWT,
  asyncHandler(disputeController.submitDispute.bind(disputeController))
);

// Respond to dispute
// PATCH /api/brackets/:eventId/disputes/:disputeId/respond
router.patch(
  '/:eventId/disputes/:disputeId/respond',
  authenticateJWT,
  asyncHandler(disputeController.respondToDispute.bind(disputeController))
);

// Resolve dispute
// PATCH /api/brackets/:eventId/disputes/:disputeId/resolve
router.patch(
  '/:eventId/disputes/:disputeId/resolve',
  authenticateJWT,
  asyncHandler(disputeController.resolveDispute.bind(disputeController))
);

export default router;
