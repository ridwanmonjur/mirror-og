"""
Logging configuration using loguru.
Replacement for Winston logger from Node.js (node/src/utils/logger.ts)
"""
import sys
from loguru import logger
from app.core.config import settings


def configure_logging():
    """Configure loguru logger with appropriate settings."""
    # Remove default handler
    logger.remove()

    # Add console handler with formatting
    logger.add(
        sys.stdout,
        format="<green>{time:YYYY-MM-DD HH:mm:ss}</green> | <level>{level: <8}</level> | <cyan>{name}</cyan>:<cyan>{function}</cyan>:<cyan>{line}</cyan> - <level>{message}</level>",
        level=settings.LOG_LEVEL.upper(),
        colorize=True,
    )

    # Add file handler for errors
    logger.add(
        "logs/error.log",
        format="{time:YYYY-MM-DD HH:mm:ss} | {level: <8} | {name}:{function}:{line} - {message}",
        level="ERROR",
        rotation="10 MB",
        retention="30 days",
        compression="zip",
    )

    # Add file handler for all logs
    logger.add(
        "logs/combined.log",
        format="{time:YYYY-MM-DD HH:mm:ss} | {level: <8} | {name}:{function}:{line} - {message}",
        level="DEBUG",
        rotation="10 MB",
        retention="7 days",
        compression="zip",
    )

    logger.info("Logging configured")


# Configure logging on module import
try:
    configure_logging()
except Exception as e:
    print(f"Failed to configure logging: {e}")
