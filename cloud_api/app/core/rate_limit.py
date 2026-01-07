"""
Rate limiting configuration using slowapi.
Ported from node/src/middleware/rateLimiter.ts
"""
from slowapi import Limiter
from slowapi.util import get_remote_address


# Initialize limiter with remote address as key
limiter = Limiter(key_func=get_remote_address)


# Rate limit decorators matching Node.js rates
# Usage: @router.post("/endpoint")
#        @auth_rate_limit
#        async def endpoint(): ...

# Authentication endpoints: 30 requests per minute
auth_rate_limit = limiter.limit("30/minute")

# Tournament endpoints: 60 requests per minute
tournament_rate_limit = limiter.limit("60/minute")

# Batch operation endpoints: 20 requests per minute
batch_rate_limit = limiter.limit("20/minute")

# Deadline processing endpoints: 10 requests per minute
deadline_rate_limit = limiter.limit("10/minute")

# General API endpoints: 100 requests per minute
general_rate_limit = limiter.limit("100/minute")
