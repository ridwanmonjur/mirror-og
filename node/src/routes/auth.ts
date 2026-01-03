import { Router } from 'express';
import { handleAuthToken, handleHealthCheck } from '../controllers/authController';
import { authRateLimiter } from '../middleware/rateLimiter';

const router = Router();

/**
 * Auth Routes
 * Converted from cloud_client_auth/main.py
 */

// POST /auth/token - Create Firebase custom token and JWT
router.post('/token', authRateLimiter, handleAuthToken);

// GET /health - Health check endpoint
router.get('/health', handleHealthCheck);

export default router;
