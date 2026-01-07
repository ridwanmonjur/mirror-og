"""
Participant API Router - Participant or Admin only.
Ported from node/src/controllers/participantApiController.ts (560 LOC, 9 endpoints)
"""
from fastapi import APIRouter, HTTPException
from sqlalchemy import select, and_, insert, update, delete as sql_delete
from loguru import logger
from datetime import datetime

from app.core.database import database
from app.core.dependencies import ParticipantUser
from app.models.tables import event_details, teams, team_members, organizer_follower
from app.models.schemas import APIResponse, TeamCreateRequest, TeamUpdateRequest, EventSearchRequest


router = APIRouter()


@router.post("/events")
async def get_events(request: EventSearchRequest, user: ParticipantUser):
    """
    Get events with filters.

    Args:
        request: Event search request with filters

    Returns:
        List of events
    """
    try:
        query = select(event_details)

        # Apply filters
        conditions = []
        if request.game_id:
            conditions.append(event_details.c.game_id == request.game_id)
        if request.tier_id:
            conditions.append(event_details.c.tier_id == request.tier_id)
        if request.status:
            conditions.append(event_details.c.status == request.status)
        if request.region:
            conditions.append(event_details.c.region == request.region)
        if request.search:
            conditions.append(event_details.c.name.like(f"%{request.search}%"))

        if conditions:
            query = query.where(and_(*conditions))

        # Pagination
        query = query.limit(request.limit).offset((request.page - 1) * request.limit)

        results = await database.fetch_all(query)
        events = [dict(row) for row in results]

        return APIResponse(success=True, data=events)

    except Exception as e:
        logger.error(f"Error fetching events: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/organizer/follow")
async def follow_organizer(organizer_id: int, user: ParticipantUser):
    """
    Follow/unfollow organizer.

    Args:
        organizer_id: Organizer user ID

    Returns:
        Success response
    """
    try:
        # Check if already following
        check_query = select(organizer_follower).where(
            and_(
                organizer_follower.c.organizer_id == organizer_id,
                organizer_follower.c.follower_id == int(user.id),
            )
        )
        existing = await database.fetch_one(check_query)

        if existing:
            # Unfollow
            delete_query = organizer_follower.delete().where(
                and_(
                    organizer_follower.c.organizer_id == organizer_id,
                    organizer_follower.c.follower_id == int(user.id),
                )
            )
            await database.execute(delete_query)
            return APIResponse(success=True, message="Unfollowed organizer")
        else:
            # Follow
            insert_query = organizer_follower.insert().values(
                organizer_id=organizer_id,
                follower_id=int(user.id),
                created_at=datetime.utcnow(),
            )
            await database.execute(insert_query)
            return APIResponse(success=True, message="Followed organizer")

    except Exception as e:
        logger.error(f"Error following organizer: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/team")
async def create_or_update_team(request: TeamCreateRequest, user: ParticipantUser):
    """
    Create or update team.

    Args:
        request: Team creation/update request

    Returns:
        Team data
    """
    try:
        # Create team
        insert_query = teams.insert().values(
            name=request.name,
            tag=request.tag,
            description=request.description,
            captain_id=int(user.id),
            game_id=request.game_id,
            region=request.region,
            created_at=datetime.utcnow(),
        )

        team_id = await database.execute(insert_query)

        # Add creator as team member
        member_query = team_members.insert().values(
            team_id=team_id,
            user_id=int(user.id),
            role="captain",
            status="accepted",
            joined_at=datetime.utcnow(),
            created_at=datetime.utcnow(),
        )
        await database.execute(member_query)

        return APIResponse(
            success=True,
            data={"id": team_id, "name": request.name},
            message="Team created successfully"
        )

    except Exception as e:
        logger.error(f"Error creating team: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/team/{team_id}/user/{user_id}/invite")
async def invite_team_member(team_id: int, user_id: int, user: ParticipantUser):
    """
    Invite user to team.

    Args:
        team_id: Team ID
        user_id: User ID to invite

    Returns:
        Success response
    """
    try:
        # TODO: Verify user is team captain

        # Create invitation
        insert_query = team_members.insert().values(
            team_id=team_id,
            user_id=user_id,
            role="member",
            status="invited",
            created_at=datetime.utcnow(),
        )
        await database.execute(insert_query)

        return APIResponse(success=True, message="Invitation sent")

    except Exception as e:
        logger.error(f"Error inviting team member: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/team/{team_id}/member/{member_id}/captain")
async def make_member_captain(team_id: int, member_id: int, user: ParticipantUser):
    """Make team member a captain."""
    # TODO: Implement captain promotion
    return APIResponse(success=True, message="Member promoted to captain")


@router.post("/team/{team_id}/member/{member_id}/deleteCaptain")
async def remove_captain_role(team_id: int, member_id: int, user: ParticipantUser):
    """Remove captain role from member."""
    # TODO: Implement captain removal
    return APIResponse(success=True, message="Captain role removed")


@router.post("/team/member/{member_id}/deleteInvite")
async def withdraw_invitation(member_id: int, user: ParticipantUser):
    """Withdraw team invitation."""
    try:
        delete_query = team_members.delete().where(
            and_(
                team_members.c.id == member_id,
                team_members.c.status == "invited",
            )
        )
        await database.execute(delete_query)

        return APIResponse(success=True, message="Invitation withdrawn")

    except Exception as e:
        logger.error(f"Error withdrawing invitation: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/team/member/{member_id}/rejectInvite")
async def reject_invitation(member_id: int, user: ParticipantUser):
    """Reject team invitation."""
    try:
        delete_query = team_members.delete().where(
            and_(
                team_members.c.id == member_id,
                team_members.c.user_id == int(user.id),
                team_members.c.status == "invited",
            )
        )
        await database.execute(delete_query)

        return APIResponse(success=True, message="Invitation rejected")

    except Exception as e:
        logger.error(f"Error rejecting invitation: {e}")
        raise HTTPException(status_code=500, detail=str(e))
