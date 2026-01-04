import { Request, Response } from 'express';
import * as admin from 'firebase-admin';
import { SignJWT } from 'jose';
import { Logger } from '../utils/logger';

const SECRET_KEY = process.env.SECRET_KEY || 'your-secret-key';
const ALGORITHM = 'HS256';
const ACCESS_TOKEN_EXPIRE_MINUTES = 30;
const ENVIRONMENT = process.env.ENVIRONMENT || 'dev';

interface AuthTokenRequest {
  uid: string;
  role?: string;
  teamId?: string | number;
}

/**
 * Verify Firebase App Check token (disabled in development)
 * Currently commented out but kept for future implementation
 */
// function verifyAppCheckToken(appCheckToken: string | undefined): boolean {
//   if (!appCheckToken) {
//     return false;
//   }
//
//   try {
//     // In production, verify the App Check token
//     if (ENVIRONMENT === 'prod' || ENVIRONMENT === 'staging') {
//       // admin.appCheck().verifyToken(appCheckToken);
//       // Commented out for now - implement when needed
//     }
//     return true;
//   } catch (error) {
//     Logger.log('App Check verification failed', error);
//     return false;
//   }
// }

/**
 * Handle authentication token creation
 * POST /auth/token
 */
export async function handleAuthToken(req: Request, res: Response): Promise<void> {
  const clientIp = (req.headers['x-forwarded-for'] as string)?.split(',')[0]?.trim() ||
                   req.socket.remoteAddress ||
                   'unknown';

  try {
    // App Check verification disabled for development
    // const appCheckToken = req.headers['x-firebase-appcheck'] as string;
    // if (!verifyAppCheckToken(appCheckToken)) {
    //   Logger.log(`POST /auth/token 401 ${clientIp} - App Check verification failed`);
    //   res.status(401).json({ detail: 'App Check verification failed' });
    //   return;
    // }

    const requestBody = req.body as AuthTokenRequest;

    if (!requestBody || !requestBody.uid) {
      Logger.log(`POST /auth/token 400 ${clientIp} - uid is required`);
      res.status(400).json({ detail: 'uid is required' });
      return;
    }

    const uid = String(requestBody.uid);
    if (!uid) {
      Logger.log(`POST /auth/token 400 ${clientIp} - uid is required`);
      res.status(400).json({ detail: 'uid is required' });
      return;
    }

    // Get additional user data from request
    const role = requestBody.role || 'PARTICIPANT';
    const teamId = requestBody.teamId;

    // Create custom claims for Firebase token
    const customClaims = {
      uid: uid,
      source: 'driftwood-laravel',
      role: role,
      userId: uid,
      teamId: teamId,
    };

    // Create Firebase custom token
    const customToken = await admin.auth().createCustomToken(uid, customClaims);

    // Create JWT token
    const expire = new Date();
    expire.setMinutes(expire.getMinutes() + ACCESS_TOKEN_EXPIRE_MINUTES);

    const secret = new TextEncoder().encode(SECRET_KEY);
    const jwtToken = await new SignJWT({ uid })
      .setProtectedHeader({ alg: ALGORITHM })
      .setIssuedAt()
      .setExpirationTime(Math.floor(expire.getTime() / 1000))
      .sign(secret);

    const responseData = {
      token: customToken,
      jwt_token: jwtToken,
      expires_at: expire.toISOString(),
    };

    Logger.log(`POST /auth/token 200 ${clientIp}`);
    res.status(200).json(responseData);
  } catch (error) {
    Logger.error(`POST /auth/token 500 ${clientIp}`, error);
    res.status(500).json({ detail: 'Failed to create authentication token' });
  }
}

/**
 * Handle health check
 * GET /health
 */
export function handleHealthCheck(req: Request, res: Response): void {
  const clientIp = (req.headers['x-forwarded-for'] as string)?.split(',')[0]?.trim() ||
                   req.socket.remoteAddress ||
                   'unknown';

  const responseData = {
    status: 'healthy',
    service: 'driftwood-client-auth',
    environment: ENVIRONMENT,
    timestamp: new Date().toISOString(),
  };

  Logger.log(`GET /health 200 ${clientIp}`);
  res.status(200).json(responseData);
}
