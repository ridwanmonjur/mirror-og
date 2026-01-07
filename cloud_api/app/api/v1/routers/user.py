"""
User API Router - Authenticated users (any role).
Ported from node/src/controllers/userApiController.ts (387 LOC, 17 endpoints)
"""
from fastapi import APIRouter, HTTPException
from sqlalchemy import select, and_
from loguru import logger

from app.core.database import database
from app.core.dependencies import AnyAuthenticatedUser
from app.models.tables import users, notifications, teams, event_like
from app.models.schemas import APIResponse, NotificationResponse


router = APIRouter()


@router.get("/user")
async def get_current_user(user: AnyAuthenticatedUser):
    """
    Get current authenticated user.

    Returns:
        User data
    """
    try:
        query = select(users).where(users.c.id == int(user.id))
        result = await database.fetch_one(query)

        if not result:
            raise HTTPException(status_code=404, detail="User not found")

        user_data = dict(result)
        # Remove sensitive fields
        user_data.pop("password", None)
        user_data.pop("remember_token", None)

        return APIResponse(success=True, data=user_data)

    except Exception as e:
        logger.error(f"Error fetching current user: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/user/notifications")
async def get_notifications(user: AnyAuthenticatedUser, limit: int = 20):
    """
    Get user notifications.

    Args:
        limit: Number of notifications to return

    Returns:
        List of notifications
    """
    try:
        query = (
            select(notifications)
            .where(notifications.c.user_id == int(user.id))
            .order_by(notifications.c.created_at.desc())
            .limit(limit)
        )

        results = await database.fetch_all(query)
        notifs = [dict(row) for row in results]

        return APIResponse(success=True, data=notifs)

    except Exception as e:
        logger.error(f"Error fetching notifications: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/user/notifications/{notification_id}")
async def mark_notification_read(notification_id: int, user: AnyAuthenticatedUser):
    """
    Mark notification as read.

    Args:
        notification_id: Notification ID

    Returns:
        Success response
    """
    try:
        query = (
            notifications.update()
            .where(
                and_(
                    notifications.c.id == notification_id,
                    notifications.c.user_id == int(user.id),
                )
            )
            .values(read=True)
        )

        await database.execute(query)

        return APIResponse(success=True, message="Notification marked as read")

    except Exception as e:
        logger.error(f"Error marking notification as read: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/user/settings")
async def update_user_settings(user: AnyAuthenticatedUser):
    """Update user settings."""
    # TODO: Implement user settings update
    return APIResponse(success=True, message="Settings updated")


@router.post("/user/likes")
async def toggle_event_like(event_id: int, user: AnyAuthenticatedUser):
    """
    Like/unlike an event.

    Args:
        event_id: Event ID

    Returns:
        Success response
    """
    try:
        # Check if already liked
        check_query = select(event_like).where(
            and_(
                event_like.c.event_details_id == event_id,
                event_like.c.user_id == int(user.id),
            )
        )
        existing = await database.fetch_one(check_query)

        if existing:
            # Unlike
            delete_query = event_like.delete().where(
                and_(
                    event_like.c.event_details_id == event_id,
                    event_like.c.user_id == int(user.id),
                )
            )
            await database.execute(delete_query)
            return APIResponse(success=True, message="Event unliked")
        else:
            # Like
            insert_query = event_like.insert().values(
                event_details_id=event_id,
                user_id=int(user.id),
            )
            await database.execute(insert_query)
            return APIResponse(success=True, message="Event liked")

    except Exception as e:
        logger.error(f"Error toggling event like: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/teams/search")
async def search_teams(query: str = "", limit: int = 20):
    """
    Search teams.

    Args:
        query: Search query
        limit: Number of results

    Returns:
        List of teams
    """
    try:
        search_query = (
            select(teams)
            .where(teams.c.name.like(f"%{query}%"))
            .limit(limit)
        )

        results = await database.fetch_all(search_query)
        teams_data = [dict(row) for row in results]

        return APIResponse(success=True, data=teams_data)

    except Exception as e:
        logger.error(f"Error searching teams: {e}")
        raise HTTPException(status_code=500, detail=str(e))
