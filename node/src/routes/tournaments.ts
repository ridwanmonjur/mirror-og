import { Router } from 'express';
import {
  handleRoomBlock,
  createBatchReports,
  createBatchDisputes,
  handleStartedTasks,
  handleEndedTasks,
  handleOrgTasks,
  getMatchResult,
  getAllMatchResults,
  handleTournamentHealthCheck,
} from '../controllers/tournamentController';
import { tournamentRateLimiter, batchRateLimiter, deadlineRateLimiter } from '../middleware/rateLimiter';

const router = Router();

/**
 * Tournament Routes
 * Converted from cloud_server_functions/main.py
 */

// Room block/unblock endpoint
router.post('/room/block', tournamentRateLimiter, handleRoomBlock);

// Batch operations endpoints
router.post('/batch/reports', batchRateLimiter, createBatchReports);
router.post('/batch/disputes', batchRateLimiter, createBatchDisputes);

// Deadline processing endpoints
router.post('/deadline/started', deadlineRateLimiter, handleStartedTasks);
router.post('/deadline/ended', deadlineRateLimiter, handleEndedTasks);
router.post('/deadline/org', deadlineRateLimiter, handleOrgTasks);

// Match result endpoints
router.post('/match/result', tournamentRateLimiter, getMatchResult);
router.post('/match/results/all', tournamentRateLimiter, getAllMatchResults);

// Health check endpoint
router.get('/health', handleTournamentHealthCheck);

export default router;
