import { prisma } from '../../src/config/database';
import { Logger } from '../../src/utils/logger';
import * as fs from 'fs';
import * as path from 'path';

/**
 * Seed Test Database with Real Production Data
 * Uses dumped SQL from driftwood database
 */
export async function seedTestDatabase(): Promise<void> {
  try {
    // Clear existing data first
    // Logger.info('Clearing existing test data...');
    await clearTestDatabase();

    // Read the SQL dump file
    const sqlFilePath = path.join(__dirname, 'driftwood_data.sql');
    const sqlContent = fs.readFileSync(sqlFilePath, 'utf8');

    // Split by semicolons and filter out empty statements, comments, and CREATE TABLE statements
    const statements = sqlContent
      .split(';')
      .map(s => s.trim())
      .filter(s => {
        if (s.length === 0) return false;
        if (s.startsWith('--') || s.startsWith('/*')) return false;
        // Skip CREATE TABLE statements (Prisma already created tables)
        if (s.toUpperCase().includes('CREATE TABLE')) return false;
        return true;
      });

    // Logger.info(`Executing ${statements.length} SQL statements...`);

    // Execute each statement
    for (const statement of statements) {
      try {
        await prisma.$executeRawUnsafe(statement);
      } catch (error: any) {
        // Log warnings for failed statements (usually foreign key constraint issues)
        // These are expected when SQL dump has data in wrong order
        // Logger.warn(`Failed to execute statement: ${statement.substring(0, 100)}...`, error);
      }
    }

    // Logger.info('✓ Test database seeded successfully with production data');
  } catch (error) {
    Logger.error('Failed to seed test database', error);
    throw error;
  }
}

/**
 * Clear all test data
 */
export async function clearTestDatabase(): Promise<void> {
  try {
    await prisma.$executeRaw`SET FOREIGN_KEY_CHECKS = 0`;

    // Get all table names
    const tables: any[] = await prisma.$queryRaw`
      SELECT table_name
      FROM information_schema.tables
      WHERE table_schema = DATABASE()
      AND table_type = 'BASE TABLE'
    `;

    // Truncate each table
    for (const { table_name } of tables) {
      await prisma.$executeRawUnsafe(`TRUNCATE TABLE \`${table_name}\``);
    }

    await prisma.$executeRaw`SET FOREIGN_KEY_CHECKS = 1`;
    // Logger.debug('✓ Test database cleared');
  } catch (error) {
    Logger.error('Failed to clear test database', error);
    throw error;
  }
}
