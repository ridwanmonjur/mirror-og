"""
SQLAlchemy Core Table definitions (query builder only, no ORM).
Converted from node/prisma/schema.prisma

Priority tables implemented:
1. users - Base user table
2. event_details - Tournament events
3. brackets - Match brackets
4. teams - Team management
5. team_members - Team roster
6. bracket_deadlines - Deadline tracking
7. event_tier - Tournament tiers
"""
from sqlalchemy import (
    Table, Column, BigInteger, Integer, String, DateTime, Text,
    ForeignKey, JSON, Index, Boolean, Numeric, Date, Enum
)
from app.core.database import metadata


# Users table
users = Table(
    "users",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("name", String(255)),
    Column("email", String(255), unique=True),
    Column("email_verified_at", DateTime),
    Column("password", String(255)),
    Column("remember_token", String(100)),
    Column("role", String(50)),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
)

# Event details table
event_details = Table(
    "event_details",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("name", String(255), nullable=False),
    Column("description", Text),
    Column("organizer_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE")),
    Column("start_date", DateTime),
    Column("end_date", DateTime),
    Column("entry_fee", Numeric(10, 2)),
    Column("prize_pool", Numeric(10, 2)),
    Column("max_teams", Integer),
    Column("tier_id", BigInteger, ForeignKey("event_tier.id")),
    Column("type_id", BigInteger),
    Column("game_id", BigInteger),
    Column("platform_id", BigInteger),
    Column("region", String(255)),
    Column("status", String(50)),
    Column("bracket_type", String(50)),  # single_elimination, double_elimination, league
    Column("games_per_match", Integer, default=3),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_event_details_organizer_id", "organizer_id"),
    Index("ix_event_details_tier_id", "tier_id"),
)

# Teams table
teams = Table(
    "teams",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("name", String(255), nullable=False),
    Column("tag", String(50)),
    Column("logo", String(255)),
    Column("banner", String(255)),
    Column("description", Text),
    Column("captain_id", BigInteger, ForeignKey("users.id")),
    Column("game_id", BigInteger),
    Column("region", String(255)),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_teams_captain_id", "captain_id"),
)

# Team members table
team_members = Table(
    "team_members",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("team_id", BigInteger, ForeignKey("teams.id", ondelete="CASCADE"), nullable=False),
    Column("user_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE"), nullable=False),
    Column("role", String(50)),  # captain, member
    Column("status", String(50)),  # active, invited, pending
    Column("joined_at", DateTime),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_team_members_team_id", "team_id"),
    Index("ix_team_members_user_id", "user_id"),
)

# Brackets table
brackets = Table(
    "brackets",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("order", Integer, nullable=False),
    Column("team1_id", BigInteger, ForeignKey("teams.id", ondelete="CASCADE")),
    Column("team2_id", BigInteger, ForeignKey("teams.id", ondelete="CASCADE")),
    Column("event_details_id", BigInteger, ForeignKey("event_details.id", ondelete="CASCADE"), nullable=False),
    Column("team1_position", String(255)),
    Column("team2_position", String(255)),
    Column("stage_name", String(255)),
    Column("inner_stage_name", String(255)),
    Column("winner_team_id", BigInteger, ForeignKey("teams.id")),
    Column("team1_score", Integer),
    Column("team2_score", Integer),
    Column("status", String(50)),  # pending, in_progress, completed
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_brackets_event_details_id", "event_details_id"),
    Index("ix_brackets_team1_id", "team1_id"),
    Index("ix_brackets_team2_id", "team2_id"),
)

# Bracket deadlines table (with JSON field for deadline data)
bracket_deadlines = Table(
    "bracket_deadlines",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("event_details_id", BigInteger, ForeignKey("event_details.id", ondelete="CASCADE")),
    Column("bracket_id", BigInteger, ForeignKey("brackets.id", ondelete="CASCADE")),
    Column("deadlines", JSON),  # JSON field: {started_timestamp, ended_timestamp, organizer_timestamp}
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_bracket_deadlines_event_details_id", "event_details_id"),
    Index("ix_bracket_deadlines_bracket_id", "bracket_id"),
)

# Event tier table
event_tier = Table(
    "event_tier",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("name", String(255), nullable=False),
    Column("description", Text),
    Column("min_prize", Numeric(10, 2)),
    Column("max_prize", Numeric(10, 2)),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
)

# Join events table (participant-event relationship)
join_events = Table(
    "join_events",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("event_details_id", BigInteger, ForeignKey("event_details.id", ondelete="CASCADE")),
    Column("team_id", BigInteger, ForeignKey("teams.id", ondelete="CASCADE")),
    Column("user_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE")),
    Column("status", String(50)),  # registered, checked_in, disqualified
    Column("placement", Integer),
    Column("prize_amount", Numeric(10, 2)),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_join_events_event_details_id", "event_details_id"),
    Index("ix_join_events_team_id", "team_id"),
    Index("ix_join_events_user_id", "user_id"),
)

# Event likes table
event_like = Table(
    "event_like",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("event_details_id", BigInteger, ForeignKey("event_details.id", ondelete="CASCADE")),
    Column("user_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE")),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_event_like_event_details_id", "event_details_id"),
    Index("ix_event_like_user_id", "user_id"),
)

# Organizer follower table
organizer_follower = Table(
    "organizerFollower",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("organizer_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE")),
    Column("follower_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE")),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_organizer_follower_organizer_id", "organizer_id"),
    Index("ix_organizer_follower_follower_id", "follower_id"),
)

# Notifications table
notifications = Table(
    "notifications",
    metadata,
    Column("id", BigInteger, primary_key=True, autoincrement=True),
    Column("user_id", BigInteger, ForeignKey("users.id", ondelete="CASCADE")),
    Column("type", String(255)),
    Column("title", String(255)),
    Column("message", Text),
    Column("read", Boolean, default=False),
    Column("data", JSON),
    Column("created_at", DateTime),
    Column("updated_at", DateTime),
    Index("ix_notifications_user_id", "user_id"),
)
