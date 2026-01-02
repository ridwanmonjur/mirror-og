import { FieldValue } from 'firebase-admin/firestore';

// User roles from Laravel
export type UserRole = 'PARTICIPANT' | 'ORGANIZER' | 'ADMIN';

// User data from JWT
export interface JWTUser {
  id: string;
  role: UserRole;
  email?: string;
  name?: string;
}

// Database models
export interface Match {
  id: number;
  team1_id: string | null;
  team1_position: string;
  team2_id: string | null;
  team2_position: string;
  stage_name: string;
  inner_stage_name: string;
  event_details_id: string;
  winner_next_position: string | null;
  loser_next_position: string | null;
}

export interface BracketDeadline {
  id: number;
  event_details_id: string;
  deadlines: Record<string, Record<string, DeadlineData>>;
}

export interface DeadlineData {
  start_date: string;
  end_date: string;
}

export interface TeamMember {
  id: number;
  user_id: string;
  team_id: string;
  status: 'accepted' | 'pending' | 'rejected';
}

export interface EventDetail {
  id: number;
  user_id: string;
  eventName: string;
}

// Firestore types
export interface BracketReport {
  score: [number, number];
  stageName: string;
  realWinners: (string | null)[];
  organizerWinners: (string | null)[];
  team1Id: string;
  team2Id: string;
  position: string;
  completeMatchStatus: string;
  randomWinners: (string | null)[];
  defaultWinners: (string | null)[];
  disqualified: boolean;
  disputeResolved: (boolean | null)[];
  team1Winners: (string | null)[];
  team2Winners: (string | null)[];
  matchStatus: string[];
}

export interface Dispute {
  report_id: string;
  match_number: number;
  event_id: string;
  dispute_userId: string;
  dispute_teamId: string;
  dispute_teamNumber: number;
  dispute_reason: string;
  dispute_description: string | null;
  dispute_image_videos: string[];
  response_userId: string | null;
  response_teamId: string | null;
  response_teamNumber: number | null;
  response_explanation: string | null;
  response_image_videos: string[] | null;
  resolution_winner: string | null;
  resolution_resolved_by: string | null;
  created_at: FieldValue | Date;
  updated_at: FieldValue | Date;
}

// Validation context
export interface ValidationContext {
  eventId: string;
  team1Id: string | null;
  team2Id: string | null;
  team1Position: string;
  team2Position: string;
  myTeamId?: string;
  userId: string;
  userRole: UserRole;
  willCheckDeadline: boolean;
}

// API request/response types
export interface ValidationResult {
  valid: boolean;
  error?: string;
  match?: Match;
}

export interface ApiResponse<T = any> {
  success: boolean;
  message?: string;
  data?: T;
}

export interface ApiError {
  success: false;
  message: string;
  error?: string;
}
