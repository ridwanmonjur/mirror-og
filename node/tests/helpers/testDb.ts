import { prisma } from '../../src/config/database';
import { Logger } from '../../src/utils/logger';

/**
 * Test Database Helpers (Prisma Version)
 *
 * Utilities for seeding and cleaning test database using Prisma
 */

/**
 * Clear all test data from tables
 */
export async function clearTestData(): Promise<void> {
  try {
    // Delete in order to respect foreign key constraints
    // Use deleteMany to clear all records
    await prisma.bracketDeadline.deleteMany();
    await prisma.brackets.deleteMany();
    await prisma.team_members.deleteMany();
    await prisma.event_invitations.deleteMany();
    await prisma.awards_results.deleteMany();
    await prisma.likes.deleteMany();
    await prisma.awards.deleteMany();
    await prisma.achievements.deleteMany();
    await prisma.notifications2.deleteMany();
    await prisma.reports.deleteMany();
    await prisma.stars.deleteMany();
    await prisma.organizer_follows.deleteMany();
    await prisma.activity_logs.deleteMany();
    await prisma.event_details.deleteMany();
    await prisma.teams.deleteMany();
    await prisma.users.deleteMany();

    Logger.debug('Test data cleared');
  } catch (error) {
    Logger.error('Error clearing test data', error);
    throw error;
  }
}

/**
 * Seed test user
 */
export async function seedTestUser(data: {
  id?: number;
  name: string;
  email: string;
  role?: 'PARTICIPANT' | 'ORGANIZER' | 'ADMIN';
}): Promise<number> {
  const user = await prisma.users.create({
    data: {
      id: data.id ? BigInt(data.id) : undefined,
      name: data.name,
      email: data.email,
      password: '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // Required field
      role: data.role || 'PARTICIPANT',
    },
  });

  return Number(user.id);
}

/**
 * Seed test event
 */
export async function seedTestEvent(data: {
  id?: number;
  user_id: number;
  eventName: string;
}): Promise<number> {
  const event = await prisma.event_details.create({
    data: {
      id: data.id ? BigInt(data.id) : undefined,
      user_id: BigInt(data.user_id),
      eventName: data.eventName,
    },
  });

  return Number(event.id);
}

/**
 * Seed test team
 */
export async function seedTestTeam(data: {
  id?: number;
  teamName: string;
  creator_id: number;
}): Promise<number> {
  const team = await prisma.teams.create({
    data: {
      id: data.id ? BigInt(data.id) : undefined,
      teamName: data.teamName,
      creator_id: BigInt(data.creator_id),
    },
  });

  return Number(team.id);
}

/**
 * Seed test team member
 */
export async function seedTestTeamMember(data: {
  user_id: number;
  team_id: number;
  status?: 'accepted' | 'pending' | 'rejected';
}): Promise<number> {
  const member = await prisma.team_members.create({
    data: {
      user_id: BigInt(data.user_id),
      team_id: BigInt(data.team_id),
      status: data.status || 'accepted',
    },
  });

  return Number(member.id);
}

/**
 * Seed test bracket match
 */
export async function seedTestBracket(data: {
  team1_id: string | null;
  team1_position: string;
  team2_id: string | null;
  team2_position: string;
  stage_name: string;
  inner_stage_name: string;
  event_details_id: number;
}): Promise<number> {
  const bracket = await prisma.brackets.create({
    data: {
      team1_id: data.team1_id,
      team1_position: data.team1_position,
      team2_id: data.team2_id,
      team2_position: data.team2_position,
      stage_name: data.stage_name,
      inner_stage_name: data.inner_stage_name,
      event_details_id: BigInt(data.event_details_id),
    },
  });

  return Number(bracket.id);
}

/**
 * Seed test bracket deadline
 */
export async function seedTestBracketDeadline(data: {
  event_details_id: number;
  deadlines: Record<string, Record<string, { start_date: string; end_date: string }>>;
}): Promise<number> {
  const deadline = await prisma.bracketDeadline.upsert({
    where: {
      event_details_id: BigInt(data.event_details_id),
    },
    update: {
      deadlines: data.deadlines as any,
    },
    create: {
      event_details_id: BigInt(data.event_details_id),
      deadlines: data.deadlines as any,
    },
  });

  return Number(deadline.id);
}

/**
 * Execute raw SQL query (for advanced cases)
 */
export async function execQuery(sql: string, params?: any[]): Promise<any> {
  // Prisma doesn't support parameterized raw queries the same way
  // For simple queries, use Prisma's models instead
  // For complex queries, use $queryRaw or $executeRaw
  return await prisma.$queryRawUnsafe(sql, ...(params || []));
}

/**
 * Start a database transaction for test isolation
 */
export async function beginTransaction(): Promise<void> {
  await prisma.$executeRaw`START TRANSACTION`;
}

/**
 * Rollback the current transaction
 */
export async function rollbackTransaction(): Promise<void> {
  try {
    await prisma.$executeRaw`ROLLBACK`;
  } catch (error) {
    Logger.warn('Failed to rollback transaction:', error);
  }
}
