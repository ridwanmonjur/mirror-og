import firebase_admin
from firebase_admin import auth, firestore
from datetime import datetime, timedelta
from jose import jwt
import os
import logging
from typing import Optional, List, Dict, Any
from pydantic import BaseModel
from fastapi import FastAPI, HTTPException, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse

# Initialize Firebase Admin
if not firebase_admin._apps:
    firebase_admin.initialize_app()

# Environment variables
SECRET_KEY = os.getenv('SECRET_KEY', 'your-secret-key')
ALGORITHM = 'HS256'
ACCESS_TOKEN_EXPIRE_MINUTES = 30

# Create FastAPI app
app = FastAPI(
    title="Driftwood API",
    description="Driftwood esports platform API",
    version="2.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Pydantic models for request/response validation
class AuthTokenRequest(BaseModel):
    uid: str
    role: Optional[str] = "PARTICIPANT"
    teamId: Optional[str] = None

class RoomBlockRequest(BaseModel):
    user1: str
    user2: str
    action: str  # 'block' or 'unblock'
    blocked_by: Optional[str] = None

class BatchReportsRequest(BaseModel):
    event_id: str
    count: int
    custom_values_array: Optional[List[Dict[str, Any]]] = []
    specific_ids: Optional[List[str]] = []
    games_per_match: Optional[int] = 3

class BatchDisputesRequest(BaseModel):
    event_id: str
    count: int
    custom_values_array: Optional[List[Dict[str, Any]]] = []
    specific_ids: Optional[List[str]] = []

class DeadlineTasksRequest(BaseModel):
    detail_id: str
    matches: Optional[List[Dict[str, Any]]] = []
    bracket_info: Optional[Dict[str, Any]] = {}
    tier_id: Optional[str] = None
    is_league: Optional[bool] = False
    games_per_match: Optional[int] = 3

# Auth token endpoint
@app.post("/auth/token")
async def create_auth_token(request: AuthTokenRequest):
    """Create authentication token for user."""
    try:
        uid = str(request.uid)
        if not uid:
            raise HTTPException(status_code=400, detail="uid is required")
        
        # Create custom claims
        custom_claims = {
            "uid": uid,
            "source": "driftwood-laravel",
            "role": request.role,
            "userId": uid,
            "teamId": request.teamId
        }
        
        # Create Firebase custom token
        custom_token = auth.create_custom_token(uid, custom_claims)
        
        # Create JWT token
        expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        jwt_payload = {
            "uid": uid,
            "exp": expire,
            "iat": datetime.utcnow()
        }
        jwt_token = jwt.encode(jwt_payload, SECRET_KEY, algorithm=ALGORITHM)
        
        # Return same format as Flask
        return {
            'token': custom_token.decode('utf-8'),
            'jwt_token': jwt_token,
            'expires_at': expire.isoformat()
        }
        
    except Exception as e:
        logging.error(f"Error creating token: {e}")
        raise HTTPException(status_code=500, detail="Failed to create authentication token")

# Room block/unblock endpoint
@app.post("/room/block")
async def handle_room_block(request: RoomBlockRequest):
    """Handle room blocking/unblocking operations."""
    try:
        if request.action not in ['block', 'unblock']:
            raise HTTPException(status_code=400, detail='action must be "block" or "unblock"')
        
        if request.action == 'block' and not request.blocked_by:
            raise HTTPException(status_code=400, detail='blocked_by is required for block action')
        
        db = firestore.client()
        room_collection = db.collection('room')
        
        query1 = room_collection.where('user1', '==', str(request.user1)).where('user2', '==', str(request.user2))
        query2 = room_collection.where('user2', '==', str(request.user1)).where('user1', '==', str(request.user2))
        
        rooms_updated = 0
        
        for doc in query1.stream():
            if request.action == 'block':
                doc.reference.update({'blocked_by': str(request.blocked_by)})
            else:  # unblock
                doc.reference.update({'blocked_by': None})
            rooms_updated += 1
        
        for doc in query2.stream():
            if request.action == 'block':
                doc.reference.update({'blocked_by': str(request.blocked_by)})
            else:  # unblock
                doc.reference.update({'blocked_by': None})
            rooms_updated += 1
        
        # Return same format as Flask
        return {
            'success': True,
            'action': request.action,
            'rooms_updated': rooms_updated,
            'message': f'Successfully {request.action}ed {rooms_updated} room(s)'
        }
        
    except HTTPException:
        raise
    except Exception as e:
        action = getattr(request, 'action', 'unknown')
        logging.error(f"Error in room {action} operation: {e}")
        raise HTTPException(status_code=500, detail=f'Failed to {action} room')

# Batch reports endpoint
@app.post("/batch/reports")
async def create_batch_reports(request: BatchReportsRequest):
    """Create batch reports."""
    try:
        result = createBatchReports(
            request.event_id, 
            request.count, 
            request.custom_values_array, 
            request.specific_ids, 
            request.games_per_match
        )
        return result
        
    except Exception as e:
        logging.error(f"Error in batch reports creation: {e}")
        raise HTTPException(status_code=500, detail="Failed to create batch reports")

# Batch disputes endpoint
@app.post("/batch/disputes")
async def create_batch_disputes(request: BatchDisputesRequest):
    """Create batch disputes."""
    try:
        result = createBatchDisputes(
            request.event_id, 
            request.count, 
            request.custom_values_array, 
            request.specific_ids
        )
        return result
        
    except Exception as e:
        logging.error(f"Error in batch disputes creation: {e}")
        raise HTTPException(status_code=500, detail="Failed to create batch disputes")

# Health check endpoint
@app.get("/health")
async def health_check():
    return {
        'status': 'healthy',
        'service': 'driftwood-api',
        'timestamp': datetime.utcnow().isoformat()
    }

def createBatchReports(event_id, count, custom_values_array=None, specific_ids=None, games_per_match=3):
    """Create or overwrite multiple reports with specified IDs and customizable values using BulkWriter"""
    if custom_values_array is None:
        custom_values_array = []
    if specific_ids is None:
        specific_ids = []
    
    results = []
    
    try:
        db = firestore.client()
        
        # Use batch writes for efficiency
        batch = db.batch()
        
        for i in range(count):
            report_id = specific_ids[i]
            custom_values = custom_values_array[i] if i < len(custom_values_array) else {}
            
            default_report = {
                'completeMatchStatus': 'UPCOMING',
                'defaultWinners': [None] * games_per_match,
                'disputeResolved': [None] * games_per_match,
                'disqualified': False,
                'matchStatus': ['UPCOMING'] * games_per_match,
                'organizerWinners': [None] * games_per_match,
                'position': None,
                'randomWinners': [None] * games_per_match,
                'realWinners': [None] * games_per_match,
                'score': [0, 0],
                'team1Id': None,
                'team1Winners': [None] * games_per_match,
                'team2Id': None,
                'team2Winners': [None] * games_per_match,
            }
            
            # Merge custom values with defaults
            report_data = {**default_report, **custom_values}
            
            doc_ref = db.collection('event').document(str(event_id)).collection('brackets').document(report_id)
            batch.set(doc_ref, report_data)
            
            results.append({
                'statusReport': 'pending',
                'reportId': report_id,
            })
        
        # Commit the batch
        batch.commit()
        
        # Update results to success after commit
        for result in results:
            result['statusReport'] = 'success'
            result['messageReport'] = 'Report created or overwritten successfully'
        
        return {
            'statusReport': 'success',
            'messageReport': 'Batch operation completed - all reports created or overwritten',
            'resultsReport': results,
        }
        
    except Exception as e:
        logging.error(f'createBatchReports error: {e}')
        return {
            'statusReport': 'error',
            'messageReport': str(e),
            'resultsReport': results,
        }

def createBatchDisputes(event_id, count, custom_values_array=None, specific_ids=None):
    """Create or overwrite multiple dispute documents with specified IDs"""
    if custom_values_array is None:
        custom_values_array = []
    if specific_ids is None:
        specific_ids = []
    
    results = []
    
    try:
        db = firestore.client()
        
        for i in range(count):
            dispute_id = specific_ids[i]
            custom_values = custom_values_array[i] if i < len(custom_values_array) else {}
            
            default_dispute = {
                'created_at': firestore.SERVER_TIMESTAMP,
                'dispute_description': None,
                'dispute_image_videos': [],
                'dispute_reason': None,
                'dispute_teamId': None,
                'dispute_teamNumber': None,
                'dispute_userId': None,
                'event_id': str(event_id),
                'match_number': None,
                'report_id': None,
                'resolution_resolved_by': None,
                'resolution_winner': None,
                'response_explanation': None,
                'response_teamId': None,
                'response_teamNumber': None,
                'response_userId': None,
                'updated_at': firestore.SERVER_TIMESTAMP,
            }
            
            dispute_data = {**default_dispute, **custom_values}
            
            doc_ref = db.collection('event').document(str(event_id)).collection('disputes').document(dispute_id)
            doc_ref.set(dispute_data)
            
            results.append({
                'statusDispute': 'success',
                'disputeId': dispute_id,
                'messageDispute': 'Dispute created or overwritten successfully',
            })
        
        return {
            'statusDispute': 'success',
            'messageDispute': 'Individual operation completed - all disputes created or overwritten',
            'resultsDispute': results,
        }
        
    except Exception as e:
        logging.error(f'createBatchDisputes error: {e}')
        return {
            'statusDispute': 'error',
            'messageDispute': str(e),
            'resultsDispute': results,
        }

# DeadlineTaskTrait Implementation

# Dispute Enums - equivalent to Laravel's enum configuration
DISPUTE_ENUMS = {
    'ORGANIZER': 3,
    'DISPUTEE': 4,
    'RESPONDER': 5,
    'TIME': 6,
    'RANDOM': 7,
}

class DeadlineTaskTrait:
    """Python implementation of the DeadlineTaskTrait functionality"""
    
    def __init__(self):
        self.db = firestore.client()
        self.all_brackets = {}
        self.all_disputes = {}
        self.dispute_enums = DISPUTE_ENUMS
    
    def fetch_all_event_data(self, event_details_id):
        """Fetch all brackets and disputes for an event in bulk"""
        try:
            event_id = str(event_details_id)
            
            # Fetch all brackets for this event
            brackets_collection = self.db.collection('event').document(event_id).collection('brackets')
            bracket_docs = brackets_collection.stream()
            
            self.all_brackets = {}
            for doc in bracket_docs:
                self.all_brackets[doc.id] = doc.to_dict()
            
            # Fetch all disputes for this event
            disputes_collection = self.db.collection('event').document(event_id).collection('disputes')
            dispute_docs = disputes_collection.stream()
            
            self.all_disputes = {}
            for doc in dispute_docs:
                self.all_disputes[doc.id] = doc.to_dict()
            
            logging.info(f'Fetched all event data for {event_id}: {len(self.all_brackets)} brackets, {len(self.all_disputes)} disputes')
            
        except Exception as e:
            logging.error(f'Failed to fetch all event data: {e}')
            raise e
    
    def calc_scores(self, real_winners):
        """Calculate scores from real winners array"""
        score1 = 0
        score2 = 0
        
        for value in real_winners:
            if value is None:
                continue
            if value == '1':
                score1 += 1
            else:
                score2 += 1
        
        return [score1, score2]
    
    def handle_disputes(self, match_status_data, bracket, event_id, will_break_conflicts=False, games_per_match=3):
        """Handle match dispute resolution"""
        real_winners = match_status_data.get('realWinners', [None] * games_per_match)
        dispute_resolved = match_status_data.get('disputeResolved', [None] * games_per_match)
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
                        dispute_ref = self.db.collection('event').document(event_id).collection('disputes').document(dispute_path)
                        
                        # Case 1: One team filed dispute, other hasn't responded
                        if 'dispute_teamNumber' in data and 'response_teamId' not in data:
                            is_updated_dispute = True
                            winner_chosen = str(data['dispute_teamNumber'])
                            real_winners[i] = winner_chosen
                            dispute_resolved[i] = True
                            update_dispute_values[i] = {
                                'resolution_winner': winner_chosen,
                                'resolution_resolved_by': self.dispute_enums['TIME']
                            }
                            dispute_ref_list[i] = dispute_ref
                        
                        # Case 2: Both teams filed conflicting claims and we break conflicts
                        elif will_break_conflicts and 'response_teamNumber' in data:
                            is_updated_dispute = True
                            import random
                            chosen_winner = str(data['dispute_teamNumber']) if random.randint(0, 1) else str(data['response_teamNumber'])
                            real_winners[i] = chosen_winner
                            dispute_resolved[i] = True
                            update_dispute_values[i] = {
                                'resolution_winner': chosen_winner,
                                'resolution_resolved_by': self.dispute_enums['RANDOM']
                            }
                            dispute_ref_list[i] = dispute_ref
        
        scores = self.calc_scores(real_winners)
        
        if is_updated_dispute:
            update_report_values = {
                'realWinners': real_winners,
                'score': scores,
                'disputeResolved': dispute_resolved
            }
        
        return update_report_values, dispute_ref_list, update_dispute_values, is_updated_dispute
    
    def handle_reports(self, match_status_data, games_per_match=3, will_break_ties_and_conflicts=False):
        """Resolve winners for matches with incomplete/conflicted/tied submissions"""
        team1_winners = match_status_data.get('team1Winners', [None] * games_per_match)
        team2_winners = match_status_data.get('team2Winners', [None] * games_per_match)
        real_winners = match_status_data.get('realWinners', [None] * games_per_match)
        default_winners = match_status_data.get('defaultWinners', [None] * games_per_match)
        random_winners = match_status_data.get('randomWinners', [None] * games_per_match)
        
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
                        dispute_resolved = match_status_data.get('disputeResolved', [None] * games_per_match)
                        if dispute_resolved[i] is None or dispute_resolved[i]:
                            import random
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
                for i in range(games_per_match):
                    if team2_winners[i] is None and team1_winners[i] is None:
                        if will_break_ties_and_conflicts:
                            dispute_resolved = match_status_data.get('disputeResolved', [None] * games_per_match)
                            if dispute_resolved[i] is None or dispute_resolved[i]:
                                import random
                                updated = True
                                winner_chosen = str(random.randint(0, 1))
                                real_winners[i] = winner_chosen
                                random_winners[i] = True
        
        if updated:
            new_update = {
                'realWinners': real_winners,
                'score': scores,
                'defaultWinners': default_winners,
                'randomWinners': random_winners,
                'disqualified': disqualified
            }
        
        return new_update, updated
    
    def interpret_deadlines(self, match_status_data, update_values, bracket, extra_bracket, tier_id, 
                          after_organizer_deadline=False, is_league=False, games_per_match=3):
        """Main deadline interpretation logic"""
        
        # Handle disputes
        update_report_values, dispute_ref_list, update_dispute_values, is_updated_dispute = self.handle_disputes(
            match_status_data, bracket, bracket['event_details_id'], after_organizer_deadline, games_per_match
        )
        
        if is_updated_dispute:
            update_values.update(update_report_values)
            match_status_data.update(update_report_values)
        
        # Handle reports
        new_update, updated = self.handle_reports(match_status_data, games_per_match, after_organizer_deadline)
        
        if updated:
            update_values.update(new_update)
            match_status_data.update(new_update)
        
        # Return data for PHP to handle resolveNextStage
        next_stage_data = None
        if not is_league and 'score' in match_status_data:
            scores = match_status_data['score']
            next_stage_data = {
                'bracket': bracket,
                'extra_bracket': extra_bracket,
                'scores': scores,
                'tier_id': tier_id
            }
        
        return {
            'dispute_ref_list': dispute_ref_list,
            'update_dispute_values': update_dispute_values,
            'update_values': update_values,
            'next_stage_data': next_stage_data
        }

# Started tasks endpoint
@app.post("/deadline/started")
async def handle_started_tasks(request: DeadlineTasksRequest):
    """Handle started tournament tasks."""
    try:
        trait = DeadlineTaskTrait()
        trait.fetch_all_event_data(request.detail_id)
        results = []
        
        for match in request.matches:
            match_status_path = f"{match['team1_position']}.{match['team2_position']}"
            
            # Check if bracket exists in our hashmap
            if match_status_path in trait.all_brackets:
                doc_ref = trait.db.collection('event').document(match['event_details_id']).collection('brackets').document(match_status_path)
                
                started_status_array = ['ONGOING'] + ['UPCOMING'] * (request.games_per_match - 1)
                doc_ref.update({
                    'matchStatus': started_status_array,
                    'completeMatchStatus': 'ONGOING'
                })
                
                results.append({
                    'match_id': match_status_path,
                    'status': 'success',
                    'message': 'Match status updated to started'
                })
        
        # Return same format as Flask
        return {
            'status': 'success',
            'message': f'Processed {len(results)} started matches',
            'results': results
        }
        
    except Exception as e:
        logging.error(f"Error in handle_started_tasks: {e}")
        raise HTTPException(status_code=500, detail="Failed to handle started tasks")

# Ended tasks endpoint
@app.post("/deadline/ended")
async def handle_ended_tasks(request: DeadlineTasksRequest):
    """Handle ended tournament tasks."""
    try:
        trait = DeadlineTaskTrait()
        trait.fetch_all_event_data(request.detail_id)
        results = []
        next_stage_data_list = []
        
        for match in request.matches:
            extra_bracket = request.bracket_info.get(match['stage_name'], {}).get(match['inner_stage_name'], {}).get(match['order'], {})
            ended_status_array = ['ENDED'] * request.games_per_match
            
            match_status_path = f"{match['team1_position']}.{match['team2_position']}"
            
            # Use hashmap lookup instead of individual Firestore query
            if match_status_path in trait.all_brackets:
                match_status_data = trait.all_brackets[match_status_path]
                
                initial_update_values = {
                    'matchStatus': ended_status_array,
                    'completeMatchStatus': 'ENDED'
                }
                
                result = trait.interpret_deadlines(
                    match_status_data, initial_update_values, match, extra_bracket, 
                    request.tier_id, False, request.is_league, request.games_per_match
                )
                
                # Update Firestore document
                if result['update_values']:
                    doc_ref = trait.db.collection('event').document(match['event_details_id']).collection('brackets').document(match_status_path)
                    doc_ref.update(result['update_values'])
                
                # Update dispute documents
                for i, dispute_ref in enumerate(result['dispute_ref_list']):
                    if dispute_ref and result['update_dispute_values'][i]:
                        dispute_ref.update(result['update_dispute_values'][i])
                
                # Collect next stage data for PHP to process
                if result['next_stage_data']:
                    next_stage_data_list.append(result['next_stage_data'])
                
                results.append({
                    'match_id': match_status_path,
                    'status': 'success',
                    'message': 'Match ended and processed'
                })
        
        # Return same format as Flask
        return {
            'status': 'success',
            'message': f'Processed {len(results)} ended matches',
            'results': results,
            'next_stage_data': next_stage_data_list
        }
        
    except Exception as e:
        logging.error(f"Error in handle_ended_tasks: {e}")
        raise HTTPException(status_code=500, detail="Failed to handle ended tasks")

# Organizer tasks endpoint
@app.post("/deadline/org")
async def handle_org_tasks(request: DeadlineTasksRequest):
    """Handle organizer deadline tasks."""
    try:
        trait = DeadlineTaskTrait()
        trait.fetch_all_event_data(request.detail_id)
        results = []
        next_stage_data_list = []
        
        for match in request.matches:
            extra_bracket = request.bracket_info.get(match['stage_name'], {}).get(match['inner_stage_name'], {}).get(match['order'], {})
            
            match_status_path = f"{match['team1_position']}.{match['team2_position']}"
            
            # Use hashmap lookup instead of individual Firestore query
            if match_status_path in trait.all_brackets:
                match_status_data = trait.all_brackets[match_status_path]
                
                result = trait.interpret_deadlines(
                    match_status_data, {}, match, extra_bracket, 
                    request.tier_id, True, request.is_league, request.games_per_match
                )
                
                # Update Firestore document
                if result['update_values']:
                    doc_ref = trait.db.collection('event').document(match['event_details_id']).collection('brackets').document(match_status_path)
                    doc_ref.update(result['update_values'])
                
                # Update dispute documents
                for i, dispute_ref in enumerate(result['dispute_ref_list']):
                    if dispute_ref and result['update_dispute_values'][i]:
                        dispute_ref.update(result['update_dispute_values'][i])
                
                # Collect next stage data for PHP to process
                if result['next_stage_data']:
                    next_stage_data_list.append(result['next_stage_data'])
                
                results.append({
                    'match_id': match_status_path,
                    'status': 'success',
                    'message': 'Organizer deadline processed'
                })
        
        # Return same format as Flask
        return {
            'status': 'success',
            'message': f'Processed {len(results)} organizer tasks',
            'results': results,
            'next_stage_data': next_stage_data_list
        }
        
    except Exception as e:
        logging.error(f"Error in handle_org_tasks: {e}")
        raise HTTPException(status_code=500, detail="Failed to handle organizer tasks")

# Google's recommended approach for FastAPI with Cloud Functions 2nd gen
import functions_framework
from functions_framework import create_app

@functions_framework.http
def driftwood_api(request):
    """Firebase Functions entry point for FastAPI app."""
    # Google's recommended method: create_app automatically handles ASGI/WSGI conversion
    handler = create_app(app)
    return handler(request)