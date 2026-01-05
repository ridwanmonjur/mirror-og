/**
 * Jest Setup File
 *
 * Runs before all tests
 * Sets up test environment and seeds test database
 */

// Set test environment variables
process.env.NODE_ENV = 'test';
process.env.DB_HOST = '127.0.0.1';
process.env.DB_PORT = '3306'; // Local MySQL port
process.env.DB_DATABASE = 'driftwood_test';
process.env.DB_USERNAME = 'root';
process.env.DB_PASSWORD = '';
process.env.DATABASE_URL = 'mysql://root:@127.0.0.1:3306/driftwood_test'; // Prisma DATABASE_URL
process.env.JWT_SECRET = 'base64:gWgnFHHkq2+LfxJVYpvw7OuePNls3SzfK0qSdSFbPp4=';
process.env.SECRET_KEY = 'test-secret-key-for-jwt-tokens';
process.env.FIREBASE_PROJECT_ID = 'test-project';
process.env.FIREBASE_EMULATOR_HOST = 'localhost:8080'; // Firebase emulator
process.env.LOG_LEVEL = 'error'; // Reduce log noise during tests

// NOTE: Seed test database manually before running tests:
// cd node && npx ts-node tests/seeds/runSeed.ts

// Mock Firebase Admin SDK
jest.mock('firebase-admin', () => {
  const mockAuth = {
    createCustomToken: jest.fn((uid: string) => {
      return Promise.resolve(`mock-custom-token-${uid}`);
    }),
  };

  const mockFirestore = {
    collection: jest.fn(() => ({
      doc: jest.fn(() => ({
        get: jest.fn(() => Promise.resolve({ exists: false })),
        set: jest.fn(() => Promise.resolve()),
        update: jest.fn(() => Promise.resolve()),
        delete: jest.fn(() => Promise.resolve()),
      })),
      where: jest.fn(() => ({
        get: jest.fn(() => Promise.resolve({ docs: [] })),
      })),
      add: jest.fn(() => Promise.resolve({ id: 'mock-doc-id' })),
    })),
  };

  return {
    __esModule: true,
    default: {
      apps: [],
      initializeApp: jest.fn(() => ({
        name: 'test-app',
      })),
      auth: jest.fn(() => mockAuth),
      firestore: jest.fn(() => mockFirestore),
      credential: {
        cert: jest.fn(),
      },
    },
    apps: [],
    initializeApp: jest.fn(() => ({
      name: 'test-app',
    })),
    auth: jest.fn(() => mockAuth),
    firestore: jest.fn(() => mockFirestore),
    credential: {
      cert: jest.fn(),
    },
  };
});

// Global test timeout
jest.setTimeout(10000);
