import jwt from 'jsonwebtoken';
import { getJWTConfig } from '../../src/config/jwt';
import { UserRole } from '../../src/models/types';

/**
 * Test Authentication Helpers
 *
 * Generate mock JWT tokens for testing
 */

/**
 * Generate a test JWT token
 */
export function generateTestToken(userData: {
  id: string;
  role: UserRole;
  email?: string;
  name?: string;
}): string {
  const jwtConfig = getJWTConfig();

  const payload = {
    sub: userData.id,
    role: userData.role,
    email: userData.email || `user${userData.id}@test.com`,
    name: userData.name || `Test User ${userData.id}`,
    iat: Math.floor(Date.now() / 1000),
    exp: Math.floor(Date.now() / 1000) + 3600, // 1 hour expiration
  };

  return jwt.sign(payload, jwtConfig.secret, {
    algorithm: jwtConfig.algorithm,
  });
}

/**
 * Generate participant token
 */
export function generateParticipantToken(userId: string = '1'): string {
  return generateTestToken({
    id: userId,
    role: 'PARTICIPANT',
  });
}

/**
 * Generate organizer token
 */
export function generateOrganizerToken(userId: string = '2'): string {
  return generateTestToken({
    id: userId,
    role: 'ORGANIZER',
  });
}

/**
 * Generate admin token
 */
export function generateAdminToken(userId: string = '3'): string {
  return generateTestToken({
    id: userId,
    role: 'ADMIN',
  });
}

/**
 * Get authorization header with Bearer token
 */
export function getAuthHeader(token: string): { Authorization: string } {
  return {
    Authorization: `Bearer ${token}`,
  };
}
