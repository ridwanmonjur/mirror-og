"""
ValidationService - Bracket update validation logic.
Ported from node/src/services/validationService.ts (238 LOC)
"""
from typing import Dict, Any, Optional
from datetime import datetime
from sqlalchemy import select, and_
from loguru import logger
from app.core.database import database
from app.models.tables import brackets, event_details, team_members, bracket_deadlines


class ValidationContext:
    """Validation context for bracket updates."""
    def __init__(
        self,
        user_id: int,
        user_role: str,
        event_id: int,
        team1_id: int,
        team1_position: str,
        team2_id: int,
        team2_position: str,
        my_team_id: Optional[int] = None,
        will_check_deadline: bool = True,
    ):
        self.user_id = user_id
        self.user_role = user_role
        self.event_id = event_id
        self.team1_id = team1_id
        self.team1_position = team1_position
        self.team2_id = team2_id
        self.team2_position = team2_position
        self.my_team_id = my_team_id
        self.will_check_deadline = will_check_deadline


class ValidationResult:
    """Validation result."""
    def __init__(self, valid: bool, error: Optional[str] = None, match: Optional[Dict[str, Any]] = None):
        self.valid = valid
        self.error = error
        self.match = match


class ValidationService:
    """
    Validation Service for bracket updates.
    Replicates ValidateBracketUpdateRequest.php logic.
    """

    async def validate_match_exists(self, context: ValidationContext) -> Optional[Dict[str, Any]]:
        """
        Validate match exists in brackets table.

        Args:
            context: Validation context

        Returns:
            Match data or None if not found
        """
        try:
            query = select(brackets).where(
                and_(
                    brackets.c.team1_id == context.team1_id,
                    brackets.c.team1_position == context.team1_position,
                    brackets.c.team2_id == context.team2_id,
                    brackets.c.team2_position == context.team2_position,
                    brackets.c.event_details_id == context.event_id,
                )
            )

            result = await database.fetch_one(query)
            return dict(result) if result else None

        except Exception as e:
            logger.error(f"Error validating match existence: {e}")
            raise

    async def validate_organizer_permission(self, user_id: int, event_id: int) -> bool:
        """
        Validate organizer owns the event.

        Args:
            user_id: User ID
            event_id: Event ID

        Returns:
            True if organizer owns the event
        """
        try:
            query = select(event_details.c.id).where(
                and_(
                    event_details.c.id == event_id,
                    event_details.c.organizer_id == user_id,
                )
            )

            result = await database.fetch_one(query)
            return result is not None

        except Exception as e:
            logger.error(f"Error validating organizer permission: {e}")
            raise

    async def validate_deadline(
        self,
        event_id: int,
        stage_name: str,
        inner_stage_name: str
    ) -> bool:
        """
        Validate deadline for participant reporting.

        Checks if current date is within start_date and end_date for the stage.

        Args:
            event_id: Event ID
            stage_name: Stage name
            inner_stage_name: Inner stage name

        Returns:
            True if within deadline
        """
        try:
            query = select(bracket_deadlines.c.deadlines).where(
                bracket_deadlines.c.event_details_id == event_id
            )

            result = await database.fetch_one(query)

            if not result:
                logger.warning(f"No bracket deadlines found for event {event_id}")
                return False

            # Parse JSON deadlines
            deadlines_data = result["deadlines"]

            if not deadlines_data:
                logger.warning(f"Empty deadlines for event {event_id}")
                return False

            # Check if deadline exists for this stage
            stage_deadlines = deadlines_data.get(stage_name)

            if not stage_deadlines:
                logger.warning(f"No deadlines for stage {stage_name} in event {event_id}")
                return False

            deadline = stage_deadlines.get(inner_stage_name)

            if not deadline or not deadline.get("start_date") or not deadline.get("end_date"):
                logger.warning(
                    f"No deadline for inner stage {inner_stage_name} "
                    f"in stage {stage_name} for event {event_id}"
                )
                return False

            # Check if current date is within deadline
            now = datetime.now()
            start_date = datetime.fromisoformat(deadline["start_date"].replace('Z', '+00:00'))
            end_date = datetime.fromisoformat(deadline["end_date"].replace('Z', '+00:00'))

            # Set time to start/end of day for date comparison
            now = now.replace(hour=0, minute=0, second=0, microsecond=0)
            start_date = start_date.replace(hour=0, minute=0, second=0, microsecond=0)
            end_date = end_date.replace(hour=23, minute=59, second=59, microsecond=999999)

            is_within_deadline = start_date <= now <= end_date

            if not is_within_deadline:
                logger.debug(
                    f"Deadline check failed: now={now}, "
                    f"start={start_date}, end={end_date}, "
                    f"stage={stage_name}, inner_stage={inner_stage_name}"
                )

            return is_within_deadline

        except Exception as e:
            logger.error(f"Error validating deadline: {e}")
            raise

    async def validate_team_membership(self, user_id: int, team_id: int) -> bool:
        """
        Validate user is a member of the team with accepted status.

        Args:
            user_id: User ID
            team_id: Team ID

        Returns:
            True if user is an accepted team member
        """
        try:
            query = select(team_members.c.id).where(
                and_(
                    team_members.c.user_id == user_id,
                    team_members.c.team_id == team_id,
                    team_members.c.status == "accepted",
                )
            )

            result = await database.fetch_one(query)
            return result is not None

        except Exception as e:
            logger.error(f"Error validating team membership: {e}")
            raise

    async def validate_bracket_update(self, context: ValidationContext) -> ValidationResult:
        """
        Main validation function.
        Replicates the authorize() method from ValidateBracketUpdateRequest.php

        Args:
            context: Validation context

        Returns:
            ValidationResult with valid flag, error message, and match data
        """
        try:
            # 1. Validate match exists
            match = await self.validate_match_exists(context)

            if not match:
                return ValidationResult(
                    valid=False,
                    error="The match is not found in tournament bracket! Are you editing in the right place?"
                )

            # 2. Role-specific validation
            if context.user_role == "ORGANIZER":
                # Validate organizer owns the event
                owns_event = await self.validate_organizer_permission(context.user_id, context.event_id)

                if not owns_event:
                    return ValidationResult(
                        valid=False,
                        error="This is not your event!"
                    )

            elif context.user_role == "PARTICIPANT":
                # 3. Validate deadline (if required)
                if context.will_check_deadline:
                    is_within_deadline = await self.validate_deadline(
                        context.event_id,
                        match["stage_name"],
                        match["inner_stage_name"]
                    )

                    if not is_within_deadline:
                        return ValidationResult(
                            valid=False,
                            error="Match is not within reporting timeframe!"
                        )

                # 4. Validate team membership
                if not context.my_team_id:
                    return ValidationResult(
                        valid=False,
                        error="No valid team ID provided"
                    )

                is_member = await self.validate_team_membership(
                    context.user_id,
                    context.my_team_id
                )

                if not is_member:
                    return ValidationResult(
                        valid=False,
                        error="You are not a member of this team"
                    )

            elif context.user_role != "ADMIN":
                return ValidationResult(
                    valid=False,
                    error="No valid user role"
                )

            # All validations passed
            return ValidationResult(valid=True, match=match)

        except Exception as e:
            logger.error(f"Error in bracket validation: {e}")
            raise
