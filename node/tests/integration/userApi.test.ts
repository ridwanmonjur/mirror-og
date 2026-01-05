import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';
import {
  seedTestUser,
  seedTestEvent,
  seedTestTeam,
  seedTestTeamMember,
  beginTransaction,
  rollbackTransaction,
} from '../helpers/testDb';
import {
  generateParticipantToken,
  generateOrganizerToken,
  generateAdminToken,
  getAuthHeader,
} from '../helpers/testAuth';

describe('User API - Integration Tests', () => {
  let participantId: number;
  let participant2Id: number;
  let organizerId: number;
  let adminId: number;
  let eventId: number;
  let teamId: number;

  beforeAll(async () => {
    initializeFirebase();
  });

  beforeEach(async () => {
    await beginTransaction();

    participantId = await seedTestUser({
      name: 'Test Participant',
      email: 'participant@test.com',
      role: 'PARTICIPANT',
    });

    participant2Id = await seedTestUser({
      name: 'Test Participant 2',
      email: 'participant2@test.com',
      role: 'PARTICIPANT',
    });

    organizerId = await seedTestUser({
      name: 'Test Organizer',
      email: 'organizer@test.com',
      role: 'ORGANIZER',
    });

    adminId = await seedTestUser({
      name: 'Test Admin',
      email: 'admin@test.com',
      role: 'ADMIN',
    });

    eventId = await seedTestEvent({
      user_id: organizerId,
      eventName: 'Test Tournament',
    });

    teamId = await seedTestTeam({
      teamName: 'Test Team',
      creator_id: participantId,
    });

    await seedTestTeamMember({
      user_id: participantId,
      team_id: teamId,
      status: 'accepted',
    });
  });

  afterEach(async () => {
    await rollbackTransaction();
  });

  describe('GET /api/user', () => {
    it('should return current authenticated participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .get('/api/user')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('id', participantId);
      expect(response.body).toHaveProperty('email', 'participant@test.com');
    });

    it('should return current authenticated organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .get('/api/user')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('id', organizerId);
      expect(response.body).toHaveProperty('role', 'ORGANIZER');
    });

    it('should return current authenticated admin', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .get('/api/user')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('id', adminId);
      expect(response.body).toHaveProperty('role', 'ADMIN');
    });

    it('should reject unauthenticated requests', async () => {
      const response = await request(app).get('/api/user');

      expect(response.status).toBe(401);
    });

    it('should reject invalid token', async () => {
      const response = await request(app)
        .get('/api/user')
        .set({ Authorization: 'Bearer invalid_token' });

      expect(response.status).toBe(401);
    });
  });

  describe('GET /api/teams/search', () => {
    it('should search teams by name', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .get('/api/teams/search')
        .query({ search_term: 'Test', limit: 20 })
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should allow organizer to search teams', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .get('/api/teams/search')
        .query({ search_term: 'Team' })
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should limit results', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .get('/api/teams/search')
        .query({ search_term: '', limit: 5 })
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });
  });

  describe('POST /api/teams/list', () => {
    it('should get teams for user', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/teams/list')
        .set(getAuthHeader(token))
        .send({
          user_id: participantId,
          page: 1,
          limit: 20,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should paginate results', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/teams/list')
        .set(getAuthHeader(token))
        .send({
          user_id: participantId,
          page: 2,
          limit: 10,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });
  });

  describe('POST /api/event/:id/brackets', () => {
    it('should validate bracket structure', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/event/${eventId}/brackets`)
        .set(getAuthHeader(token))
        .send({
          bracket_data: {
            matches: [],
          },
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('valid');
    });

    it('should reject invalid bracket structure', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/event/${eventId}/brackets`)
        .set(getAuthHeader(token))
        .send({
          bracket_data: null,
        });

      expect(response.status).toBe(200);
      expect(response.body.valid).toBe(false);
    });
  });

  describe('GET /api/user/:id/reports', () => {
    it('should get user reports', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .get(`/api/user/${participantId}/reports`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(Array.isArray(response.body.data)).toBe(true);
    });
  });

  describe('GET /api/user/notifications', () => {
    it('should get user notifications', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .get('/api/user/notifications')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(Array.isArray(response.body.data)).toBe(true);
    });
  });

  describe('POST /api/user/notifications', () => {
    it('should create notification', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .post('/api/user/notifications')
        .set(getAuthHeader(token))
        .send({
          user_id: participantId,
          title: 'Test Notification',
          message: 'This is a test',
          type: 'info',
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
    });
  });

  describe('POST /api/user/notifications/:id', () => {
    it('should mark notification as read', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/user/notifications/1')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('marked as read');
    });
  });

  describe('POST /api/user/settings', () => {
    it('should update user settings', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/user/settings')
        .set(getAuthHeader(token))
        .send({
          settings: {
            notifications: true,
            privacy: 'public',
            theme: 'dark',
          },
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('updated');
    });
  });

  describe('POST /api/user/:id/background', () => {
    it('should update user background', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/user/${participantId}/background`)
        .set(getAuthHeader(token))
        .send({
          background_url: 'https://example.com/background.jpg',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('updated');
    });
  });

  describe('POST /api/user/:id/star', () => {
    it('should star another user', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/user/${participant2Id}/star`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.starred).toBe(true);
    });

    it('should unstar user when already starred', async () => {
      const token = generateParticipantToken(String(participantId));

      // Star first
      await request(app)
        .post(`/api/user/${participant2Id}/star`)
        .set(getAuthHeader(token));

      // Unstar
      const response = await request(app)
        .post(`/api/user/${participant2Id}/star`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.starred).toBe(false);
    });
  });

  describe('POST /api/user/:id/report', () => {
    it('should report user', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/user/${participant2Id}/report`)
        .set(getAuthHeader(token))
        .send({
          reason: 'INAPPROPRIATE_BEHAVIOR',
          description: 'User was being toxic in chat',
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
    });
  });

  describe('POST /api/user/likes', () => {
    it('should like event', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/user/likes')
        .set(getAuthHeader(token))
        .send({
          event_id: eventId,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.liked).toBe(true);
    });

    it('should unlike event when already liked', async () => {
      const token = generateParticipantToken(String(participantId));

      // Like first
      await request(app)
        .post('/api/user/likes')
        .set(getAuthHeader(token))
        .send({
          event_id: eventId,
        });

      // Unlike
      const response = await request(app)
        .post('/api/user/likes')
        .set(getAuthHeader(token))
        .send({
          event_id: eventId,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.liked).toBe(false);
    });
  });

  describe('POST /api/user/participants', () => {
    it('should search participants by name', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/user/participants')
        .set(getAuthHeader(token))
        .send({
          search_term: 'Participant',
          limit: 20,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(Array.isArray(response.body.data)).toBe(true);
    });
  });

  describe('POST /api/user/unlink', () => {
    it('should unlink bank account', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/user/unlink')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('unlinked');
    });
  });
});
