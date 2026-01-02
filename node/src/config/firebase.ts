import admin from 'firebase-admin';
import { Logger } from '../utils/logger';
import * as fs from 'fs';
import * as path from 'path';

let firebaseApp: admin.app.App;
let db: admin.firestore.Firestore;

/**
 * Initialize Firebase Admin SDK
 */
export function initializeFirebase(): void {
  try {
    const projectId = process.env.FIREBASE_PROJECT_ID;
    const credentialsPath = process.env.FIREBASE_CREDENTIALS_PATH;

    if (!projectId) {
      throw new Error('FIREBASE_PROJECT_ID is not set in environment variables');
    }

    // Check if running in emulator mode
    const emulatorHost = process.env.FIREBASE_EMULATOR_HOST;

    if (emulatorHost) {
      // Use Firebase emulator for testing
      Logger.log(`Connecting to Firebase emulator at ${emulatorHost}`);
      process.env.FIRESTORE_EMULATOR_HOST = emulatorHost;

      firebaseApp = admin.initializeApp({
        projectId,
      });
    } else {
      // Production/development mode - use service account
      if (!credentialsPath) {
        throw new Error('FIREBASE_CREDENTIALS_PATH is not set in environment variables');
      }

      const absolutePath = path.resolve(credentialsPath);

      if (!fs.existsSync(absolutePath)) {
        throw new Error(`Firebase credentials file not found at: ${absolutePath}`);
      }

      const serviceAccount = JSON.parse(fs.readFileSync(absolutePath, 'utf8'));

      firebaseApp = admin.initializeApp({
        credential: admin.credential.cert(serviceAccount),
        projectId,
      });

      Logger.log('Firebase Admin SDK initialized successfully');
    }

    db = admin.firestore();
    const databaseId = process.env.FIREBASE_DATABASE_ID || '(default)';

    Logger.log(`Connected to Firestore database: ${databaseId}`);
  } catch (error) {
    Logger.error('Failed to initialize Firebase', error);
    throw error;
  }
}

/**
 * Get Firestore instance
 */
export function getFirestore(): admin.firestore.Firestore {
  if (!db) {
    throw new Error('Firestore not initialized. Call initializeFirebase() first.');
  }
  return db;
}

/**
 * Get Firebase app instance
 */
export function getFirebaseApp(): admin.app.App {
  if (!firebaseApp) {
    throw new Error('Firebase not initialized. Call initializeFirebase() first.');
  }
  return firebaseApp;
}

/**
 * Get Firestore server timestamp
 */
export function getServerTimestamp(): admin.firestore.FieldValue {
  return admin.firestore.FieldValue.serverTimestamp();
}

/**
 * Helper to get bracket collection reference
 */
export function getBracketCollection(eventId: string) {
  return db.collection(`event/${eventId}/brackets`);
}

/**
 * Helper to get dispute collection reference
 */
export function getDisputeCollection(eventId: string) {
  return db.collection(`event/${eventId}/disputes`);
}

export default {
  initializeFirebase,
  getFirestore,
  getFirebaseApp,
  getServerTimestamp,
  getBracketCollection,
  getDisputeCollection
};
