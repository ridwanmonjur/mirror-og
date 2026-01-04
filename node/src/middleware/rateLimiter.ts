import rateLimit from 'express-rate-limit';
import { Request, Response } from 'express';

/**
 * Rate limiter for authentication endpoints
 * Allows 30 requests per minute per IP
 */
export const authRateLimiter = rateLimit({
  windowMs: 60 * 1000, // 1 minute
  max: 30, // 30 requests per windowMs
  message: { error: 'Too Many Requests' },
  standardHeaders: true,
  legacyHeaders: false,
  handler: (_req: Request, res: Response) => {
    res.status(429).json({ error: 'Too Many Requests' });
  },
});

/**
 * Rate limiter for tournament endpoints
 * Allows 60 requests per minute per IP
 */
export const tournamentRateLimiter = rateLimit({
  windowMs: 60 * 1000, // 1 minute
  max: 60,
  message: { error: 'Too Many Requests' },
  standardHeaders: true,
  legacyHeaders: false,
  handler: (_req: Request, res: Response) => {
    res.status(429).json({ error: 'Too Many Requests' });
  },
});

/**
 * Strict rate limiter for batch operations
 * Allows 20 requests per minute per IP
 */
export const batchRateLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: 20,
  message: { error: 'Too Many Requests' },
  standardHeaders: true,
  legacyHeaders: false,
  handler: (_req: Request, res: Response) => {
    res.status(429).json({ error: 'Too Many Requests' });
  },
});

/**
 * Rate limiter for deadline processing endpoints
 * Allows 10 requests per minute per IP
 */
export const deadlineRateLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: 10,
  message: { error: 'Too Many Requests' },
  standardHeaders: true,
  legacyHeaders: false,
  handler: (_req: Request, res: Response) => {
    res.status(429).json({ error: 'Too Many Requests' });
  },
});
