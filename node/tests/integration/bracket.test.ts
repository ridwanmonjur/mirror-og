import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';
import {
  clearTestData,
  seedTestUser,
  seedTestEvent,
  seedTestTeam,
  seedTestTeamMember,
  seedTestBracket,
  seedTestBracketDeadline,
} from '../helpers/testDb';
import {
  generateParticipantToken,
  generateOrganizerToken,
  getAuthHeader,
} from '../helpers/testAuth';

describe('Bracket API - Integration Tests', () => {
  let organizerId: number;
  let participantId: number;
  let eventId: number;
  let teamId: number;
  // let _matchId: number; // Reserved for future use

  beforeAll(async () => {
    // Initialize Firebase (will use emulator from env vars)
    initializeFirebase();
  });

  beforeEach(async () => {
    // Clear test data before each test
    await clearTestData();

    // Seed test data
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

    eventId = await seedTestEvent({
      id: 1,
      user_id: organizerId,
      eventName: 'Test Tournament',
    });

    teamId = await seedTestTeam({
      id: 1,
      teamName: 'Test Team',
      creator_id: participantId,
    });

    await seedTestTeamMember({
      user_id: participantId,
      team_id: teamId,
      status: 'accepted',
    });

    /*_matchId =*/ await seedTestBracket({
      team1_id: String(teamId),
      team1_position: 'W1',
      team2_id: '2',
      team2_position: 'W2',
      stage_name: 'U',
      inner_stage_name: 'e1',
      event_details_id: eventId,
    });

    // Seed bracket deadline (within current date)
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

  describe('POST /api/brackets/:eventId/report', () => {
    it('should allow organizer to report match results', async () => {
      const token = generateOrganizerToken(String(organizerId));

      const reportData = {
        team1_id: String(teamId),
        team1_position: 'W1',
        team2_id: '2',
        team2_position: 'W2',
        willCheckDeadline: false,
        reportData: {
          score: [2, 1],
          stageName: 'U',
          realWinners: ['0', '1', '0'],
          organizerWinners: ['0', '1', '0'],
          team1Id: String(teamId),
          team2Id: '2',
          position: 'W1.W2',
          completeMatchStatus: 'ENDED',
          randomWinners: [null, null, null],
          defaultWinners: [null, null, null],
          disqualified: false,
          disputeResolved: [null, null, null],
          team1Winners: ['0', '1', '0'],
          team2Winners: ['1', '0', '1'],
          matchStatus: ['ENDED', 'ENDED', 'ENDED'],
        },
      };

      const response = await request(app)
        .post(`/api/brackets/${eventId}/report`)
        .set(getAuthHeader(token))
        .send(reportData);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toBe('Report updated successfully');
    });

    it('should allow participant to report within deadline', async () => {
      const token = generateParticipantToken(String(participantId));

      const reportData = {
        team1_id: String(teamId),
        team1_position: 'W1',
        team2_id: '2',
        team2_position: 'W2',
        my_team_id: String(teamId),
        willCheckDeadline: true,
        reportData: {
          score: [2, 1],
          stageName: 'U',
          realWinners: ['0', '1', '0'],
          organizerWinners: [null, null, null],
          team1Id: String(teamId),
          team2Id: '2',
          position: 'W1.W2',
          completeMatchStatus: 'ONGOING',
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
        .post(`/api/brackets/${eventId}/report`)
        .set(getAuthHeader(token))
        .send(reportData);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should block participant when deadline passed', async () => {
      // Update deadline to past dates
      const yesterday = new Date();
      yesterday.setDate(yesterday.getDate() - 2);
      const twoDaysAgo = new Date();
      twoDaysAgo.setDate(twoDaysAgo.getDate() - 3);

      await seedTestBracketDeadline({
        event_details_id: eventId,
        deadlines: {
          U: {
            e1: {
              start_date: twoDaysAgo.toISOString().split('T')[0],
              end_date: yesterday.toISOString().split('T')[0],
            },
          },
        },
      });

      const token = generateParticipantToken(String(participantId));

      const reportData = {
        team1_id: String(teamId),
        team1_position: 'W1',
        team2_id: '2',
        team2_position: 'W2',
        my_team_id: String(teamId),
        willCheckDeadline: true,
        reportData: {
          score: [0, 0],
          stageName: 'U',
          realWinners: [null, null, null],
          organizerWinners: [null, null, null],
          team1Id: String(teamId),
          team2Id: '2',
          position: 'W1.W2',
          completeMatchStatus: 'UPCOMING',
          randomWinners: [null, null, null],
          defaultWinners: [null, null, null],
          disqualified: false,
          disputeResolved: [null, null, null],
          team1Winners: [null, null, null],
          team2Winners: [null, null, null],
          matchStatus: ['UPCOMING', 'UPCOMING', 'UPCOMING'],
        },
      };

      const response = await request(app)
        .post(`/api/brackets/${eventId}/report`)
        .set(getAuthHeader(token))
        .send(reportData);

      expect(response.status).toBe(403);
      expect(response.body.success).toBe(false);
      expect(response.body.message).toContain('timeframe');
    });

    it('should block participant if not team member', async () => {
      // Create another user who is not a team member
      const nonMemberId = await seedTestUser({
        id: 3,
        name: 'Non Member',
        email: 'nonmember@test.com',
        role: 'PARTICIPANT',
      });

      const token = generateParticipantToken(String(nonMemberId));

      const reportData = {
        team1_id: String(teamId),
        team1_position: 'W1',
        team2_id: '2',
        team2_position: 'W2',
        my_team_id: String(teamId),
        willCheckDeadline: true,
        reportData: {
          score: [0, 0],
          stageName: 'U',
          realWinners: [null, null, null],
          organizerWinners: [null, null, null],
          team1Id: String(teamId),
          team2Id: '2',
          position: 'W1.W2',
          completeMatchStatus: 'UPCOMING',
          randomWinners: [null, null, null],
          defaultWinners: [null, null, null],
          disqualified: false,
          disputeResolved: [null, null, null],
          team1Winners: [null, null, null],
          team2Winners: [null, null, null],
          matchStatus: ['UPCOMING', 'UPCOMING', 'UPCOMING'],
        },
      };

      const response = await request(app)
        .post(`/api/brackets/${eventId}/report`)
        .set(getAuthHeader(token))
        .send(reportData);

      expect(response.status).toBe(403);
      expect(response.body.success).toBe(false);
      expect(response.body.message).toContain('not a member');
    });

    it('should reject invalid JWT', async () => {
      const response = await request(app)
        .post(`/api/brackets/${eventId}/report`)
        .set({ Authorization: 'Bearer invalid_token' })
        .send({});

      expect(response.status).toBe(401);
      expect(response.body.success).toBe(false);
    });

    it('should reject request without authentication', async () => {
      const response = await request(app)
        .post(`/api/brackets/${eventId}/report`)
        .send({});

      expect(response.status).toBe(401);
      expect(response.body.success).toBe(false);
    });
  });
});
