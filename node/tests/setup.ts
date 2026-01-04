/**
 * Jest Setup File
 *
 * Runs before all tests
 */

// Set test environment variables
process.env.NODE_ENV = 'test';
process.env.DB_HOST = '127.0.0.1';
process.env.DB_PORT = '3307'; // Test database port from docker-compose.local.yml
process.env.DB_DATABASE = 'driftwood_test';
process.env.DB_USERNAME = 'root';
process.env.DB_PASSWORD = '';
process.env.DATABASE_URL = 'mysql://root:@127.0.0.1:3307/driftwood_test'; // Prisma DATABASE_URL
process.env.JWT_SECRET = 'base64:gWgnFHHkq2+LfxJVYpvw7OuePNls3SzfK0qSdSFbPp4=';
process.env.FIREBASE_PROJECT_ID = 'test-project';
process.env.FIREBASE_EMULATOR_HOST = 'localhost:8080'; // Firebase emulator
process.env.LOG_LEVEL = 'error'; // Reduce log noise during tests

// Global test timeout
jest.setTimeout(10000);
