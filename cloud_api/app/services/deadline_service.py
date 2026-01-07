"""
DeadlineService - Tournament deadline and dispute resolution logic.
LINE-BY-LINE port from node/src/services/deadlineService.ts (365 LOC)

CRITICAL: This service handles complex tournament deadline processing,
dispute resolution, and winner determination. All business logic must
be preserved exactly to maintain competitive integrity.
"""
from typing import Dict, List, Any, Optional, Tuple
from loguru import logger
from app.core.firebase import get_firestore_client
import random


# Dispute resolution enums (matches Laravel configuration)
DISPUTE_ENUMS = {
    "ORGANIZER": 3,
    "DISPUTEE": 4,
    "RESPONDER": 5,
    "TIME": 6,
    "RANDOM": 7,
}


class DeadlineService:
    """
    Handles tournament deadline processing and dispute resolution.
    Ported from TypeScript cloud_server_functions/main.py
    """

    def __init__(self):
        """Initialize DeadlineService with Firestore client and caches."""
        self.db = get_firestore_client()
        self.all_brackets: Dict[str, Any] = {}
        self.all_disputes: Dict[str, Any] = {}
        self.dispute_enums = DISPUTE_ENUMS

    async def fetch_all_event_data(self, event_details_id: int | str) -> Dict[str, int]:
        """
        Fetch all brackets and disputes for an event in bulk (hashmap optimization).

        Args:
            event_details_id: Event ID

        Returns:
            Dict with brackets_count and disputes_count
        """
        try:
            event_id = str(event_details_id)

            # Fetch all brackets for this event
            brackets_collection = (
                self.db.collection('event')
                .document(event_id)
                .collection('brackets')
            )
            bracket_docs = brackets_collection.get()

            self.all_brackets.clear()
            for doc in bracket_docs:
                self.all_brackets[doc.id] = doc.to_dict()

            # Fetch all disputes for this event
            disputes_collection = (
                self.db.collection('event')
                .document(event_id)
                .collection('disputes')
            )
            dispute_docs = disputes_collection.get()

            self.all_disputes.clear()
            for doc in dispute_docs:
                self.all_disputes[doc.id] = doc.to_dict()

            logger.info(
                f"Fetched all event data for {event_id}: "
                f"{len(self.all_brackets)} brackets, {len(self.all_disputes)} disputes"
            )

            return {
                "brackets_count": len(self.all_brackets),
                "disputes_count": len(self.all_disputes),
            }

        except Exception as e:
            logger.error(f"Failed to fetch event data for {event_details_id}: {e}")
            raise

    def calc_scores(self, real_winners: List[Optional[str]]) -> List[int]:
        """
        Calculate scores from real winners array.

        Args:
            real_winners: List of winner indicators ("1", "2", or None)

        Returns:
            [team1_score, team2_score]
        """
        score1 = 0
        score2 = 0

        for value in real_winners:
            if value is None:
                continue
            if value == "1":
                score1 += 1
            else:
                score2 += 1

        return [score1, score2]

    def handle_disputes(
        self,
        match_status_data: Dict[str, Any],
        bracket: Dict[str, Any],
        event_id: str,
        will_break_conflicts: bool = False,
        games_per_match: int = 3
    ) -> Dict[str, Any]:
        """
        Handle match dispute resolution.

        Args:
            match_status_data: Match status with winners/disputes
            bracket: Bracket data
            event_id: Event ID
            will_break_conflicts: Whether to resolve conflicts randomly
            games_per_match: Number of games per match (default 3)

        Returns:
            Dict with updateReportValues, disputeRefList, updateDisputeValues, isUpdatedDispute
        """
        real_winners = match_status_data.get("realWinners") or [None] * games_per_match
        dispute_resolved = match_status_data.get("disputeResolved") or [None] * games_per_match

        is_updated_dispute = False
        update_report_values = {}
        update_dispute_values = [None] * games_per_match
        dispute_ref_list = [None] * games_per_match

        for i in range(games_per_match):
            if real_winners[i] is None:
                if not dispute_resolved[i]:
                    dispute_path = f"{bracket['team1_position']}.{bracket['team2_position']}.{i}"

                    # Use hashmap lookup instead of individual Firestore query
                    if dispute_path in self.all_disputes:
                        data = self.all_disputes[dispute_path]
                        dispute_ref = (
                            self.db.collection('event')
                            .document(event_id)
                            .collection('disputes')
                            .document(dispute_path)
                        )

                        # Case 1: One team filed dispute, other hasn't responded
                        if "dispute_teamNumber" in data and "response_teamId" not in data:
                            is_updated_dispute = True
                            winner_chosen = str(data["dispute_teamNumber"])
                            real_winners[i] = winner_chosen
                            dispute_resolved[i] = True
                            update_dispute_values[i] = {
                                "resolution_winner": winner_chosen,
                                "resolution_resolved_by": self.dispute_enums["TIME"],
                            }
                            dispute_ref_list[i] = dispute_ref

                        # Case 2: Both teams filed conflicting claims and we break conflicts
                        elif will_break_conflicts and "response_teamNumber" in data:
                            is_updated_dispute = True
                            chosen_winner = (
                                str(data["dispute_teamNumber"])
                                if random.random() < 0.5
                                else str(data["response_teamNumber"])
                            )
                            real_winners[i] = chosen_winner
                            dispute_resolved[i] = True
                            update_dispute_values[i] = {
                                "resolution_winner": chosen_winner,
                                "resolution_resolved_by": self.dispute_enums["RANDOM"],
                            }
                            dispute_ref_list[i] = dispute_ref

        scores = self.calc_scores(real_winners)

        if is_updated_dispute:
            update_report_values = {
                "realWinners": real_winners,
                "score": scores,
                "disputeResolved": dispute_resolved,
            }

        return {
            "updateReportValues": update_report_values,
            "disputeRefList": dispute_ref_list,
            "updateDisputeValues": update_dispute_values,
            "isUpdatedDispute": is_updated_dispute,
        }

    def handle_reports(
        self,
        match_status_data: Dict[str, Any],
        games_per_match: int = 3,
        will_break_ties_and_conflicts: bool = False
    ) -> Dict[str, Any]:
        """
        Resolve winners for matches with incomplete/conflicted/tied submissions.

        Args:
            match_status_data: Match status data
            games_per_match: Number of games per match
            will_break_ties_and_conflicts: Whether to break ties/conflicts randomly

        Returns:
            Dict with newUpdate and updated flag
        """
        team1_winners = match_status_data.get("team1Winners") or [None] * games_per_match
        team2_winners = match_status_data.get("team2Winners") or [None] * games_per_match
        real_winners = match_status_data.get("realWinners") or [None] * games_per_match
        default_winners = match_status_data.get("defaultWinners") or [None] * games_per_match
        random_winners = match_status_data.get("randomWinners") or [None] * games_per_match

        no_scores = 0
        updated = False
        new_update = {}
        disqualified = False

        for i in range(games_per_match):
            if real_winners[i] is None:
                # Complete but conflict
                if team2_winners[i] is not None and team1_winners[i] is not None:
                    if team2_winners[i] == team1_winners[i]:
                        updated = True
                        winner_chosen = str(team1_winners[i])
                        real_winners[i] = winner_chosen

                    if will_break_ties_and_conflicts:
                        dispute_resolved = match_status_data.get("disputeResolved") or [None] * games_per_match
                        if dispute_resolved[i] is None or dispute_resolved[i]:
                            updated = True
                            winner_chosen = str(random.randint(0, 1))
                            real_winners[i] = winner_chosen
                            random_winners[i] = True

                # Only team 2 submitted
                elif team2_winners[i] is not None and team1_winners[i] is None:
                    updated = True
                    default_winners[i] = True
                    winner_chosen = str(team2_winners[i])
                    real_winners[i] = winner_chosen

                # Only team 1 submitted
                elif team1_winners[i] is not None and team2_winners[i] is None:
                    updated = True
                    default_winners[i] = True
                    winner_chosen = str(team1_winners[i])
                    real_winners[i] = winner_chosen

                # Neither team submitted
                else:
                    no_scores += 1

        scores = self.calc_scores(real_winners)

        # Check for disqualification (no scores submitted for any game)
        if no_scores == games_per_match:
            updated = True
            disqualified = True
        elif will_break_ties_and_conflicts:
            # Break Tie
            if scores[0] == scores[1]:
                dispute_resolved = match_status_data.get("disputeResolved") or [None] * games_per_match
                for i in range(games_per_match):
                    if team2_winners[i] is None and team1_winners[i] is None:
                        if will_break_ties_and_conflicts:
                            if dispute_resolved[i] is None or dispute_resolved[i]:
                                updated = True
                                winner_chosen = str(random.randint(0, 1))
                                real_winners[i] = winner_chosen
                                random_winners[i] = True

        if updated:
            new_update = {
                "realWinners": real_winners,
                "score": scores,
                "defaultWinners": default_winners,
                "randomWinners": random_winners,
                "disqualified": disqualified,
            }

        return {"newUpdate": new_update, "updated": updated}

    def interpret_deadlines(
        self,
        match_status_data: Dict[str, Any],
        update_values: Dict[str, Any],
        bracket: Dict[str, Any],
        extra_bracket: Any,
        tier_id: int | str,
        after_organizer_deadline: bool = False,
        is_league: bool = False,
        games_per_match: int = 3
    ) -> Dict[str, Any]:
        """
        Main deadline interpretation logic.

        Args:
            match_status_data: Match status data
            update_values: Values to update
            bracket: Bracket data
            extra_bracket: Extra bracket data
            tier_id: Tournament tier ID
            after_organizer_deadline: Whether organizer deadline passed
            is_league: Whether this is a league format
            games_per_match: Number of games per match

        Returns:
            Dict with dispute_ref_list, update_dispute_values, update_values, next_stage_data
        """
        # Handle disputes
        dispute_result = self.handle_disputes(
            match_status_data,
            bracket,
            str(bracket["event_details_id"]),
            after_organizer_deadline,
            games_per_match
        )

        if dispute_result["isUpdatedDispute"]:
            update_values.update(dispute_result["updateReportValues"])
            match_status_data.update(dispute_result["updateReportValues"])

        # Handle reports
        report_result = self.handle_reports(
            match_status_data,
            games_per_match,
            after_organizer_deadline
        )

        if report_result["updated"]:
            update_values.update(report_result["newUpdate"])
            match_status_data.update(report_result["newUpdate"])

        # Return data for PHP to handle resolveNextStage
        next_stage_data = None
        if not is_league and match_status_data.get("score"):
            next_stage_data = {
                "bracket": bracket,
                "extra_bracket": extra_bracket,
                "score": match_status_data["score"],
                "tier_id": tier_id,
            }

        return {
            "dispute_ref_list": dispute_result["disputeRefList"],
            "update_dispute_values": dispute_result["updateDisputeValues"],
            "update_values": update_values,
            "next_stage_data": next_stage_data,
        }

    def get_bracket(self, match_id: str) -> Optional[Dict[str, Any]]:
        """Get bracket data from cache."""
        return self.all_brackets.get(match_id)

    def get_dispute(self, dispute_id: str) -> Optional[Dict[str, Any]]:
        """Get dispute data from cache."""
        return self.all_disputes.get(dispute_id)
