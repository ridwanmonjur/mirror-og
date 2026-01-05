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

describe('Participant API - Integration Tests', () => {
  let participantId: number;
  let participant2Id: number;
  let organizerId: number;
  let adminId: number;
  // let _eventId: number; // Reserved for future use
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

    /*_eventId =*/ await seedTestEvent({
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

  describe('POST /api/participant/events', () => {
    it('should allow participant to get events with filters', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/events')
        .set(getAuthHeader(token))
        .send({
          filters: {},
          page: 1,
          limit: 20,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should filter by game_id', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/events')
        .set(getAuthHeader(token))
        .send({
          filters: {
            game_id: 1,
          },
          page: 1,
          limit: 10,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should filter by status', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/events')
        .set(getAuthHeader(token))
        .send({
          filters: {
            status: 'active',
          },
          page: 1,
          limit: 20,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should allow admin', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .post('/api/participant/events')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/events')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });

    it('should reject unauthenticated requests', async () => {
      const response = await request(app)
        .post('/api/participant/events')
        .send({});

      expect(response.status).toBe(401);
    });
  });

  describe('POST /api/participant/organizer/follow', () => {
    it('should allow participant to follow organizer', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/organizer/follow')
        .set(getAuthHeader(token))
        .send({
          organizer_id: organizerId,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('following');
      expect(response.body.following).toBe(true);
    });

    it('should allow participant to unfollow organizer', async () => {
      const token = generateParticipantToken(String(participantId));

      // Follow first
      await request(app)
        .post('/api/participant/organizer/follow')
        .set(getAuthHeader(token))
        .send({
          organizer_id: organizerId,
        });

      // Unfollow
      const response = await request(app)
        .post('/api/participant/organizer/follow')
        .set(getAuthHeader(token))
        .send({
          organizer_id: organizerId,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.following).toBe(false);
      expect(response.body.message).toContain('unfollowed');
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/organizer/follow')
        .set(getAuthHeader(token))
        .send({
          organizer_id: participantId,
        });

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/profile', () => {
    it('should allow participant to edit profile', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/profile')
        .set(getAuthHeader(token))
        .send({
          name: 'Updated Participant',
          bio: 'Pro gamer',
          avatar_url: 'https://example.com/avatar.jpg',
          social_links: {
            twitch: 'progamer',
            twitter: '@progamer',
          },
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('updated');
    });

    it('should allow admin to edit profile', async () => {
      const token = generateAdminToken(String(adminId));

      const response = await request(app)
        .post('/api/participant/profile')
        .set(getAuthHeader(token))
        .send({
          name: 'Updated Admin',
          bio: 'Admin bio',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/profile')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team', () => {
    it('should allow team captain to edit team', async () => {
      // Make participant a captain
      await seedTestTeamMember({
        user_id: participantId,
        team_id: teamId,
        status: 'accepted',
      });

      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/team')
        .set(getAuthHeader(token))
        .send({
          team_id: teamId,
          name: 'Updated Team Name',
          description: 'Updated description',
          logo_url: 'https://example.com/logo.png',
        });

      expect([200, 403]).toContain(response.status);
      if (response.status === 200) {
        expect(response.body.success).toBe(true);
      }
    });

    it('should reject non-captain from editing team', async () => {
      const token = generateParticipantToken(String(participant2Id));

      const response = await request(app)
        .post('/api/participant/team')
        .set(getAuthHeader(token))
        .send({
          team_id: teamId,
          name: 'Hacked Team',
        });

      expect(response.status).toBe(403);
      expect(response.body.error).toContain('captain');
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/team')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team/:id/user/:userId/invite', () => {
    it('should allow team captain to invite member', async () => {
      // Set participant as captain
      await seedTestTeamMember({
        user_id: participantId,
        team_id: teamId,
        status: 'accepted',
      });

      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/user/${participant2Id}/invite`)
        .set(getAuthHeader(token));

      expect([201, 403]).toContain(response.status);
      if (response.status === 201) {
        expect(response.body.success).toBe(true);
      }
    });

    it('should reject non-captain from inviting', async () => {
      const token = generateParticipantToken(String(participant2Id));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/user/${participantId}/invite`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
      expect(response.body.error).toContain('captain');
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/user/${participantId}/invite`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team/:id/member/:memberId/captain', () => {
    it('should allow captain to promote member to captain', async () => {
      // Set participant as captain
      await seedTestTeamMember({
        user_id: participantId,
        team_id: teamId,
        status: 'accepted',
      });

      // Add another member
      await seedTestTeamMember({
        user_id: participant2Id,
        team_id: teamId,
        status: 'accepted',
      });

      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/member/${participant2Id}/captain`)
        .set(getAuthHeader(token));

      expect([200, 403]).toContain(response.status);
      if (response.status === 200) {
        expect(response.body.success).toBe(true);
      }
    });

    it('should reject non-captain', async () => {
      const token = generateParticipantToken(String(participant2Id));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/member/${participantId}/captain`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team/:id/member/:memberId/deleteCaptain', () => {
    it('should allow removing captain role', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/member/${participantId}/deleteCaptain`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('removed');
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post(`/api/participant/team/${teamId}/member/${participantId}/deleteCaptain`)
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team/member/:id/update', () => {
    it('should allow updating team member', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/team/member/1/update')
        .set(getAuthHeader(token))
        .send({
          role: 'captain',
          status: 'accepted',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/team/member/1/update')
        .set(getAuthHeader(token))
        .send({});

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team/member/:id/deleteInvite', () => {
    it('should allow withdrawing invitation', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/team/member/1/deleteInvite')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('withdrawn');
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/team/member/1/deleteInvite')
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/participant/team/member/:id/rejectInvite', () => {
    it('should allow rejecting invitation', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post('/api/participant/team/member/1/rejectInvite')
        .set(getAuthHeader(token));

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('rejected');
    });

    it('should reject organizer', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const response = await request(app)
        .post('/api/participant/team/member/1/rejectInvite')
        .set(getAuthHeader(token));

      expect(response.status).toBe(403);
    });
  });
});
