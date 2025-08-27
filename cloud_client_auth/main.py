import functions_framework
import firebase_admin
from firebase_admin import auth
from datetime import datetime, timedelta
from jose import jwt
import os
import logging
from flask import jsonify
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
import time

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Initialize Firebase Admin SDK
if not firebase_admin._apps:
    firebase_admin.initialize_app()

# Environment variables
SECRET_KEY = os.getenv('SECRET_KEY', 'your-secret-key')
ALGORITHM = 'HS256'
ACCESS_TOKEN_EXPIRE_MINUTES = 30
ENVIRONMENT = os.environ.get('ENVIRONMENT', 'dev')

# Configure CORS origins based on environment
if ENVIRONMENT == 'prod':
    ALLOWED_ORIGINS = ["https://driftwood.gg"]
elif ENVIRONMENT == 'staging':
    ALLOWED_ORIGINS = ["https://oceansgaming.gg"]
else:  # dev
    ALLOWED_ORIGINS = [
        "http://localhost:8000",
        "http://127.0.0.1:8000",
        "http://localhost:5173",  # Vite dev server
        "http://127.0.0.1:5173"
    ]

# Initialize rate limiter (simplified for Cloud Functions)
request_counts = {}

def check_rate_limit(client_ip, limit_per_minute=30):
    """Simple in-memory rate limiting."""
    current_time = time.time()
    minute_key = int(current_time // 60)
    
    # Clean old entries
    keys_to_delete = [k for k in request_counts.keys() if k[1] < minute_key - 1]
    for k in keys_to_delete:
        del request_counts[k]
    
    # Check current minute count
    count_key = (client_ip, minute_key)
    current_count = request_counts.get(count_key, 0)
    
    if current_count >= limit_per_minute:
        return False
    
    request_counts[count_key] = current_count + 1
    return True

@functions_framework.http
def driftwood_client_auth(request):
    """Main API endpoint for auth and health."""
    start_time = time.time()
    client_ip = request.headers.get('X-Forwarded-For', request.environ.get('REMOTE_ADDR', 'unknown'))
    if client_ip and ',' in client_ip:
        client_ip = client_ip.split(',')[0].strip()
    
    # Log request
    auth_header = request.headers.get('Authorization', 'None')
    auth_info = f"{auth_header[:20]}..." if len(auth_header) > 20 else auth_header
    user_agent = request.headers.get('User-Agent', 'Unknown')[:50]
    logging.info(f"📥 {request.method}\t{request.path}\t{client_ip}\t{auth_info}\t{user_agent}")
    
    # CORS headers
    origin = request.headers.get('Origin')
    headers = {'Content-Type': 'application/json'}
    
    if origin in ALLOWED_ORIGINS:
        headers['Access-Control-Allow-Origin'] = origin
        headers['Access-Control-Allow-Credentials'] = 'true'
    elif ENVIRONMENT == 'dev':
        headers['Access-Control-Allow-Origin'] = '*'
    
    headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS'
    headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, X-Requested-With'
    headers['Access-Control-Max-Age'] = '86400'
    
    # Handle preflight OPTIONS requests
    if request.method == 'OPTIONS':
        return ('', 200, headers)
    
    # Rate limiting
    if not check_rate_limit(client_ip, 30):
        process_time = (time.time() - start_time) * 1000
        logging.warning(f"⚠️ {request.method}\t{request.path}\t429\t{process_time:.2f}ms\t{client_ip}")
        return (jsonify({'error': 'Too Many Requests'}), 429, headers)
    
    try:
        # Route based on path
        if request.path == '/auth/token' and request.method == 'POST':
            return handle_auth_token(request, headers, start_time, client_ip)
        elif request.path == '/health' and request.method == 'GET':
            return handle_health_check(headers, start_time, client_ip)
        else:
            process_time = (time.time() - start_time) * 1000
            logging.info(f"⚠️ {request.method}\t{request.path}\t404\t{process_time:.2f}ms\t{client_ip}")
            return (jsonify({'error': 'Not found'}), 404, headers)
            
    except Exception as e:
        process_time = (time.time() - start_time) * 1000
        logging.error(f"💥 {request.method}\t{request.path}\t500\t{process_time:.2f}ms\t{client_ip} - {e}")
        return (jsonify({'error': 'Internal server error'}), 500, headers)

def handle_auth_token(request, headers, start_time, client_ip):
    """Handle auth token creation."""
    try:
        request_json = request.get_json(silent=True)
        logging.info(f"Raw request data: {request.get_data()}")
        logging.info(f"Parsed JSON data: {request_json}")
        
        if not request_json or 'uid' not in request_json:
            process_time = (time.time() - start_time) * 1000
            logging.info(f"❌ POST\t/auth/token\t400\t{process_time:.2f}ms\t{client_ip}")
            return (jsonify({'detail': 'uid is required'}), 400, headers)
        
        uid = str(request_json['uid'])
        if not uid:
            process_time = (time.time() - start_time) * 1000
            logging.info(f"❌ POST\t/auth/token\t400\t{process_time:.2f}ms\t{client_ip}")
            return (jsonify({'detail': 'uid is required'}), 400, headers)
        
        # Get additional user data from request
        role = request_json.get('role', 'PARTICIPANT')
        team_id = request_json.get('teamId')
        
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
        
        process_time = (time.time() - start_time) * 1000
        logging.info(f"✅ POST\t/auth/token\t200\t{process_time:.2f}ms\t{client_ip}")
        logging.info("─" * 80)
        
        return (jsonify(response_data), 200, headers)
        
    except Exception as e:
        process_time = (time.time() - start_time) * 1000
        logging.error(f"❌ POST\t/auth/token\t500\t{process_time:.2f}ms\t{client_ip} - Error creating token: {e}")
        logging.info("─" * 80)
        return (jsonify({'detail': 'Failed to create authentication token'}), 500, headers)

def handle_health_check(headers, start_time, client_ip):
    """Handle health check."""
    response_data = {
        'status': 'healthy',
        'service': 'driftwood-client-auth',
        'environment': ENVIRONMENT,
        'timestamp': datetime.utcnow().isoformat()
    }
    
    process_time = (time.time() - start_time) * 1000
    logging.info(f"✅ GET\t/health\t200\t{process_time:.2f}ms\t{client_ip}")
    logging.info("─" * 80)
    
    return (jsonify(response_data), 200, headers)

if __name__ == '__main__':
    # For local testing
    from flask import Flask
    app = Flask(__name__)
    app.add_url_rule('/', 'driftwood_client_auth', driftwood_client_auth, methods=['GET', 'POST', 'OPTIONS'])
    app.add_url_rule('/<path:path>', 'driftwood_client_auth_path', driftwood_client_auth, methods=['GET', 'POST', 'OPTIONS'])
    app.run(debug=True, host='0.0.0.0', port=8080)