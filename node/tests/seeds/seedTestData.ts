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
    // Read the SQL dump file
    const sqlFilePath = path.join(__dirname, 'driftwood_data.sql');
    const sqlContent = fs.readFileSync(sqlFilePath, 'utf8');

    // Split by semicolons and filter out empty statements
    const statements = sqlContent
      .split(';')
      .map(s => s.trim())
      .filter(s => s.length > 0 && !s.startsWith('--') && !s.startsWith('/*'));

    Logger.info(`Executing ${statements.length} SQL statements...`);

    // Execute each statement
    for (const statement of statements) {
      try {
        await prisma.$executeRawUnsafe(statement);
      } catch (error: any) {
        // Ignore certain expected errors
        if (
          error.message?.includes('Duplicate entry') ||
          error.message?.includes('already exists')
        ) {
          // Skip duplicate entries
          continue;
        }
        Logger.warn(`Failed to execute statement: ${statement.substring(0, 100)}...`, error);
      }
    }

    Logger.info('✓ Test database seeded successfully with production data');
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
    Logger.debug('✓ Test database cleared');
  } catch (error) {
    Logger.error('Failed to clear test database', error);
    throw error;
  }
}
