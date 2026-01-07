"""
Firebase Admin SDK initialization.
Ported from node/src/config/firebase.ts
"""
import os
import firebase_admin
from firebase_admin import credentials, firestore
from app.core.config import settings
from loguru import logger


_firestore_client = None


def initialize_firebase():
    """
    Initialize Firebase Admin SDK.
    Supports both production (with credentials file) and emulator mode.
    """
    global _firestore_client

    try:
        # Configure emulator if specified
        if settings.FIREBASE_EMULATOR_HOST:
            os.environ["FIRESTORE_EMULATOR_HOST"] = settings.FIREBASE_EMULATOR_HOST
            logger.info(f"Using Firebase emulator at {settings.FIREBASE_EMULATOR_HOST}")

        # Initialize Firebase Admin
        if settings.FIREBASE_CREDENTIALS_PATH and os.path.exists(settings.FIREBASE_CREDENTIALS_PATH):
            cred = credentials.Certificate(settings.FIREBASE_CREDENTIALS_PATH)
            firebase_admin.initialize_app(cred, {
                'projectId': settings.FIREBASE_PROJECT_ID,
            })
            logger.info(f"Firebase initialized with credentials from {settings.FIREBASE_CREDENTIALS_PATH}")
        else:
            # Initialize without credentials (uses default application credentials)
            firebase_admin.initialize_app(options={
                'projectId': settings.FIREBASE_PROJECT_ID,
            })
            logger.info("Firebase initialized with default application credentials")

        # Get Firestore client
        _firestore_client = firestore.client()
        logger.info("Firestore client initialized")

        return _firestore_client

    except Exception as e:
        logger.error(f"Failed to initialize Firebase: {e}")
        raise


def get_firestore_client():
    """Get the Firestore client instance."""
    global _firestore_client
    if _firestore_client is None:
        _firestore_client = initialize_firebase()
    return _firestore_client


# Initialize on module import
try:
    db = get_firestore_client()
except Exception as e:
    logger.warning(f"Firebase initialization deferred: {e}")
    db = None
