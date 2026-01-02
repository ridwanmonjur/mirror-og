/**
 * JWT Configuration
 *
 * Laravel uses base64-encoded APP_KEY for JWT signing.
 * This module handles decoding and provides JWT configuration.
 */

interface JWTConfig {
  secret: string;
  algorithm: 'HS256' | 'HS384' | 'HS512';
}

/**
 * Decode Laravel's base64-encoded APP_KEY
 */
function decodeAppKey(appKey: string): string {
  // Laravel APP_KEY format: "base64:xxxxx"
  if (appKey.startsWith('base64:')) {
    const base64Part = appKey.substring(7);
    return Buffer.from(base64Part, 'base64').toString('utf8');
  }

  // If not base64 encoded, return as-is
  return appKey;
}

/**
 * Get JWT configuration
 */
export function getJWTConfig(): JWTConfig {
  const jwtSecret = process.env.JWT_SECRET;

  if (!jwtSecret) {
    throw new Error('JWT_SECRET is not set in environment variables');
  }

  const algorithm = (process.env.JWT_ALGORITHM as 'HS256' | 'HS384' | 'HS512') || 'HS256';

  return {
    secret: decodeAppKey(jwtSecret),
    algorithm,
  };
}

export default getJWTConfig;
