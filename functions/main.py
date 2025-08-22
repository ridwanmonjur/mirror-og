import functions_framework
import firebase_admin
from firebase_admin import auth
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

def handle_health_check(headers):
    """Handle health check."""
    return (jsonify({
        'status': 'healthy',
        'service': 'driftwood-api',
        'timestamp': datetime.utcnow().isoformat()
    }), 200, headers)

# Alias for backwards compatibility
health_check = driftwood_api