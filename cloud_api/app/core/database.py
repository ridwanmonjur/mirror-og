"""
Database configuration using Databases library (SQLAlchemy Core query builder).
Ported from node/src/config/database.ts
"""
from databases import Database
from sqlalchemy import MetaData
from app.core.config import settings
from loguru import logger


# Database instance for query building
database = Database(
    settings.DATABASE_URL,
    min_size=5,
    max_size=10
)

# Metadata for table definitions
metadata = MetaData()


async def get_db():
    """
    Dependency for database access in routes.
    Returns the database instance for query building.
    """
    return database


async def connect_db():
    """Connect to database on startup."""
    try:
        await database.connect()
        logger.info("Database connected successfully")
        logger.info(f"Database: {settings.DB_DATABASE} on {settings.DB_HOST}:{settings.DB_PORT}")
    except Exception as e:
        logger.error(f"Failed to connect to database: {e}")
        raise


async def disconnect_db():
    """Disconnect from database on shutdown."""
    try:
        await database.disconnect()
        logger.info("Database disconnected")
    except Exception as e:
        logger.error(f"Error disconnecting from database: {e}")


async def test_database_connection():
    """Test database connection by executing a simple query."""
    try:
        result = await database.fetch_one("SELECT 1 as test")
        if result and result["test"] == 1:
            logger.info("Database connection test passed")
            return True
        else:
            logger.error("Database connection test failed")
            return False
    except Exception as e:
        logger.error(f"Database connection test error: {e}")
        return False
