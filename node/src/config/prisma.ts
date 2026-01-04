import { PrismaClient } from '@prisma/client';
import { Logger } from '../utils/logger';

/**
 * Prisma Client Singleton
 *
 * Ensures only one instance of Prisma Client exists throughout the application lifecycle
 */

// Declare global prisma client type
declare global {
  // eslint-disable-next-line no-var
  var prisma: PrismaClient | undefined;
}

// Create or reuse Prisma Client instance
export const prisma = global.prisma || new PrismaClient({
  log: process.env.NODE_ENV === 'development' ? ['query', 'error', 'warn'] : ['error'],
});

// In development, store prisma in global to prevent creating multiple instances during hot reload
if (process.env.NODE_ENV !== 'production') {
  global.prisma = prisma;
}

/**
 * Test Prisma database connection
 */
export async function testPrismaConnection(): Promise<void> {
  try {
    await prisma.$connect();
    Logger.log('Prisma database connection established successfully');
  } catch (error) {
    Logger.error('Failed to connect to database via Prisma', error);
    throw error;
  }
}

/**
 * Disconnect Prisma client (for graceful shutdown)
 */
export async function disconnectPrisma(): Promise<void> {
  try {
    await prisma.$disconnect();
    Logger.log('Prisma database connection closed');
  } catch (error) {
    Logger.error('Error disconnecting Prisma', error);
  }
}

export default prisma;
