import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';
import {
  clearTestData,
  seedTestUser,
  seedTestEvent,
  seedTestBracket,
  seedTestBracketDeadline,
} from '../helpers/testDb';

describe('Tournament API - Integration Tests', () => {
  let userId: number;
  let eventId: number;

  beforeAll(async () => {
    initializeFirebase();
  });

  beforeEach(async () => {
    await clearTestData();

    userId = await seedTestUser({
      id: 1,
      name: 'Test Organizer',
      email: 'organizer@test.com',
      role: 'ORGANIZER',
    });

    eventId = await seedTestEvent({
      id: 1,
      user_id: userId,
      eventName: 'Test Tournament',
    });

    // Seed some bracket data
    await seedTestBracket({
      team1_id: '1',
      team1_position: 'W1',
      team2_id: '2',
      team2_position: 'W2',
      stage_name: 'U',
      inner_stage_name: 'e1',
      event_details_id: eventId,
    });

    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    await seedTestBracketDeadline({
      event_details_id: eventId,
      deadlines: {
        U: {
          e1: {
            start_date: yesterday.toISOString().split('T')[0],
            end_date: tomorrow.toISOString().split('T')[0],
          },
        },
      },
    });
  });

  describe('GET /health', () => {
    it('should return healthy status', async () => {
      const response = await request(app).get('/health');

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('status', 'healthy');
      expect(response.body).toHaveProperty('service', 'driftwood-api');
      expect(response.body).toHaveProperty('firebase_initialized', true);
      expect(response.body).toHaveProperty('timestamp');
    });

    it('should not require authentication', async () => {
      const response = await request(app).get('/health');

      expect(response.status).toBe(200);
    });
  });

  describe('POST /room/block', () => {
    it('should block room between two users', async () => {
      const response = await request(app)
        .post('/room/block')
        .send({
          user1: 1,
          user2: 2,
          action: 'block',
          blocked_by: 1,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.action).toBe('block');
      expect(response.body).toHaveProperty('rooms_updated');
    });

    it('should unblock room between two users', async () => {
      const response = await request(app)
        .post('/room/block')
        .send({
          user1: 1,
          user2: 2,
          action: 'unblock',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.action).toBe('unblock');
    });

    it('should reject invalid action', async () => {
      const response = await request(app)
        .post('/room/block')
        .send({
          user1: 1,
          user2: 2,
          action: 'invalid',
        });

      expect(response.status).toBe(400);
      expect(response.body.detail).toContain('action must be');
    });

    it('should require blocked_by for block action', async () => {
      const response = await request(app)
        .post('/room/block')
        .send({
          user1: 1,
          user2: 2,
          action: 'block',
        });

      expect(response.status).toBe(400);
      expect(response.body.detail).toContain('blocked_by');
    });
  });

  describe('POST /batch/reports', () => {
    it('should create batch reports', async () => {
      const response = await request(app)
        .post('/batch/reports')
        .send({
          event_id: eventId,
          count: 2,
          specific_ids: ['W1.W2', 'W3.W4'],
          games_per_match: 3,
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('statusReport', 'success');
      expect(response.body).toHaveProperty('resultsReport');
      expect(Array.isArray(response.body.resultsReport)).toBe(true);
    });

    it('should create reports with custom values', async () => {
      const response = await request(app)
        .post('/batch/reports')
        .send({
          event_id: eventId,
          count: 1,
          specific_ids: ['W1.W2'],
          custom_values_array: [{
            completeMatchStatus: 'ENDED',
            score: [2, 1],
          }],
        });

      expect(response.status).toBe(200);
      expect(response.body.statusReport).toBe('success');
    });

    it('should handle empty batch', async () => {
      const response = await request(app)
        .post('/batch/reports')
        .send({
          event_id: eventId,
          count: 0,
          specific_ids: [],
        });

      expect(response.status).toBe(200);
    });
  });

  describe('POST /batch/disputes', () => {
    it('should create batch disputes', async () => {
      const response = await request(app)
        .post('/batch/disputes')
        .send({
          event_id: eventId,
          count: 2,
          specific_ids: ['dispute-1', 'dispute-2'],
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('statusDispute', 'success');
      expect(response.body).toHaveProperty('resultsDispute');
      expect(Array.isArray(response.body.resultsDispute)).toBe(true);
    });

    it('should create disputes with custom values', async () => {
      const response = await request(app)
        .post('/batch/disputes')
        .send({
          event_id: eventId,
          count: 1,
          specific_ids: ['custom-dispute'],
          custom_values_array: [{
            dispute_reason: 'INCORRECT_SCORE',
            dispute_teamId: '1',
          }],
        });

      expect(response.status).toBe(200);
      expect(response.body.statusDispute).toBe('success');
    });
  });

  describe('POST /deadline/started', () => {
    it('should handle started tournament tasks with matches', async () => {
      const response = await request(app)
        .post('/deadline/started')
        .send({
          detail_id: eventId,
          matches: [{
            team1_position: 'W1',
            team2_position: 'W2',
            event_details_id: eventId,
          }],
          games_per_match: 3,
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
      expect(response.body).toHaveProperty('results');
    });

    it('should handle empty matches array', async () => {
      const response = await request(app)
        .post('/deadline/started')
        .send({
          detail_id: eventId,
          matches: [],
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
      expect(response.body.message).toContain('No matches');
    });

    it('should handle event with no brackets', async () => {
      // Create event with no brackets
      const emptyEventId = await seedTestEvent({
        id: 99,
        user_id: userId,
        eventName: 'Empty Event',
      });

      const response = await request(app)
        .post('/deadline/started')
        .send({
          detail_id: emptyEventId,
          matches: [],
        });

      expect(response.status).toBe(200);
      expect(response.body.message).toContain('No');
    });
  });

  describe('POST /deadline/ended', () => {
    it('should handle ended tournament tasks', async () => {
      const response = await request(app)
        .post('/deadline/ended')
        .send({
          detail_id: eventId,
          matches: [{
            team1_position: 'W1',
            team2_position: 'W2',
            event_details_id: eventId,
            stage_name: 'U',
            inner_stage_name: 'e1',
            order: 0,
          }],
          bracket_info: {},
          tier_id: 1,
          is_league: false,
          games_per_match: 3,
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
      expect(response.body).toHaveProperty('results');
      expect(response.body).toHaveProperty('next_stage_data');
    });

    it('should handle empty matches array', async () => {
      const response = await request(app)
        .post('/deadline/ended')
        .send({
          detail_id: eventId,
          matches: [],
        });

      expect(response.status).toBe(200);
      expect(response.body.message).toContain('No matches');
    });
  });

  describe('POST /deadline/org', () => {
    it('should handle organizer deadline tasks', async () => {
      const response = await request(app)
        .post('/deadline/org')
        .send({
          detail_id: eventId,
          matches: [{
            team1_position: 'W1',
            team2_position: 'W2',
            event_details_id: eventId,
            stage_name: 'U',
            inner_stage_name: 'e1',
            order: 0,
          }],
          bracket_info: {},
          tier_id: 1,
          is_league: false,
          games_per_match: 3,
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
      expect(response.body).toHaveProperty('results');
      expect(response.body).toHaveProperty('next_stage_data');
    });

    it('should handle league tournaments', async () => {
      const response = await request(app)
        .post('/deadline/org')
        .send({
          detail_id: eventId,
          matches: [],
          is_league: true,
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
    });
  });

  describe('POST /match/result', () => {
    it('should get single match result', async () => {
      const response = await request(app)
        .post('/match/result')
        .send({
          event_id: eventId,
          match_id: 'W1.W2',
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('status');
      // May be 'not_found' or 'success' depending on if Firestore has the data
      expect(['not_found', 'success']).toContain(response.body.status);
    });

    it('should return not_found for non-existent match', async () => {
      const response = await request(app)
        .post('/match/result')
        .send({
          event_id: eventId,
          match_id: 'NON.EXISTENT',
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('not_found');
    });
  });

  describe('POST /match/results/all', () => {
    it('should get all match results for event', async () => {
      const response = await request(app)
        .post('/match/results/all')
        .send({
          event_id: eventId,
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
      expect(response.body).toHaveProperty('total_matches');
      expect(response.body).toHaveProperty('data');
      expect(typeof response.body.data).toBe('object');
    });

    it('should handle event with no matches', async () => {
      const emptyEventId = await seedTestEvent({
        id: 99,
        user_id: userId,
        eventName: 'Empty Event',
      });

      const response = await request(app)
        .post('/match/results/all')
        .send({
          event_id: emptyEventId,
        });

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('success');
      expect(response.body.total_matches).toBe(0);
    });
  });
});
