import { prisma } from '../../src/config/database';
import { Logger } from '../../src/utils/logger';
import { UserRole } from '@prisma/client';

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
    await prisma.bracket.deleteMany();
    await prisma.teamMember.deleteMany();
    await prisma.teamInvitation.deleteMany();
    await prisma.eventResult.deleteMany();
    await prisma.eventLike.deleteMany();
    await prisma.eventInvitation.deleteMany();
    await prisma.eventMatch.deleteMany();
    await prisma.eventAward.deleteMany();
    await prisma.userAchievement.deleteMany();
    await prisma.notification.deleteMany();
    await prisma.userReport.deleteMany();
    await prisma.userStar.deleteMany();
    await prisma.organizerFollower.deleteMany();
    await prisma.activityLog.deleteMany();
    await prisma.eventDetail.deleteMany();
    await prisma.team.deleteMany();
    await prisma.user.deleteMany();

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
  const user = await prisma.user.create({
    data: {
      id: data.id,
      name: data.name,
      email: data.email,
      role: (data.role as UserRole) || UserRole.PARTICIPANT,
    },
  });

  return user.id;
}

/**
 * Seed test event
 */
export async function seedTestEvent(data: {
  id?: number;
  user_id: number;
  eventName: string;
}): Promise<number> {
  const event = await prisma.eventDetail.create({
    data: {
      id: data.id,
      user_id: data.user_id,
      eventName: data.eventName,
    },
  });

  return event.id;
}

/**
 * Seed test team
 */
export async function seedTestTeam(data: {
  id?: number;
  teamName: string;
  creator_id: number;
}): Promise<number> {
  const team = await prisma.team.create({
    data: {
      id: data.id,
      teamName: data.teamName,
      creator_id: data.creator_id,
    },
  });

  return team.id;
}

/**
 * Seed test team member
 */
export async function seedTestTeamMember(data: {
  user_id: number;
  team_id: number;
  status?: 'accepted' | 'pending' | 'rejected';
}): Promise<number> {
  const member = await prisma.teamMember.create({
    data: {
      user_id: data.user_id,
      team_id: data.team_id,
      status: data.status || 'accepted',
    },
  });

  return member.id;
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
  const bracket = await prisma.bracket.create({
    data: {
      team1_id: data.team1_id,
      team1_position: data.team1_position,
      team2_id: data.team2_id,
      team2_position: data.team2_position,
      stage_name: data.stage_name,
      inner_stage_name: data.inner_stage_name,
      event_details_id: data.event_details_id,
    },
  });

  return bracket.id;
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
      event_details_id: data.event_details_id,
    },
    update: {
      deadlines: data.deadlines as any,
    },
    create: {
      event_details_id: data.event_details_id,
      deadlines: data.deadlines as any,
    },
  });

  return deadline.id;
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
