import { Request, Response, NextFunction } from 'express';
import jwt from 'jsonwebtoken';
import { getJWTConfig } from '../config/jwt';
import { JWTUser } from '../models/types';
import { Logger } from '../utils/logger';

// Extend Express Request to include user
declare global {
  namespace Express {
    interface Request {
      user?: JWTUser;
    }
  }
}

/**
 * JWT Authentication Middleware
 *
 * Validates JWT token from Authorization header and attaches user to request
 */
export async function authenticateJWT(
  req: Request,
  res: Response,
  next: NextFunction
): Promise<void> {
  try {
    // Extract token from Authorization header
    const authHeader = req.headers.authorization;

    if (!authHeader) {
      res.status(401).json({
        success: false,
        message: 'No authorization header provided',
      });
      return;
    }

    // Expected format: "Bearer <token>"
    const parts = authHeader.split(' ');

    if (parts.length !== 2 || parts[0] !== 'Bearer') {
      res.status(401).json({
        success: false,
        message: 'Invalid authorization header format. Expected: Bearer <token>',
      });
      return;
    }

    const token = parts[1];

    // Get JWT config (handles base64 decoding of Laravel APP_KEY)
    const jwtConfig = getJWTConfig();

    // Verify token
    const decoded = jwt.verify(token, jwtConfig.secret, {
      algorithms: [jwtConfig.algorithm],
    }) as any;

    // Extract user data from JWT payload
    // Laravel JWT structure may vary, adjust based on actual token structure
    const user: JWTUser = {
      id: decoded.sub || decoded.user_id || decoded.id,
      role: decoded.role || 'PARTICIPANT',
      email: decoded.email,
      name: decoded.name,
    };

    if (!user.id) {
      Logger.error('JWT token missing user ID', { decoded });
      res.status(401).json({
        success: false,
        message: 'Invalid token payload: missing user ID',
      });
      return;
    }

    // Attach user to request
    req.user = user;

    next();
  } catch (error) {
    if (error instanceof jwt.JsonWebTokenError) {
      Logger.warn('Invalid JWT token', { error: error.message });
      res.status(401).json({
        success: false,
        message: 'Invalid token',
        error: error.message,
      });
      return;
    }

    if (error instanceof jwt.TokenExpiredError) {
      Logger.warn('Expired JWT token');
      res.status(401).json({
        success: false,
        message: 'Token expired',
      });
      return;
    }

    Logger.error('JWT authentication error', error);
    res.status(500).json({
      success: false,
      message: 'Authentication error',
    });
  }
}

/**
 * Middleware to require specific role
 */
export function requireRole(...roles: string[]) {
  return (req: Request, res: Response, next: NextFunction) => {
    if (!req.user) {
      res.status(401).json({
        success: false,
        message: 'Authentication required',
      });
      return;
    }

    if (!roles.includes(req.user.role)) {
      res.status(403).json({
        success: false,
        message: `Access denied. Required role: ${roles.join(' or ')}`,
      });
      return;
    }

    next();
  };
}
