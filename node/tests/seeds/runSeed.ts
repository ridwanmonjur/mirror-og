#!/usr/bin/env ts-node
/**
 * Manual Test Database Seeder
 * Run this before running integration tests:
 * cd node && npx ts-node tests/seeds/runSeed.ts
 */

import { seedTestDatabase } from './seedTestData';

async function main() {
  console.log('🌱 Seeding test database...');
  await seedTestDatabase();
  console.log('✅ Test database seeded successfully!');
  process.exit(0);
}

main().catch((error) => {
  console.error('❌ Failed to seed database:', error);
  process.exit(1);
});
