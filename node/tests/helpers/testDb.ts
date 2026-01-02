import { query, getConnection } from '../../src/config/database';
import { Logger } from '../../src/utils/logger';

/**
 * Test Database Helpers
 *
 * Utilities for seeding and cleaning test database
 */

/**
 * Clear all test data from tables
 */
export async function clearTestData(): Promise<void> {
  try {
    // Disable foreign key checks temporarily
    await query('SET FOREIGN_KEY_CHECKS = 0');

    // Clear tables in order (respect foreign keys)
    await query('DELETE FROM brackets');
    await query('DELETE FROM bracket_deadlines');
    await query('DELETE FROM team_members');
    await query('DELETE FROM event_details');
    await query('DELETE FROM teams');
    await query('DELETE FROM users');

    // Re-enable foreign key checks
    await query('SET FOREIGN_KEY_CHECKS = 1');

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
  const result: any = await query(
    `INSERT INTO users (id, name, email, role, created_at, updated_at)
     VALUES (?, ?, ?, ?, NOW(), NOW())`,
    [data.id || null, data.name, data.email, data.role || 'PARTICIPANT']
  );

  return result.insertId || data.id;
}

/**
 * Seed test event
 */
export async function seedTestEvent(data: {
  id?: number;
  user_id: number;
  eventName: string;
}): Promise<number> {
  const result: any = await query(
    `INSERT INTO event_details (id, user_id, eventName, created_at, updated_at)
     VALUES (?, ?, ?, NOW(), NOW())`,
    [data.id || null, data.user_id, data.eventName]
  );

  return result.insertId || data.id;
}

/**
 * Seed test team
 */
export async function seedTestTeam(data: {
  id?: number;
  teamName: string;
  creator_id: number;
}): Promise<number> {
  const result: any = await query(
    `INSERT INTO teams (id, teamName, creator_id, created_at, updated_at)
     VALUES (?, ?, ?, NOW(), NOW())`,
    [data.id || null, data.teamName, data.creator_id]
  );

  return result.insertId || data.id;
}

/**
 * Seed test team member
 */
export async function seedTestTeamMember(data: {
  user_id: number;
  team_id: number;
  status?: 'accepted' | 'pending' | 'rejected';
}): Promise<number> {
  const result: any = await query(
    `INSERT INTO team_members (user_id, team_id, status, created_at, updated_at)
     VALUES (?, ?, ?, NOW(), NOW())`,
    [data.user_id, data.team_id, data.status || 'accepted']
  );

  return result.insertId;
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
  const result: any = await query(
    `INSERT INTO brackets (team1_id, team1_position, team2_id, team2_position,
                          stage_name, inner_stage_name, event_details_id,
                          created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
    [
      data.team1_id,
      data.team1_position,
      data.team2_id,
      data.team2_position,
      data.stage_name,
      data.inner_stage_name,
      data.event_details_id,
    ]
  );

  return result.insertId;
}

/**
 * Seed test bracket deadline
 */
export async function seedTestBracketDeadline(data: {
  event_details_id: number;
  deadlines: Record<string, Record<string, { start_date: string; end_date: string }>>;
}): Promise<number> {
  const result: any = await query(
    `INSERT INTO bracket_deadlines (event_details_id, deadlines)
     VALUES (?, ?)`,
    [data.event_details_id, JSON.stringify(data.deadlines)]
  );

  return result.insertId;
}

/**
 * Execute raw SQL query
 */
export async function execQuery(sql: string, params?: any[]): Promise<any> {
  return await query(sql, params);
}
