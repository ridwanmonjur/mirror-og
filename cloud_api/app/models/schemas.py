"""
Pydantic models for request/response validation.
Replaces Zod schemas from Node.js controllers.
"""
from typing import Any, Optional, List, Dict
from pydantic import BaseModel, Field
from datetime import datetime


# ============================================================================
# Standard API Response
# ============================================================================

class APIResponse(BaseModel):
    """Standard response format matching Node.js API."""
    success: bool
    data: Any | None = None
    message: str | None = None
    error: str | None = None


# ============================================================================
# User Schemas
# ============================================================================

class UserResponse(BaseModel):
    """User data response."""
    id: int
    name: str | None = None
    email: str | None = None
    role: str
    created_at: datetime | None = None


class UserSettingsUpdate(BaseModel):
    """User settings update request."""
    setting_key: str
    setting_value: Any


# ============================================================================
# Team Schemas
# ============================================================================

class TeamCreateRequest(BaseModel):
    """Create team request."""
    name: str = Field(..., min_length=1, max_length=255)
    tag: str | None = Field(None, max_length=50)
    description: str | None = None
    game_id: int | None = None
    region: str | None = None


class TeamUpdateRequest(BaseModel):
    """Update team request."""
    name: str | None = Field(None, min_length=1, max_length=255)
    tag: str | None = Field(None, max_length=50)
    description: str | None = None
    logo: str | None = None
    banner: str | None = None


class TeamMemberInviteRequest(BaseModel):
    """Invite member to team request."""
    user_id: int
    role: str = "member"


class TeamResponse(BaseModel):
    """Team data response."""
    id: int
    name: str
    tag: str | None = None
    logo: str | None = None
    captain_id: int | None = None
    created_at: datetime | None = None


# ============================================================================
# Event Schemas
# ============================================================================

class EventSearchRequest(BaseModel):
    """Event search/filter request."""
    search: str | None = None
    game_id: int | None = None
    tier_id: int | None = None
    status: str | None = None
    region: str | None = None
    page: int = 1
    limit: int = 20


class EventCreateRequest(BaseModel):
    """Create event request."""
    name: str = Field(..., min_length=1, max_length=255)
    description: str | None = None
    start_date: datetime
    end_date: datetime
    entry_fee: float | None = None
    prize_pool: float | None = None
    max_teams: int
    tier_id: int
    game_id: int
    bracket_type: str = "single_elimination"
    games_per_match: int = 3


class EventResponse(BaseModel):
    """Event data response."""
    id: int
    name: str
    description: str | None = None
    organizer_id: int
    start_date: datetime | None = None
    end_date: datetime | None = None
    status: str | None = None
    created_at: datetime | None = None


# ============================================================================
# Bracket/Match Schemas
# ============================================================================

class BracketUpdateRequest(BaseModel):
    """Update bracket/match request."""
    team1_score: int | None = None
    team2_score: int | None = None
    winner_team_id: int | None = None
    status: str | None = None


class MatchResultRequest(BaseModel):
    """Submit match result request."""
    bracket_id: int
    team_id: int
    scores: List[int | None] = Field(..., min_length=1, max_length=5)


class BracketValidateRequest(BaseModel):
    """Validate bracket request."""
    event_id: int
    bracket_data: Dict[str, Any]


# ============================================================================
# Tournament/Deadline Schemas
# ============================================================================

class DeadlineTaskRequest(BaseModel):
    """Deadline processing task request."""
    event_id: int
    tier_id: int
    is_league: bool = False
    brackets: List[Dict[str, Any]]


class RoomBlockRequest(BaseModel):
    """Block/unblock chat room request."""
    event_id: int
    room_id: str
    blocked: bool


class BatchReportRequest(BaseModel):
    """Batch create reports request."""
    event_id: int
    reports: List[Dict[str, Any]]


class BatchDisputeRequest(BaseModel):
    """Batch create disputes request."""
    event_id: int
    disputes: List[Dict[str, Any]]


# ============================================================================
# Notification Schemas
# ============================================================================

class NotificationCreateRequest(BaseModel):
    """Create notification request."""
    user_id: int
    type: str
    title: str
    message: str
    data: Dict[str, Any] | None = None


class NotificationResponse(BaseModel):
    """Notification data response."""
    id: int
    type: str
    title: str
    message: str
    read: bool
    created_at: datetime | None = None


# ============================================================================
# Award/Achievement Schemas
# ============================================================================

class AwardCreateRequest(BaseModel):
    """Create award request."""
    join_events_id: int
    award_id: int
    team_id: int


class AchievementCreateRequest(BaseModel):
    """Create achievement request."""
    join_event_id: int
    title: str
    description: str


# ============================================================================
# Media Schemas
# ============================================================================

class MediaUploadResponse(BaseModel):
    """Media upload response."""
    url: str
    filename: str
    size: int


# ============================================================================
# Firestore Schemas
# ============================================================================

class FirestoreBracketData(BaseModel):
    """Firestore bracket/match data."""
    realWinners: List[str | None] = Field(default_factory=lambda: [None, None, None])
    team1Winners: List[str | None] = Field(default_factory=lambda: [None, None, None])
    team2Winners: List[str | None] = Field(default_factory=lambda: [None, None, None])
    disputeResolved: List[bool | None] = Field(default_factory=lambda: [None, None, None])
    defaultWinners: List[bool | None] = Field(default_factory=lambda: [None, None, None])
    randomWinners: List[bool | None] = Field(default_factory=lambda: [None, None, None])
    score: List[int] = Field(default_factory=lambda: [0, 0])
    disqualified: bool = False


class DisputeCreateRequest(BaseModel):
    """Create dispute request."""
    event_id: int
    bracket_id: str
    game_index: int
    dispute_team_number: int
    dispute_team_id: int
    reason: str | None = None


class DisputeResponseRequest(BaseModel):
    """Respond to dispute request."""
    event_id: int
    dispute_id: str
    response_team_number: int
    response_team_id: int
    response: str | None = None
