import { z } from 'zod';

/**
 * Zod Validators for API Request Bodies
 *
 * These schemas validate incoming request data
 */

// Bracket report schema
export const bracketReportSchema = z.object({
  team1_id: z.string().nullable(),
  team1_position: z.string(),
  team2_id: z.string().nullable(),
  team2_position: z.string(),
  my_team_id: z.string().optional(),
  willCheckDeadline: z.boolean().default(true),
  reportData: z.object({
    score: z.tuple([z.number(), z.number()]),
    stageName: z.string(),
    realWinners: z.array(z.string().nullable()),
    organizerWinners: z.array(z.string().nullable()),
    team1Id: z.string(),
    team2Id: z.string(),
    position: z.string(),
    completeMatchStatus: z.string(),
    randomWinners: z.array(z.string().nullable()),
    defaultWinners: z.array(z.string().nullable()),
    disqualified: z.boolean(),
    disputeResolved: z.array(z.boolean().nullable()),
    team1Winners: z.array(z.string().nullable()),
    team2Winners: z.array(z.string().nullable()),
    matchStatus: z.array(z.string()),
  }),
});

// Submit dispute schema
export const submitDisputeSchema = z.object({
  report_id: z.string(),
  match_number: z.number().int().min(0),
  dispute_userId: z.string(),
  dispute_teamId: z.string(),
  dispute_teamNumber: z.number().int().min(0).max(1),
  dispute_reason: z.string().min(1),
  dispute_description: z.string().nullable().optional(),
  dispute_image_videos: z.array(z.string()).default([]),
  team1_position: z.string(),
  team2_position: z.string(),
});

// Respond to dispute schema
export const respondDisputeSchema = z.object({
  response_teamId: z.string(),
  response_teamNumber: z.number().int().min(0).max(1),
  response_explanation: z.string().nullable().optional(),
  response_userId: z.string(),
  response_image_videos: z.array(z.string()).default([]),
});

// Resolve dispute schema
export const resolveDisputeSchema = z.object({
  resolution_winner: z.string(),
  resolution_resolved_by: z.string(),
  match_number: z.number().int().min(0),
  team1_position: z.string().optional(),
  team2_position: z.string().optional(),
  // Optional bracket update data
  reportData: z.object({
    score: z.tuple([z.number(), z.number()]),
    stageName: z.string(),
    realWinners: z.array(z.string().nullable()),
    organizerWinners: z.array(z.string().nullable()),
    team1Id: z.string(),
    team2Id: z.string(),
    position: z.string(),
    completeMatchStatus: z.string(),
    randomWinners: z.array(z.string().nullable()),
    defaultWinners: z.array(z.string().nullable()),
    disqualified: z.boolean(),
    disputeResolved: z.array(z.boolean().nullable()),
    team1Winners: z.array(z.string().nullable()),
    team2Winners: z.array(z.string().nullable()),
    matchStatus: z.array(z.string()),
  }).optional(),
});

// Type exports for TypeScript
export type BracketReportRequest = z.infer<typeof bracketReportSchema>;
export type SubmitDisputeRequest = z.infer<typeof submitDisputeSchema>;
export type RespondDisputeRequest = z.infer<typeof respondDisputeSchema>;
export type ResolveDisputeRequest = z.infer<typeof resolveDisputeSchema>;
