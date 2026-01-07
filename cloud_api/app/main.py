"""
FastAPI Application - Main entry point.
Ported from node/src/index.ts (206 LOC)
"""
from contextlib import asynccontextmanager
from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from slowapi import _rate_limit_exceeded_handler
from slowapi.errors import RateLimitExceeded
from loguru import logger

from app.core.config import settings
from app.core.database import connect_db, disconnect_db, test_database_connection
from app.core.firebase import initialize_firebase
from app.core.rate_limit import limiter
import app.core.logging  # Initialize logging


@asynccontextmanager
async def lifespan(app: FastAPI):
    """
    Application lifespan manager.
    Handles startup and shutdown events.
    """
    # Startup
    logger.info("Starting up Driftwood FastAPI server...")
    logger.info(f"Environment: {settings.ENVIRONMENT}")
    logger.info(f"Port: {settings.PORT}")

    try:
        # Connect to database
        await connect_db()
        await test_database_connection()

        # Initialize Firebase
        initialize_firebase()
        logger.info("Firebase initialized")

        logger.info("Application startup complete")

    except Exception as e:
        logger.error(f"Failed to initialize application: {e}")
        raise

    yield

    # Shutdown
    logger.info("Shutting down...")
    await disconnect_db()
    logger.info("Shutdown complete")


# Create FastAPI app
app = FastAPI(
    title="Driftwood API",
    description="FastAPI version of Driftwood esports platform API",
    version="2.0.0",
    lifespan=lifespan,
)

# Configure CORS based on environment
origins_map = {
    "prod": ["https://driftwood.gg"],
    "staging": ["https://oceansgaming.gg"],
    "dev": [
        "http://localhost:8000",
        "http://127.0.0.1:8000",
        "http://localhost:5173",  # Vite dev server
        "http://127.0.0.1:5173",
    ],
}

allowed_origins = origins_map.get(settings.ENVIRONMENT, origins_map["dev"])

app.add_middleware(
    CORSMiddleware,
    allow_origins=allowed_origins if settings.ENVIRONMENT != "dev" else ["*"],
    allow_credentials=True,
    allow_methods=["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS", "HEAD"],
    allow_headers=[
        "Accept",
        "Accept-Language",
        "Content-Language",
        "Content-Type",
        "Authorization",
        "X-Requested-With",
        "X-Firebase-AppCheck",
        "Origin",
        "Referer",
        "User-Agent",
    ],
    expose_headers=["Content-Type", "Authorization", "X-Firebase-AppCheck"],
    max_age=86400,  # 24 hours
)

# Rate limiting
app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, _rate_limit_exceeded_handler)


# Request logging middleware
@app.middleware("http")
async def log_requests(request: Request, call_next):
    """Log all incoming requests."""
    logger.info(f"{request.method} {request.url.path} from {request.client.host}")
    response = await call_next(request)
    return response


# Health check endpoint
@app.get("/health")
async def health_check():
    """Health check endpoint."""
    return {
        "status": "healthy",
        "service": "driftwood-api",
        "version": "2.0.0",
        "environment": settings.ENVIRONMENT,
    }


# Global exception handler
@app.exception_handler(Exception)
async def global_exception_handler(request: Request, exc: Exception):
    """Handle all unhandled exceptions."""
    logger.error(f"Unhandled exception: {exc}", exc_info=True)
    return JSONResponse(
        status_code=500,
        content={
            "success": False,
            "error": "Internal server error",
            "message": str(exc) if settings.ENVIRONMENT == "dev" else "An error occurred"
        }
    )


# Include API routers
from app.api.v1.routers import public, user, participant, organizer, tournament

app.include_router(public.router, prefix="/api", tags=["Public"])
app.include_router(user.router, prefix="/api", tags=["User"])
app.include_router(participant.router, prefix="/api/participant", tags=["Participant"])
app.include_router(organizer.router, prefix="/api/organizer", tags=["Organizer"])
app.include_router(tournament.router, tags=["Tournament"])


# Root endpoint
@app.get("/")
async def root():
    """Root endpoint."""
    return {
        "message": "Driftwood API",
        "version": "2.0.0",
        "docs": "/docs",
        "health": "/health",
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.main:app",
        host="0.0.0.0",
        port=settings.PORT,
        reload=settings.ENVIRONMENT == "dev",
        log_level=settings.LOG_LEVEL.lower(),
    )
