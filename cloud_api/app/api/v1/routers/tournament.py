"""
Tournament API Router - Firebase/Firestore operations and deadline processing.
Ported from node/src/controllers/tournamentController.ts (671 LOC, 9 endpoints)
"""
from fastapi import APIRouter, HTTPException, Request
from typing import Dict, Any, List
from loguru import logger

from app.core.rate_limit import tournament_rate_limit, batch_rate_limit, deadline_rate_limit
from app.services.deadline_service import DeadlineService
from app.services.firestore_service import FirestoreService
from app.models.schemas import (
    APIResponse, DeadlineTaskRequest, RoomBlockRequest,
    BatchReportRequest, BatchDisputeRequest
)


router = APIRouter()


@router.post("/room/block")
@tournament_rate_limit
async def block_chat_room(request: RoomBlockRequest):
    """
    Block/unblock chat room in Firebase.

    Args:
        request: Room block request

    Returns:
        Success response
    """
    try:
        firestore_service = FirestoreService()
        await firestore_service.block_room(
            str(request.event_id),
            request.room_id,
            request.blocked
        )

        action = "blocked" if request.blocked else "unblocked"
        return APIResponse(success=True, message=f"Room {action} successfully")

    except Exception as e:
        logger.error(f"Error blocking room: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/batch/reports")
@batch_rate_limit
async def create_batch_reports(request: BatchReportRequest):
    """
    Create batch bracket reports in Firestore.

    Args:
        request: Batch report request

    Returns:
        Success response
    """
    try:
        firestore_service = FirestoreService()

        for report in request.reports:
            await firestore_service.update_bracket_report(
                str(request.event_id),
                report["team1_position"],
                report["team2_position"],
                report["data"]
            )

        return APIResponse(
            success=True,
            message=f"Created {len(request.reports)} bracket reports"
        )

    except Exception as e:
        logger.error(f"Error creating batch reports: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/batch/disputes")
@batch_rate_limit
async def create_batch_disputes(request: BatchDisputeRequest):
    """
    Create batch disputes in Firestore.

    Args:
        request: Batch dispute request

    Returns:
        Success response
    """
    try:
        firestore_service = FirestoreService()

        created_disputes = []
        for dispute in request.disputes:
            dispute_id = await firestore_service.submit_dispute(
                str(request.event_id),
                dispute["team1_position"],
                dispute["team2_position"],
                dispute["match_number"],
                dispute["data"]
            )
            created_disputes.append(dispute_id)

        return APIResponse(
            success=True,
            data={"dispute_ids": created_disputes},
            message=f"Created {len(created_disputes)} disputes"
        )

    except Exception as e:
        logger.error(f"Error creating batch disputes: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/deadline/started")
@deadline_rate_limit
async def handle_started_deadline_tasks(request: DeadlineTaskRequest):
    """
    Handle tournament start deadline tasks.

    This processes matches when participant deadline has passed.

    Args:
        request: Deadline task request

    Returns:
        Processed deadline results
    """
    try:
        deadline_service = DeadlineService()

        # Fetch all event data (brackets/disputes) in bulk
        await deadline_service.fetch_all_event_data(request.event_id)

        results = []
        for bracket_data in request.brackets:
            match_status = bracket_data.get("match_status_data", {})

            # Interpret deadlines (after_organizer_deadline=False for started)
            result = deadline_service.interpret_deadlines(
                match_status_data=match_status,
                update_values={},
                bracket=bracket_data.get("bracket", {}),
                extra_bracket=bracket_data.get("extra_bracket"),
                tier_id=request.tier_id,
                after_organizer_deadline=False,
                is_league=request.is_league,
                games_per_match=bracket_data.get("games_per_match", 3)
            )

            results.append(result)

        return APIResponse(
            success=True,
            data=results,
            message=f"Processed {len(results)} deadline tasks"
        )

    except Exception as e:
        logger.error(f"Error handling started deadline tasks: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/deadline/ended")
@deadline_rate_limit
async def handle_ended_deadline_tasks(request: DeadlineTaskRequest):
    """
    Handle tournament end deadline tasks.

    This processes matches when organizer deadline has passed.
    Will break ties and conflicts randomly if needed.

    Args:
        request: Deadline task request

    Returns:
        Processed deadline results
    """
    try:
        deadline_service = DeadlineService()

        # Fetch all event data in bulk
        await deadline_service.fetch_all_event_data(request.event_id)

        results = []
        for bracket_data in request.brackets:
            match_status = bracket_data.get("match_status_data", {})

            # Interpret deadlines (after_organizer_deadline=True for ended)
            result = deadline_service.interpret_deadlines(
                match_status_data=match_status,
                update_values={},
                bracket=bracket_data.get("bracket", {}),
                extra_bracket=bracket_data.get("extra_bracket"),
                tier_id=request.tier_id,
                after_organizer_deadline=True,  # Will break ties/conflicts
                is_league=request.is_league,
                games_per_match=bracket_data.get("games_per_match", 3)
            )

            results.append(result)

        return APIResponse(
            success=True,
            data=results,
            message=f"Processed {len(results)} deadline tasks with conflict resolution"
        )

    except Exception as e:
        logger.error(f"Error handling ended deadline tasks: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/deadline/org")
@deadline_rate_limit
async def handle_organizer_deadline_tasks(request: DeadlineTaskRequest):
    """
    Handle organizer deadline tasks.

    Similar to ended tasks but specifically for organizer intervention.

    Args:
        request: Deadline task request

    Returns:
        Processed deadline results
    """
    try:
        deadline_service = DeadlineService()

        # Fetch all event data in bulk
        await deadline_service.fetch_all_event_data(request.event_id)

        results = []
        for bracket_data in request.brackets:
            match_status = bracket_data.get("match_status_data", {})

            result = deadline_service.interpret_deadlines(
                match_status_data=match_status,
                update_values={},
                bracket=bracket_data.get("bracket", {}),
                extra_bracket=bracket_data.get("extra_bracket"),
                tier_id=request.tier_id,
                after_organizer_deadline=True,
                is_league=request.is_league,
                games_per_match=bracket_data.get("games_per_match", 3)
            )

            results.append(result)

        return APIResponse(
            success=True,
            data=results,
            message=f"Processed {len(results)} organizer deadline tasks"
        )

    except Exception as e:
        logger.error(f"Error handling organizer deadline tasks: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/match/result")
async def get_match_result(event_id: int, match_id: str):
    """
    Get single match result from Firestore.

    Args:
        event_id: Event ID
        match_id: Match ID (bracket document ID)

    Returns:
        Match result data
    """
    try:
        firestore_service = FirestoreService()
        # TODO: Implement get bracket report method
        # result = await firestore_service.get_bracket_report(str(event_id), match_id)

        return APIResponse(
            success=True,
            data={},
            message="Match result retrieval - TBD"
        )

    except Exception as e:
        logger.error(f"Error getting match result: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/match/results/all")
async def get_all_match_results(event_id: int):
    """
    Get all match results for an event from Firestore.

    Args:
        event_id: Event ID

    Returns:
        All match results
    """
    try:
        # TODO: Implement fetching all brackets for event
        return APIResponse(
            success=True,
            data=[],
            message="All match results retrieval - TBD"
        )

    except Exception as e:
        logger.error(f"Error getting all match results: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/health")
async def firebase_health_check():
    """
    Health check for Firebase connectivity.

    Returns:
        Firebase connection status
    """
    try:
        firestore_service = FirestoreService()
        # Simple check - try to access Firestore
        if firestore_service.db:
            return APIResponse(
                success=True,
                message="Firebase connected"
            )
        else:
            return APIResponse(
                success=False,
                message="Firebase not connected"
            )

    except Exception as e:
        logger.error(f"Firebase health check failed: {e}")
        return APIResponse(success=False, message=f"Firebase error: {str(e)}")
