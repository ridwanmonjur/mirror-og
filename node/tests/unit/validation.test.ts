import { ValidationService } from '../../src/services/validationService';
import {
  clearTestData,
  seedTestUser,
  seedTestEvent,
  seedTestTeam,
  seedTestTeamMember,
  seedTestBracket,
  seedTestBracketDeadline,
} from '../helpers/testDb';
import { ValidationContext } from '../../src/models/types';

describe('ValidationService - Unit Tests', () => {
  let validationService: ValidationService;
  let organizerId: number;
  let participantId: number;
  let eventId: number;
  let teamId: number;

  beforeAll(() => {
    validationService = new ValidationService();
  });

  beforeEach(async () => {
    await clearTestData();

    organizerId = await seedTestUser({
      id: 1,
      name: 'Organizer',
      email: 'organizer@test.com',
      role: 'ORGANIZER',
    });

    participantId = await seedTestUser({
      id: 2,
      name: 'Participant',
      email: 'participant@test.com',
      role: 'PARTICIPANT',
    });

    eventId = await seedTestEvent({
      id: 1,
      user_id: organizerId,
      eventName: 'Test Event',
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
  });

  describe('validateMatchExists', () => {
    it('should return match when found', async () => {
      await seedTestBracket({
        team1_id: String(teamId),
        team1_position: 'W1',
        team2_id: '2',
        team2_position: 'W2',
        stage_name: 'U',
        inner_stage_name: 'e1',
        event_details_id: eventId,
      });

      const context: ValidationContext = {
        eventId: String(eventId),
        team1Id: String(teamId),
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: String(participantId),
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const match = await validationService.validateMatchExists(context);

      expect(match).not.toBeNull();
      expect(match?.team1_position).toBe('W1');
      expect(match?.team2_position).toBe('W2');
      expect(match?.stage_name).toBe('U');
    });

    it('should return null when match not found', async () => {
      const context: ValidationContext = {
        eventId: String(eventId),
        team1Id: '999',
        team2Id: '888',
        team1Position: 'W99',
        team2Position: 'W88',
        userId: String(participantId),
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const match = await validationService.validateMatchExists(context);

      expect(match).toBeNull();
    });
  });

  describe('validateOrganizerPermission', () => {
    it('should return true when organizer owns event', async () => {
      const ownsEvent = await validationService.validateOrganizerPermission(
        String(organizerId),
        String(eventId)
      );

      expect(ownsEvent).toBe(true);
    });

    it('should return false when organizer does not own event', async () => {
      const ownsEvent = await validationService.validateOrganizerPermission(
        String(participantId),
        String(eventId)
      );

      expect(ownsEvent).toBe(false);
    });

    it('should return false for non-existent event', async () => {
      const ownsEvent = await validationService.validateOrganizerPermission(
        String(organizerId),
        '999'
      );

      expect(ownsEvent).toBe(false);
    });
  });

  describe('validateDeadline', () => {
    it('should return true when within deadline', async () => {
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

      const isValid = await validationService.validateDeadline(
        String(eventId),
        'U',
        'e1'
      );

      expect(isValid).toBe(true);
    });

    it('should return false when deadline passed', async () => {
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

      const isValid = await validationService.validateDeadline(
        String(eventId),
        'U',
        'e1'
      );

      expect(isValid).toBe(false);
    });

    it('should return false when no deadline exists', async () => {
      const isValid = await validationService.validateDeadline(
        String(eventId),
        'NONEXISTENT',
        'e1'
      );

      expect(isValid).toBe(false);
    });
  });

  describe('validateTeamMembership', () => {
    it('should return true when user is accepted team member', async () => {
      const isMember = await validationService.validateTeamMembership(
        String(participantId),
        String(teamId)
      );

      expect(isMember).toBe(true);
    });

    it('should return false when user is not team member', async () => {
      const nonMemberId = await seedTestUser({
        id: 3,
        name: 'Non Member',
        email: 'nonmember@test.com',
        role: 'PARTICIPANT',
      });

      const isMember = await validationService.validateTeamMembership(
        String(nonMemberId),
        String(teamId)
      );

      expect(isMember).toBe(false);
    });

    it('should return false when team member status is not accepted', async () => {
      const pendingUserId = await seedTestUser({
        id: 4,
        name: 'Pending User',
        email: 'pending@test.com',
        role: 'PARTICIPANT',
      });

      await seedTestTeamMember({
        user_id: pendingUserId,
        team_id: teamId,
        status: 'pending',
      });

      const isMember = await validationService.validateTeamMembership(
        String(pendingUserId),
        String(teamId)
      );

      expect(isMember).toBe(false);
    });
  });
});
