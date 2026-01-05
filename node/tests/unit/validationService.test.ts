import { ValidationService } from '../../src/services/validationService';
import { ValidationContext } from '../../src/models/types';

// Mock Prisma
jest.mock('../../src/config/database', () => ({
  prisma: {
    bracket: {
      findFirst: jest.fn(),
    },
    eventDetail: {
      findFirst: jest.fn(),
    },
    bracketDeadline: {
      findUnique: jest.fn(),
    },
    teamMember: {
      findFirst: jest.fn(),
    },
  },
}));

jest.mock('../../src/utils/logger');

import { prisma } from '../../src/config/database';

const mockBracketFindFirst = prisma.brackets.findFirst as jest.MockedFunction<typeof prisma.brackets.findFirst>;
const mockEventDetailFindFirst = prisma.event_details.findFirst as jest.MockedFunction<typeof prisma.event_details.findFirst>;
const mockBracketDeadlineFindUnique = prisma.bracketDeadline.findUnique as jest.MockedFunction<typeof prisma.bracketDeadline.findUnique>;
const mockTeamMemberFindFirst = prisma.team_members.findFirst as jest.MockedFunction<typeof prisma.team_members.findFirst>;

describe('ValidationService', () => {
  let service: ValidationService;

  beforeEach(() => {
    service = new ValidationService();
    jest.clearAllMocks();
  });

  describe('validateMatchExists', () => {
    it('should return match when it exists', async () => {
      const mockMatch = {
        id: 1,
        team1_id: '1',
        team1_position: 'W1',
        team2_id: '2',
        team2_position: 'W2',
        stage_name: 'U',
        inner_stage_name: 'e1',
        event_details_id: 100,
        order: null,
        winner_id: null,
        created_at: new Date(),
        updated_at: new Date(),
      };

      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const result = await service.validateMatchExists(context);

      expect(result).toEqual(mockMatch);
      expect(mockBracketFindFirst).toHaveBeenCalledWith({
        where: {
          team1_id: '1',
          team1_position: 'W1',
          team2_id: '2',
          team2_position: 'W2',
          event_details_id: 100,
        },
      });
    });

    it('should return null when match does not exist', async () => {
      mockBracketFindFirst.mockResolvedValueOnce(null);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const result = await service.validateMatchExists(context);

      expect(result).toBeNull();
    });

    it('should throw error on database failure', async () => {
      mockBracketFindFirst.mockRejectedValueOnce(new Error('Database error'));

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      await expect(service.validateMatchExists(context)).rejects.toThrow('Database error');
    });
  });

  describe('validateOrganizerPermission', () => {
    it('should return true when organizer owns the event', async () => {
      mockEventDetailFindFirst.mockResolvedValueOnce({ id: 100 } as any);

      const result = await service.validateOrganizerPermission('1', '100');

      expect(result).toBe(true);
      expect(mockEventDetailFindFirst).toHaveBeenCalledWith({
        where: {
          id: 100,
          user_id: 1,
        },
        select: {
          id: true,
        },
      });
    });

    it('should return false when organizer does not own the event', async () => {
      mockEventDetailFindFirst.mockResolvedValueOnce(null);

      const result = await service.validateOrganizerPermission('1', '100');

      expect(result).toBe(false);
    });

    it('should throw error on database failure', async () => {
      mockEventDetailFindFirst.mockRejectedValueOnce(new Error('Database error'));

      await expect(service.validateOrganizerPermission('1', '100')).rejects.toThrow('Database error');
    });
  });

  describe('validateDeadline', () => {
    it('should return true when current date is within deadline', async () => {
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      const tomorrow = new Date(today);
      tomorrow.setDate(tomorrow.getDate() + 1);

      const mockDeadlines = {
        U: {
          e1: {
            start_date: yesterday.toISOString().split('T')[0],
            end_date: tomorrow.toISOString().split('T')[0],
          },
        },
      };

      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(true);
    });

    it('should return false when current date is before deadline', async () => {
      // Use fixed future dates to avoid timezone edge cases
      const mockDeadlines = {
        U: {
          e1: {
            start_date: '2030-01-15',
            end_date: '2030-01-20',
          },
        },
      };

      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(false);
    });

    it('should return false when current date is after deadline', async () => {
      const today = new Date();
      const threeDaysAgo = new Date(today);
      threeDaysAgo.setDate(threeDaysAgo.getDate() - 3);
      const twoDaysAgo = new Date(today);
      twoDaysAgo.setDate(twoDaysAgo.getDate() - 2);

      const mockDeadlines = {
        U: {
          e1: {
            start_date: threeDaysAgo.toISOString().split('T')[0],
            end_date: twoDaysAgo.toISOString().split('T')[0],
          },
        },
      };

      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(false);
    });

    it('should handle JSON object deadlines (not string)', async () => {
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      const tomorrow = new Date(today);
      tomorrow.setDate(tomorrow.getDate() + 1);

      const mockDeadlines = {
        U: {
          e1: {
            start_date: yesterday.toISOString().split('T')[0],
            end_date: tomorrow.toISOString().split('T')[0],
          },
        },
      };

      // Return as object (Prisma automatically deserializes JSON)
      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(true);
    });

    it('should return false when no deadlines found for event', async () => {
      mockBracketDeadlineFindUnique.mockResolvedValueOnce(null);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(false);
    });

    it('should return false when stage not found in deadlines', async () => {
      const mockDeadlines = {
        L: {
          e1: {
            start_date: '2024-01-01',
            end_date: '2024-01-31',
          },
        },
      };

      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(false);
    });

    it('should return false when inner stage not found in deadlines', async () => {
      const mockDeadlines = {
        U: {
          e2: {
            start_date: '2024-01-01',
            end_date: '2024-01-31',
          },
        },
      };

      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const result = await service.validateDeadline('100', 'U', 'e1');

      expect(result).toBe(false);
    });

    it('should throw error on database failure', async () => {
      mockBracketDeadlineFindUnique.mockRejectedValueOnce(new Error('Database error'));

      await expect(service.validateDeadline('100', 'U', 'e1')).rejects.toThrow('Database error');
    });
  });

  describe('validateTeamMembership', () => {
    it('should return true when user is an accepted team member', async () => {
      mockTeamMemberFindFirst.mockResolvedValueOnce({ id: 1 } as any);

      const result = await service.validateTeamMembership('1', '10');

      expect(result).toBe(true);
      expect(mockTeamMemberFindFirst).toHaveBeenCalledWith({
        where: {
          user_id: 1,
          team_id: 10,
          status: 'accepted',
        },
        select: {
          id: true,
        },
      });
    });

    it('should return false when user is not a team member', async () => {
      mockTeamMemberFindFirst.mockResolvedValueOnce(null);

      const result = await service.validateTeamMembership('1', '10');

      expect(result).toBe(false);
    });

    it('should throw error on database failure', async () => {
      mockTeamMemberFindFirst.mockRejectedValueOnce(new Error('Database error'));

      await expect(service.validateTeamMembership('1', '10')).rejects.toThrow('Database error');
    });
  });

  describe('validateBracketUpdate', () => {
    const mockMatch = {
      id: 1,
      team1_id: '1',
      team1_position: 'W1',
      team2_id: '2',
      team2_position: 'W2',
      stage_name: 'U',
      inner_stage_name: 'e1',
      event_details_id: 100,
      order: null,
      winner_id: null,
      created_at: new Date(),
      updated_at: new Date(),
    };

    it('should validate successfully for organizer who owns event', async () => {
      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);
      // Mock organizer owns event
      mockEventDetailFindFirst.mockResolvedValueOnce({ id: 100 } as any);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'ORGANIZER',
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(true);
      expect(result.match).toEqual(mockMatch);
    });

    it('should fail for organizer who does not own event', async () => {
      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);
      // Mock organizer does not own event
      mockEventDetailFindFirst.mockResolvedValueOnce(null);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '2',
        userRole: 'ORGANIZER',
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(false);
      expect(result.error).toContain('not your event');
    });

    it('should validate successfully for participant within deadline and team member', async () => {
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      const tomorrow = new Date(today);
      tomorrow.setDate(tomorrow.getDate() + 1);

      const mockDeadlines = {
        U: {
          e1: {
            start_date: yesterday.toISOString().split('T')[0],
            end_date: tomorrow.toISOString().split('T')[0],
          },
        },
      };

      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);
      // Mock deadlines
      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);
      // Mock team membership
      mockTeamMemberFindFirst.mockResolvedValueOnce({ id: 1 } as any);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        myTeamId: '1',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: true,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(true);
      expect(result.match).toEqual(mockMatch);
    });

    it('should fail for participant outside deadline', async () => {
      const today = new Date();
      const twoDaysAgo = new Date(today);
      twoDaysAgo.setDate(twoDaysAgo.getDate() - 2);
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);

      const mockDeadlines = {
        U: {
          e1: {
            start_date: twoDaysAgo.toISOString().split('T')[0],
            end_date: yesterday.toISOString().split('T')[0],
          },
        },
      };

      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);
      // Mock deadlines (expired)
      mockBracketDeadlineFindUnique.mockResolvedValueOnce({
        id: 1,
        event_details_id: 100,
        deadlines: mockDeadlines,
        created_at: new Date(),
        updated_at: new Date(),
      } as any);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        myTeamId: '1',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: true,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(false);
      expect(result.error).toContain('timeframe');
    });

    it('should fail for participant who is not team member', async () => {
      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);
      // Mock team membership (not a member)
      mockTeamMemberFindFirst.mockResolvedValueOnce(null);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        myTeamId: '1',
        userId: '99',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(false);
      expect(result.error).toContain('not a member');
    });

    it('should validate successfully for admin without additional checks', async () => {
      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'ADMIN',
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(true);
      expect(result.match).toEqual(mockMatch);
    });

    it('should fail when match does not exist', async () => {
      // Mock match does not exist
      mockBracketFindFirst.mockResolvedValueOnce(null);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(false);
      expect(result.error).toContain('not found');
    });

    it('should fail for participant without myTeamId', async () => {
      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(false);
      expect(result.error).toContain('No valid team ID');
    });

    it('should fail for invalid user role', async () => {
      // Mock match exists
      mockBracketFindFirst.mockResolvedValueOnce(mockMatch);

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'INVALID' as any,
        willCheckDeadline: false,
      };

      const result = await service.validateBracketUpdate(context);

      expect(result.valid).toBe(false);
      expect(result.error).toContain('No valid user role');
    });

    it('should throw error on database failure', async () => {
      mockBracketFindFirst.mockRejectedValueOnce(new Error('Database error'));

      const context: ValidationContext = {
        eventId: '100',
        team1Id: '1',
        team2Id: '2',
        team1Position: 'W1',
        team2Position: 'W2',
        userId: '1',
        userRole: 'PARTICIPANT',
        willCheckDeadline: false,
      };

      await expect(service.validateBracketUpdate(context)).rejects.toThrow('Database error');
    });
  });
});
