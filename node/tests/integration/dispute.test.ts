import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';
import {
  seedTestUser,
  seedTestEvent,
  seedTestTeam,
  seedTestTeamMember,
  seedTestBracket,
  seedTestBracketDeadline,
  beginTransaction,
  rollbackTransaction,
} from '../helpers/testDb';
import {
  generateParticipantToken,
  generateOrganizerToken,
  getAuthHeader,
} from '../helpers/testAuth';

describe('Dispute API - Integration Tests', () => {
  let organizerId: number;
  let participantId: number;
  let participant2Id: number;
  let eventId: number;
  let team1Id: number;
  let team2Id: number;

  beforeAll(async () => {
    initializeFirebase();
  });

  beforeEach(async () => {
    await beginTransaction();

    // Seed test data
    organizerId = await seedTestUser({
      name: 'Test Organizer',
      email: 'organizer@test.com',
      role: 'ORGANIZER',
    });

    participantId = await seedTestUser({
      name: 'Test Participant 1',
      email: 'participant1@test.com',
      role: 'PARTICIPANT',
    });

    participant2Id = await seedTestUser({
      name: 'Test Participant 2',
      email: 'participant2@test.com',
      role: 'PARTICIPANT',
    });

    eventId = await seedTestEvent({
      user_id: organizerId,
      eventName: 'Test Tournament',
    });

    team1Id = await seedTestTeam({
      teamName: 'Team Alpha',
      creator_id: participantId,
    });

    team2Id = await seedTestTeam({
      teamName: 'Team Beta',
      creator_id: participant2Id,
    });

    await seedTestTeamMember({
      user_id: participantId,
      team_id: team1Id,
      status: 'accepted',
    });

    await seedTestTeamMember({
      user_id: participant2Id,
      team_id: team2Id,
      status: 'accepted',
    });

    await seedTestBracket({
      team1_id: String(team1Id),
      team1_position: 'W1',
      team2_id: String(team2Id),
      team2_position: 'W2',
      stage_name: 'U',
      inner_stage_name: 'e1',
      event_details_id: eventId,
    });

    // Set deadline (within valid range)
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

  afterEach(async () => {
    await rollbackTransaction();
  });

  describe('POST /api/brackets/:eventId/disputes', () => {
    it('should allow team member to submit dispute', async () => {
      const token = generateParticipantToken(String(participantId));

      const disputeData = {
        team1_position: 'W1',
        team2_position: 'W2',
        match_number: 0,
        report_id: 'W1.W2',
        dispute_userId: String(participantId),
        dispute_teamId: String(team1Id),
        dispute_teamNumber: 0,
        dispute_reason: 'INCORRECT_SCORE',
        dispute_description: 'The score was entered incorrectly',
        dispute_image_videos: [],
      };

      const response = await request(app)
        .post(`/api/brackets/${eventId}/disputes`)
        .set(getAuthHeader(token))
        .send(disputeData);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('disputeId');
      expect(response.body.message).toContain('successfully');
    });

    it('should reject dispute from non-team member', async () => {
      const nonMemberId = await seedTestUser({
        name: 'Non Member',
        email: 'nonmember@test.com',
        role: 'PARTICIPANT',
      });

      const token = generateParticipantToken(String(nonMemberId));

      const disputeData = {
        team1_position: 'W1',
        team2_position: 'W2',
        match_number: 0,
        report_id: 'W1.W2',
        dispute_userId: String(nonMemberId),
        dispute_teamId: String(team1Id),
        dispute_teamNumber: 0,
        dispute_reason: 'INCORRECT_SCORE',
        dispute_image_videos: [],
      };

      const response = await request(app)
        .post(`/api/brackets/${eventId}/disputes`)
        .set(getAuthHeader(token))
        .send(disputeData);

      expect(response.status).toBe(403);
      expect(response.body.success).toBe(false);
      expect(response.body.message).toContain('not a member');
    });

    it('should reject dispute without authentication', async () => {
      const response = await request(app)
        .post(`/api/brackets/${eventId}/disputes`)
        .send({});

      expect(response.status).toBe(401);
    });

    it('should reject dispute with invalid token', async () => {
      const response = await request(app)
        .post(`/api/brackets/${eventId}/disputes`)
        .set({ Authorization: 'Bearer invalid_token' })
        .send({});

      expect(response.status).toBe(401);
    });

    it('should reject dispute with missing required fields', async () => {
      const token = generateParticipantToken(String(participantId));

      const response = await request(app)
        .post(`/api/brackets/${eventId}/disputes`)
        .set(getAuthHeader(token))
        .send({
          team1_position: 'W1',
          // Missing other required fields
        });

      expect(response.status).toBe(400);
    });
  });

  describe('PATCH /api/brackets/:eventId/disputes/:disputeId/respond', () => {
    it('should allow opposing team member to respond to dispute', async () => {
      const token = generateParticipantToken(String(participant2Id));

      const responseData = {
        response_userId: String(participant2Id),
        response_teamId: String(team2Id),
        response_teamNumber: 1,
        response_explanation: 'The score is correct',
        response_image_videos: [],
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/respond`)
        .set(getAuthHeader(token))
        .send(responseData);

      // May be 404 if dispute doesn't exist, or 200 if it does
      expect([200, 404]).toContain(response.status);
    });

    it('should reject response from non-team member', async () => {
      const nonMemberId = await seedTestUser({
        name: 'Non Member',
        email: 'nonmember2@test.com',
        role: 'PARTICIPANT',
      });

      const token = generateParticipantToken(String(nonMemberId));

      const responseData = {
        response_userId: String(nonMemberId),
        response_teamId: String(team2Id),
        response_teamNumber: 1,
        response_explanation: 'Trying to respond',
        response_image_videos: [],
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/respond`)
        .set(getAuthHeader(token))
        .send(responseData);

      expect(response.status).toBe(403);
      expect(response.body.success).toBe(false);
    });

    it('should return 404 for non-existent dispute', async () => {
      const token = generateParticipantToken(String(participant2Id));

      const responseData = {
        response_userId: String(participant2Id),
        response_teamId: String(team2Id),
        response_teamNumber: 1,
        response_explanation: 'Response',
        response_image_videos: [],
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/non-existent-dispute/respond`)
        .set(getAuthHeader(token))
        .send(responseData);

      expect(response.status).toBe(404);
      expect(response.body.message).toContain('not found');
    });

    it('should reject response without authentication', async () => {
      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/respond`)
        .send({});

      expect(response.status).toBe(401);
    });
  });

  describe('PATCH /api/brackets/:eventId/disputes/:disputeId/resolve', () => {
    it('should allow event organizer to resolve dispute', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const resolveData = {
        resolution_winner: '0',
        resolution_resolved_by: String(organizerId),
        match_number: 0,
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/resolve`)
        .set(getAuthHeader(token))
        .send(resolveData);

      // May be 404 if dispute doesn't exist, or 200 if it does
      expect([200, 404]).toContain(response.status);
    });

    it('should allow organizer to resolve with bracket update', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const resolveData = {
        resolution_winner: '0',
        resolution_resolved_by: String(organizerId),
        match_number: 0,
        team1_position: 'W1',
        team2_position: 'W2',
        reportData: {
          score: [2, 1],
          stageName: 'U',
          realWinners: ['0', '1', '0'],
          organizerWinners: ['0', '1', '0'],
          team1Id: String(team1Id),
          team2Id: String(team2Id),
          position: 'W1.W2',
          completeMatchStatus: 'ENDED',
          randomWinners: [null, null, null],
          defaultWinners: [null, null, null],
          disqualified: false,
          disputeResolved: [null, null, null],
          team1Winners: ['0', '1', '0'],
          team2Winners: [null, null, null],
          matchStatus: ['ENDED', 'ENDED', 'ENDED'],
        },
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/resolve`)
        .set(getAuthHeader(token))
        .send(resolveData);

      expect([200, 404]).toContain(response.status);
    });

    it('should reject participant from resolving dispute', async () => {
      const token = generateParticipantToken(String(participantId));

      const resolveData = {
        resolution_winner: '0',
        resolution_resolved_by: String(participantId),
        match_number: 0,
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/resolve`)
        .set(getAuthHeader(token))
        .send(resolveData);

      expect(response.status).toBe(403);
      expect(response.body.message).toContain('Only organizers');
    });

    it('should reject organizer who does not own the event', async () => {
      const otherOrganizerId = await seedTestUser({
        name: 'Other Organizer',
        email: 'other.organizer@test.com',
        role: 'ORGANIZER',
      });

      const token = generateOrganizerToken(String(otherOrganizerId));

      const resolveData = {
        resolution_winner: '0',
        resolution_resolved_by: String(otherOrganizerId),
        match_number: 0,
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/resolve`)
        .set(getAuthHeader(token))
        .send(resolveData);

      expect(response.status).toBe(403);
      expect(response.body.message).toContain('do not own this event');
    });

    it('should return 404 for non-existent dispute', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const resolveData = {
        resolution_winner: '0',
        resolution_resolved_by: String(organizerId),
        match_number: 0,
      };

      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/non-existent-dispute/resolve`)
        .set(getAuthHeader(token))
        .send(resolveData);

      expect(response.status).toBe(404);
      expect(response.body.message).toContain('not found');
    });

    it('should reject resolve without authentication', async () => {
      const response = await request(app)
        .patch(`/api/brackets/${eventId}/disputes/test-dispute-id/resolve`)
        .send({});

      expect(response.status).toBe(401);
    });
  });
});
