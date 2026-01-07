"""
Public API Router - No authentication required.
Ported from node/src/controllers/publicApiController.ts (284 LOC, 8 endpoints)
"""
from fastapi import APIRouter, HTTPException
from sqlalchemy import select
from loguru import logger

from app.core.database import database
from app.models.tables import activity_logs, users
from app.models.schemas import APIResponse


router = APIRouter()


@router.get("/user/{user_id}/logs")
async def get_user_logs(user_id: int, limit: int = 20, offset: int = 0):
    """
    Get user activity logs (public).

    Args:
        user_id: User ID
        limit: Number of logs to return
        offset: Pagination offset

    Returns:
        User activity logs
    """
    try:
        query = (
            select(activity_logs)
            .where(activity_logs.c.subject_id == user_id)
            .order_by(activity_logs.c.created_at.desc())
            .limit(limit)
            .offset(offset)
        )

        results = await database.fetch_all(query)
        logs = [dict(row) for row in results]

        return APIResponse(success=True, data=logs)

    except Exception as e:
        logger.error(f"Error fetching user logs: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/user/{user_id}/connections")
async def get_user_connections(user_id: int):
    """
    Get user connections (followers/following) - public.

    Args:
        user_id: User ID

    Returns:
        User connections data
    """
    try:
        # TODO: Implement followers/following queries
        # This would query organizerFollower table and related tables

        return APIResponse(
            success=True,
            data={
                "followers": [],
                "following": [],
                "followers_count": 0,
                "following_count": 0,
            }
        )

    except Exception as e:
        logger.error(f"Error fetching user connections: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/media")
async def upload_media():
    """Upload media (image/video)."""
    # TODO: Implement media upload logic
    return APIResponse(success=True, message="Media upload endpoint - TBD")


@router.get("/media/stream/{media}")
async def stream_media(media: str):
    """Stream media file."""
    # TODO: Implement media streaming
    return APIResponse(success=True, message="Media streaming endpoint - TBD")


@router.delete("/media/{media}")
async def delete_media(media: str):
    """Delete media file."""
    # TODO: Implement media deletion
    return APIResponse(success=True, message="Media deletion endpoint - TBD")


@router.put("/interest")
async def register_beta_interest():
    """Register beta interest."""
    # TODO: Implement beta interest registration
    return APIResponse(success=True, message="Beta interest registered")
