import { Request, Response, NextFunction } from 'express';
import { Logger } from '../utils/logger';

/**
 * Extended Express Request with user info
 */
export interface AuthenticatedRequest extends Request {
  user?: {
    id: string;
    role: string;
    email?: string;
    [key: string]: any;
  };
}

/**
 * Middleware to check if user has required role(s)
 * Converted from Laravel's check-jwt-permission middleware
 */
export function checkRole(...allowedRoles: string[]) {
  return (req: AuthenticatedRequest, res: Response, next: NextFunction) => {
    try {
      // Check if user is authenticated
      if (!req.user) {
        res.status(401).json({ error: 'Unauthorized - Authentication required' });
        return;
      }

      // Get user role (normalize to uppercase)
      const userRole = req.user.role?.toUpperCase();

      if (!userRole) {
        res.status(403).json({ error: 'Forbidden - No role assigned' });
        return;
      }

      // Normalize allowed roles to uppercase
      const normalizedAllowedRoles = allowedRoles.map((role) => role.toUpperCase());

      // Check if user has one of the allowed roles
      if (!normalizedAllowedRoles.includes(userRole)) {
        Logger.log(`Role check failed: User has ${userRole}, requires one of: ${normalizedAllowedRoles.join(', ')}`);
        res.status(403).json({
          error: 'Forbidden - Insufficient permissions',
          required_roles: allowedRoles,
          user_role: userRole,
        });
        return;
      }

      // User has required role, continue
      next();
    } catch (error) {
      Logger.error('Error in role check middleware', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  };
}

/**
 * Middleware to check if user is participant or admin
 */
export const participantOrAdmin = checkRole('participant', 'admin');

/**
 * Middleware to check if user is organizer or admin
 */
export const organizerOrAdmin = checkRole('organizer', 'admin');

/**
 * Middleware to check if user is participant, organizer, or admin (any role)
 */
export const anyAuthenticatedRole = checkRole('participant', 'organizer', 'admin');

/**
 * Middleware to check if user is admin only
 */
export const adminOnly = checkRole('admin');
