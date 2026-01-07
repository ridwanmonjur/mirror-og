"""
Configuration settings for the FastAPI application.
Uses Pydantic Settings to load environment variables.
"""
from pydantic_settings import BaseSettings
from typing import Optional


class Settings(BaseSettings):
    """Application settings loaded from environment variables."""

    # Server
    PORT: int = 3000
    ENVIRONMENT: str = "dev"
    NODE_ENV: str = "development"

    # MySQL Database
    DB_HOST: str
    DB_PORT: int = 3306
    DB_DATABASE: str
    DB_USERNAME: str
    DB_PASSWORD: str = ""

    @property
    def DATABASE_URL(self) -> str:
        """Construct database URL for Databases library."""
        return f"mysql+aiomysql://{self.DB_USERNAME}:{self.DB_PASSWORD}@{self.DB_HOST}:{self.DB_PORT}/{self.DB_DATABASE}"

    # JWT (must match Laravel configuration)
    JWT_SECRET: str  # base64:... format from Laravel APP_KEY
    JWT_ALGORITHM: str = "HS256"

    # Firebase
    FIREBASE_PROJECT_ID: str
    FIREBASE_DATABASE_ID: str = "(default)"
    FIREBASE_CREDENTIALS_PATH: Optional[str] = None
    FIREBASE_EMULATOR_HOST: Optional[str] = None

    # Logging
    LOG_LEVEL: str = "info"

    class Config:
        env_file = ".env"
        case_sensitive = True


# Global settings instance
settings = Settings()
