import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';
import {
  clearTestData,
  seedTestUser,
  seedTestEvent,
} from '../helpers/testDb';
import {
  generateOrganizerToken,
  generateParticipantToken,
  generateAdminToken,
  getAuthHeader,
} from '../helpers/testAuth';

describe('Organizer API - Integration Tests', () => {
  let organizerId: number;
  let participantId: number;
  let adminId: number;
  let eventId: number;

  beforeAll(async () => {
    initializeFirebase();
  });

  beforeEach(async () => {
    await clearTestData();

    organizerId = await seedTestUser({
      id: 1,
      name: 'Test Organizer',
      email: 'organizer@test.com',
      role: 'ORGANIZER',
    });

    participantId = await seedTestUser({
      id: 2,
      name: 'Test Participant',
      email: 'participant@test.com',
      role: 'PARTICIPANT',
    });

    adminId = await seedTestUser({
      id: 3,
      name: 'Test Admin',
      email: 'admin@test.com',
      role: 'ADMIN',
    });

    eventId = await seedTestEvent({
      id: 1,
      user_id: organizerId,
      eventName: 'Test Tournament',
    });
  });

  describe('POST /api/organizer/events/search', () => {
    it('should allow organizer to search their events', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/organizer/events/search')
        .set(getAuthHeader(token))
        .send({
          search_term: 'Test',
          page: 1,
          limit: 20,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should allow admin to search events', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .post('/api/organizer/events/search')
        .set(getAuthHeader(token))
        .send({
          search_term: '',
          page: 1,
          limit: 10,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should search with filters', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/organizer/events/search')
        .set(getAuthHeader(token))
        .send({
          search_term: 'Tournament',
          filters: {
            game_id: 1,
            status: 'active',
          },
          page: 1,
          limit: 20,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should reject participant from searching organizer events', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/organizer/events/search')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });

    it('should reject unauthenticated requests', async () => {
      const response = await request(app)
        .post('/api/organizer/events/search')
        .send({});

      expect(response.status).toBe(401);
    });
  });

  describe('POST /api/organizer/event/:id/destroy', () => {
    it('should allow organizer to delete their event', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/destroy`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('deleted');
    });

    it('should reject organizer from deleting another organizer event', async () => {
      const otherOrganizerId = await seedTestUser({
        id: 4,
        name: 'Other Organizer',
        email: 'other.organizer@test.com',
        role: 'ORGANIZER',
      });

      const token = generateOrganizerToken(String(otherOrganizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/destroy`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(404);
      expect(response.body.error).toContain('not found');
    });

    it('should return 404 for non-existent event', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/organizer/event/99999/destroy')
        .set(getAuthHeader(token));

      expect(response.status).toBe(404);
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/destroy`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/organizer/event/:id/results', () => {
    it('should allow organizer to store event results', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/results`)
        .set(getAuthHeader(token))
        .send({
          team_id: 1,
          placement: 1,
          prize_amount: 1000,
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('successfully');
    });

    it('should update existing result on duplicate', async () => {
      const token = generateOrganizerToken(String(organizerId));

      // First insert
      await request(app)
        .post(`/api/organizer/event/${eventId}/results`)
        .set(getAuthHeader(token))
        .send({
          team_id: 1,
          placement: 1,
          prize_amount: 1000,
        });

      // Update with same team_id
      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/results`)
        .set(getAuthHeader(token))
        .send({
          team_id: 1,
          placement: 2,
          prize_amount: 500,
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/results`)
        .set(getAuthHeader(token))
        .send({
          team_id: 1,
          placement: 1,
          prize_amount: 1000,
        });

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/organizer/event/:id/notifications', () => {
    it('should allow organizer to send notifications to all participants', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/notifications`)
        .set(getAuthHeader(token))
        .send({
          title: 'Tournament Update',
          message: 'The tournament schedule has changed',
          recipient_type: 'all_participants',
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('count');
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/notifications`)
        .set(getAuthHeader(token))
        .send({
          title: 'Test',
          message: 'Test',
          recipient_type: 'all_participants',
        });

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/organizer/event/:id/matches', () => {
    it('should allow organizer to upsert bracket match', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/matches`)
        .set(getAuthHeader(token))
        .send({
          match_id: 'W1.W2',
          team1_id: '1',
          team2_id: '2',
          winner_id: '1',
          status: 'completed',
          round: 1,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('Bracket updated');
    });

    it('should update existing match on duplicate', async () => {
      const token = generateOrganizerToken(String(organizerId));

      // First insert
      await request(app)
        .post(`/api/organizer/event/${eventId}/matches`)
        .set(getAuthHeader(token))
        .send({
          match_id: 'W1.W2',
          team1_id: '1',
          team2_id: '2',
          winner_id: null,
          status: 'upcoming',
          round: 1,
        });

      // Update
      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/matches`)
        .set(getAuthHeader(token))
        .send({
          match_id: 'W1.W2',
          team1_id: '1',
          team2_id: '2',
          winner_id: '1',
          status: 'completed',
          round: 1,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/matches`)
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/organizer/event/:id/awards', () => {
    it('should allow organizer to create award', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/awards`)
        .set(getAuthHeader(token))
        .send({
          team_id: 1,
          user_id: participantId,
          award_type: 'MVP',
          award_name: 'Most Valuable Player',
          award_value: 100,
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/awards`)
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });
  });

  describe('DELETE /api/organizer/event/:id/awards/:awardId', () => {
    it('should allow organizer to delete award', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .delete(`/api/organizer/event/${eventId}/awards/1`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('deleted');
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .delete(`/api/organizer/event/${eventId}/awards/1`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/organizer/event/:id/achievements', () => {
    it('should allow organizer to create achievement', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/achievements`)
        .set(getAuthHeader(token))
        .send({
          user_id: participantId,
          achievement_type: 'WINNER',
          achievement_name: 'Tournament Winner',
          achievement_data: { tournament_id: eventId },
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/organizer/event/${eventId}/achievements`)
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });
  });

  describe('DELETE /api/organizer/event/achievements/:achievementId', () => {
    it('should allow organizer to delete achievement', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .delete('/api/organizer/event/achievements/1')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('deleted');
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .delete('/api/organizer/event/achievements/1')
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/organizer/profile', () => {
    it('should allow organizer to edit their profile', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/organizer/profile')
        .set(getAuthHeader(token))
        .send({
          name: 'Updated Organizer',
          bio: 'Tournament organizer',
          company_name: 'Esports Inc',
          website: 'https://esports.com',
          avatar_url: 'https://example.com/avatar.jpg',
          social_links: {
            twitter: '@organizer',
            discord: 'organizer#1234',
          },
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('updated');
    });

    it('should allow admin to edit profile', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .post('/api/organizer/profile')
        .set(getAuthHeader(token))
        .send({
          name: 'Updated Admin',
          bio: 'Admin bio',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should reject participant', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/organizer/profile')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });

    it('should reject unauthenticated requests', async () => {
      const response = await request(app)
        .post('/api/organizer/profile')
        .send({});

      expect(response.status).toBe(401);
    });
  });
});
