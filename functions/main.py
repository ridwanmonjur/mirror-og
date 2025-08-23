import functions_framework
import firebase_admin
from firebase_admin import auth, firestore
from datetime import datetime, timedelta
from jose import jwt
import os
import logging
from flask import jsonify

if not firebase_admin._apps:
    firebase_admin.initialize_app()

SECRET_KEY = os.getenv('SECRET_KEY', 'your-secret-key')
ALGORITHM = 'HS256'
ACCESS_TOKEN_EXPIRE_MINUTES = 30

@functions_framework.http
def driftwood_api(request):
    """Main API endpoint for auth and health."""
    headers = {
        'Access-Control-Allow-Origin': '*',
        'Content-Type': 'application/json'
    }
    
    if request.method == 'OPTIONS':
        headers['Access-Control-Allow-Methods'] = 'GET, POST'
        headers['Access-Control-Allow-Headers'] = 'Content-Type'
        headers['Access-Control-Max-Age'] = '3600'
        return ('', 204, headers)
    
    # Route based on path
    if request.path == '/auth/token' and request.method == 'POST':
        return handle_auth_token(request, headers)
    elif request.path == '/room/block' and request.method == 'POST':
        return handle_room_block(request, headers)
    elif request.path == '/batch/reports' and request.method == 'POST':
        return handle_batch_reports(request, headers)
    elif request.path == '/batch/disputes' and request.method == 'POST':
        return handle_batch_disputes(request, headers)
    elif request.path == '/health' and request.method == 'GET':
        return handle_health_check(headers)
    else:
        return (jsonify({'error': 'Not found'}), 404, headers)

def handle_auth_token(request, headers):
    """Handle auth token creation."""
    try:
        request_json = request.get_json()
        if not request_json or 'uid' not in request_json:
            return (jsonify({'error': 'uid is required'}), 400, headers)
        
        uid = str(request_json['uid'])
        if not uid:
            return (jsonify({'error': 'uid is required'}), 400, headers)
        
        # Get additional user data from request
        role = request_json.get('role', 'PARTICIPANT')  # Default role
        team_id = request_json.get('teamId')  # Can be None
        
        custom_claims = {
            "uid": uid,
            "source": "driftwood-laravel",
            "role": role,
            "userId": uid,
            "teamId": team_id
        }
        
        custom_token = auth.create_custom_token(uid, custom_claims)
        
        expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        jwt_payload = {
            "uid": uid,
            "exp": expire,
            "iat": datetime.utcnow()
        }
        jwt_token = jwt.encode(jwt_payload, SECRET_KEY, algorithm=ALGORITHM)
        
        response_data = {
            'token': custom_token.decode('utf-8'),
            'jwt_token': jwt_token,
            'expires_at': expire.isoformat()
        }
        
        return (jsonify(response_data), 200, headers)
        
    except Exception as e:
        logging.error(f"Error creating token: {e}")
        return (jsonify({'error': 'Failed to create authentication token'}), 500, headers)

def handle_room_block(request, headers):
    """Handle room blocking/unblocking operations."""
    try:
        request_json = request.get_json()
        if not request_json:
            return (jsonify({'error': 'Request body is required'}), 400, headers)
        
        user1_id = request_json.get('user1')
        user2_id = request_json.get('user2')
        action = request_json.get('action')  # 'block' or 'unblock'
        blocked_by = request_json.get('blocked_by')
        
        if not user1_id or not user2_id or not action:
            return (jsonify({'error': 'user1, user2, and action are required'}), 400, headers)
        
        if action not in ['block', 'unblock']:
            return (jsonify({'error': 'action must be "block" or "unblock"'}), 400, headers)
        
        if action == 'block' and not blocked_by:
            return (jsonify({'error': 'blocked_by is required for block action'}), 400, headers)
        
        db = firestore.client()
        room_collection = db.collection('room')
        
        query1 = room_collection.where('user1', '==', str(user1_id)).where('user2', '==', str(user2_id))
        query2 = room_collection.where('user2', '==', str(user1_id)).where('user1', '==', str(user2_id))
        
        rooms_updated = 0
        
        for doc in query1.stream():
            if action == 'block':
                doc.reference.update({'blocked_by': str(blocked_by)})
            else:  # unblock
                doc.reference.update({'blocked_by': None})
            rooms_updated += 1
        
        for doc in query2.stream():
            if action == 'block':
                doc.reference.update({'blocked_by': str(blocked_by)})
            else:  # unblock
                doc.reference.update({'blocked_by': None})
            rooms_updated += 1
        
        return (jsonify({
            'success': True,
            'action': action,
            'rooms_updated': rooms_updated,
            'message': f'Successfully {action}ed {rooms_updated} room(s)'
        }), 200, headers)
        
    except Exception as e:
        logging.error(f"Error in room {action} operation: {e}")
        return (jsonify({'error': f'Failed to {action} room'}), 500, headers)

def handle_batch_reports(request, headers):
    """Handle batch reports creation."""
    try:
        request_json = request.get_json()
        if not request_json:
            return (jsonify({'error': 'Request body is required'}), 400, headers)
        
        event_id = request_json.get('event_id')
        count = request_json.get('count')
        custom_values_array = request_json.get('custom_values_array', [])
        specific_ids = request_json.get('specific_ids', [])
        games_per_match = request_json.get('games_per_match', 3)
        
        if not event_id or not count:
            return (jsonify({'error': 'event_id and count are required'}), 400, headers)
        
        result = createBatchReports(event_id, count, custom_values_array, specific_ids, games_per_match)
        return (jsonify(result), 200, headers)
        
    except Exception as e:
        logging.error(f"Error in batch reports creation: {e}")
        return (jsonify({'error': 'Failed to create batch reports'}), 500, headers)

def handle_batch_disputes(request, headers):
    """Handle batch disputes creation."""
    try:
        request_json = request.get_json()
        if not request_json:
            return (jsonify({'error': 'Request body is required'}), 400, headers)
        
        event_id = request_json.get('event_id')
        count = request_json.get('count')
        custom_values_array = request_json.get('custom_values_array', [])
        specific_ids = request_json.get('specific_ids', [])
        
        if not event_id or not count:
            return (jsonify({'error': 'event_id and count are required'}), 400, headers)
        
        result = createBatchDisputes(event_id, count, custom_values_array, specific_ids)
        return (jsonify(result), 200, headers)
        
    except Exception as e:
        logging.error(f"Error in batch disputes creation: {e}")
        return (jsonify({'error': 'Failed to create batch disputes'}), 500, headers)

def handle_health_check(headers):
    """Handle health check."""
    return (jsonify({
        'status': 'healthy',
        'service': 'driftwood-api',
        'timestamp': datetime.utcnow().isoformat()
    }), 200, headers)

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

# Alias for backwards compatibility
health_check = driftwood_api