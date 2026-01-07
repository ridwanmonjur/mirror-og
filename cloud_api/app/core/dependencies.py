"""
FastAPI dependencies for authentication and authorization.
Ported from node/src/middleware/auth.ts and node/src/middleware/roleCheck.ts
"""
import base64
from typing import Annotated
from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import jwt, JWTError
from pydantic import BaseModel
from app.core.config import settings
from loguru import logger


# HTTPBearer security scheme
security = HTTPBearer()


class JWTUser(BaseModel):
    """JWT user payload model."""
    id: str
    role: str
    email: str | None = None
    name: str | None = None


def decode_laravel_key(key: str) -> str:
    """
    Decode Laravel's base64: prefixed APP_KEY.
    Laravel stores APP_KEY in format: base64:encodedstring
    """
    if key.startswith("base64:"):
        try:
            decoded = base64.b64decode(key[7:]).decode('utf-8')
            return decoded
        except Exception as e:
            logger.error(f"Failed to decode Laravel APP_KEY: {e}")
            return key
    return key


async def get_current_user(
    credentials: Annotated[HTTPAuthorizationCredentials, Depends(security)]
) -> JWTUser:
    """
    Verify JWT token from Laravel and return user.

    Ported from: node/src/middleware/auth.ts authenticateJWT()

    Args:
        credentials: HTTP Authorization header with Bearer token

    Returns:
        JWTUser: Authenticated user information

    Raises:
        HTTPException: 401 if token is invalid or expired
    """
    try:
        # Decode Laravel APP_KEY
        secret = decode_laravel_key(settings.JWT_SECRET)

        # Verify and decode JWT token
        payload = jwt.decode(
            credentials.credentials,
            secret,
            algorithms=[settings.JWT_ALGORITHM]
        )

        # Extract user data from JWT payload
        # Laravel JWT structure may vary, check multiple possible fields
        user = JWTUser(
            id=payload.get("sub") or payload.get("user_id") or payload.get("id"),
            role=payload.get("role", "PARTICIPANT"),
            email=payload.get("email"),
            name=payload.get("name")
        )

        if not user.id:
            logger.error(f"JWT token missing user ID. Payload: {payload}")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid token payload: missing user ID"
            )

        return user

    except JWTError as e:
        error_msg = str(e)
        if "expired" in error_msg.lower():
            logger.warn("Expired JWT token")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Token expired"
            )
        else:
            logger.warn(f"Invalid JWT token: {error_msg}")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail=f"Invalid token: {error_msg}"
            )
    except Exception as e:
        logger.error(f"JWT authentication error: {e}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Authentication error"
        )


def require_role(*roles: str):
    """
    Dependency factory for role-based access control.

    Ported from: node/src/middleware/roleCheck.ts

    Args:
        *roles: Allowed roles (e.g., "participant", "organizer", "admin")

    Returns:
        Dependency function that checks user role
    """
    def role_checker(user: Annotated[JWTUser, Depends(get_current_user)]) -> JWTUser:
        """Check if user has required role."""
        if user.role.upper() not in [r.upper() for r in roles]:
            logger.warn(f"User {user.id} with role {user.role} attempted to access endpoint requiring: {roles}")
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail=f"Access denied. Required role: {' or '.join(roles)}"
            )
        return user
    return role_checker


# Pre-configured role dependencies for convenience
CurrentUser = Annotated[JWTUser, Depends(get_current_user)]
ParticipantUser = Annotated[JWTUser, Depends(require_role("participant", "admin"))]
OrganizerUser = Annotated[JWTUser, Depends(require_role("organizer", "admin"))]
AdminUser = Annotated[JWTUser, Depends(require_role("admin"))]
AnyAuthenticatedUser = Annotated[JWTUser, Depends(get_current_user)]
