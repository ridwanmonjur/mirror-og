"""
FirestoreService - Handles all Firestore write operations for brackets and disputes.
Ported from node/src/services/firestoreService.ts (248 LOC)
"""
from typing import Dict, Any, List, Optional
from loguru import logger
from google.cloud import firestore
from app.core.firebase import get_firestore_client


class FirestoreService:
    """
    Firestore Service for bracket reports and disputes.
    Replicates operations from BracketData.js
    """

    def __init__(self):
        """Initialize FirestoreService with Firestore client."""
        self.db = get_firestore_client()

    async def update_bracket_report(
        self,
        event_id: str,
        team1_position: str,
        team2_position: str,
        data: Dict[str, Any]
    ) -> None:
        """
        Update bracket report in Firestore.

        Replicates writeReportDB() from BracketData.js
        Document ID format: {team1Position}.{team2Position} (e.g., "W1.W2")

        Args:
            event_id: Event ID
            team1_position: Team 1 position
            team2_position: Team 2 position
            data: Bracket report data
        """
        try:
            doc_id = f"{team1_position}.{team2_position}"
            doc_ref = self.db.collection('event').document(event_id).collection('brackets').document(doc_id)

            # Prepare document data (matches structure from BracketData.js line 364-380)
            firestore_doc = {
                "score": data.get("score", [0, 0]),
                "stageName": data.get("stageName"),
                "realWinners": data.get("realWinners"),
                "organizerWinners": data.get("organizerWinners"),
                "team1Id": data.get("team1Id"),
                "team2Id": data.get("team2Id"),
                "position": data.get("position"),
                "completeMatchStatus": data.get("completeMatchStatus"),
                "randomWinners": data.get("randomWinners"),
                "defaultWinners": data.get("defaultWinners"),
                "disqualified": data.get("disqualified"),
                "disputeResolved": data.get("disputeResolved"),
                "team1Winners": data.get("team1Winners"),
                "team2Winners": data.get("team2Winners"),
                "matchStatus": data.get("matchStatus"),
            }

            # Use set to create or update
            doc_ref.set(firestore_doc)

            logger.info(
                f"Bracket report updated successfully: "
                f"event={event_id}, doc={doc_id}, position={data.get('position')}"
            )

        except Exception as e:
            logger.error(f"Error updating bracket report in Firestore: {e}")
            raise

    async def submit_dispute(
        self,
        event_id: str,
        team1_position: str,
        team2_position: str,
        match_number: int,
        data: Dict[str, Any]
    ) -> str:
        """
        Submit dispute to Firestore.

        Replicates submitDisputeForm() from BracketData.js (line 574-639)
        Document ID format: {team1Position}.{team2Position}.{matchNumber} (e.g., "W1.W2.0")

        Args:
            event_id: Event ID
            team1_position: Team 1 position
            team2_position: Team 2 position
            match_number: Match number (0-indexed)
            data: Dispute data

        Returns:
            Dispute ID
        """
        try:
            dispute_id = f"{team1_position}.{team2_position}.{match_number}"
            doc_ref = self.db.collection('event').document(event_id).collection('disputes').document(dispute_id)

            # Add timestamps
            dispute_doc = {
                **data,
                "created_at": firestore.SERVER_TIMESTAMP,
                "updated_at": firestore.SERVER_TIMESTAMP,
            }

            doc_ref.set(dispute_doc)

            logger.info(
                f"Dispute submitted successfully: "
                f"event={event_id}, dispute={dispute_id}, match={match_number}"
            )

            return dispute_id

        except Exception as e:
            logger.error(f"Error submitting dispute to Firestore: {e}")
            raise

    async def respond_to_dispute(
        self,
        event_id: str,
        dispute_id: str,
        response_data: Dict[str, Any]
    ) -> None:
        """
        Respond to dispute in Firestore.

        Replicates respondDisputeForm() from BracketData.js (line 642-705)

        Args:
            event_id: Event ID
            dispute_id: Dispute ID
            response_data: Response data (response_teamId, response_teamNumber, etc.)
        """
        try:
            doc_ref = self.db.collection('event').document(event_id).collection('disputes').document(dispute_id)

            update_data = {
                **response_data,
                "updated_at": firestore.SERVER_TIMESTAMP,
                "status": "responded",
            }

            doc_ref.update(update_data)

            logger.info(f"Dispute response updated successfully: event={event_id}, dispute={dispute_id}")

        except Exception as e:
            logger.error(f"Error responding to dispute in Firestore: {e}")
            raise

    async def resolve_dispute(
        self,
        event_id: str,
        dispute_id: str,
        resolution_data: Dict[str, Any],
        bracket_update: Optional[Dict[str, Any]] = None
    ) -> None:
        """
        Resolve dispute in Firestore.

        Replicates resolveDisputeForm() from BracketData.js (line 266-317)
        Also updates the associated bracket report if provided.

        Args:
            event_id: Event ID
            dispute_id: Dispute ID
            resolution_data: Resolution data (resolution_winner, resolution_resolved_by)
            bracket_update: Optional bracket report update
        """
        try:
            dispute_ref = self.db.collection('event').document(event_id).collection('disputes').document(dispute_id)

            # Update dispute
            dispute_update_data = {
                "resolution_winner": resolution_data["resolution_winner"],
                "resolution_resolved_by": resolution_data["resolution_resolved_by"],
                "updated_at": firestore.SERVER_TIMESTAMP,
            }

            dispute_ref.update(dispute_update_data)

            # Also update bracket report if provided
            if bracket_update:
                await self.update_bracket_report(
                    event_id,
                    bracket_update["team1Position"],
                    bracket_update["team2Position"],
                    bracket_update["reportData"]
                )

            logger.info(
                f"Dispute resolved successfully: "
                f"event={event_id}, dispute={dispute_id}, "
                f"winner={resolution_data['resolution_winner']}"
            )

        except Exception as e:
            logger.error(f"Error resolving dispute in Firestore: {e}")
            raise

    async def get_dispute(self, event_id: str, dispute_id: str) -> Optional[Dict[str, Any]]:
        """
        Get dispute document from Firestore.

        Args:
            event_id: Event ID
            dispute_id: Dispute ID

        Returns:
            Dispute data or None if not found
        """
        try:
            doc_ref = self.db.collection('event').document(event_id).collection('disputes').document(dispute_id)
            doc = doc_ref.get()

            if not doc.exists:
                return None

            return doc.to_dict()

        except Exception as e:
            logger.error(f"Error getting dispute from Firestore: {e}")
            raise

    async def update_dispute_status(
        self,
        event_id: str,
        team1_position: str,
        team2_position: str,
        match_number: int,
        is_resolved: bool,
        report_data: Dict[str, Any]
    ) -> None:
        """
        Update dispute status in bracket report.

        Helper method to update disputeResolved array in bracket report.

        Args:
            event_id: Event ID
            team1_position: Team 1 position
            team2_position: Team 2 position
            match_number: Match number (0-indexed)
            is_resolved: Whether dispute is resolved
            report_data: Bracket report data
        """
        try:
            # Update the disputeResolved array
            if "disputeResolved" not in report_data:
                report_data["disputeResolved"] = [None, None, None]

            report_data["disputeResolved"][match_number] = is_resolved

            await self.update_bracket_report(event_id, team1_position, team2_position, report_data)

            logger.info(
                f"Dispute status updated in bracket report: "
                f"event={event_id}, match={match_number}, resolved={is_resolved}"
            )

        except Exception as e:
            logger.error(f"Error updating dispute status: {e}")
            raise

    async def block_room(self, event_id: str, room_id: str, blocked: bool) -> None:
        """
        Block or unblock a chat room in Firestore.

        Args:
            event_id: Event ID
            room_id: Room ID
            blocked: Whether to block the room
        """
        try:
            doc_ref = self.db.collection('room').document(room_id)

            doc_ref.update({
                "blocked": blocked,
                "updated_at": firestore.SERVER_TIMESTAMP,
            })

            logger.info(f"Room {'blocked' if blocked else 'unblocked'}: event={event_id}, room={room_id}")

        except Exception as e:
            logger.error(f"Error blocking/unblocking room: {e}")
            raise
