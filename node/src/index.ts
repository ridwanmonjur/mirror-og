import express, { Express } from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import { Logger } from './utils/logger';
import { testDatabaseConnection } from './config/database';
import { initializeFirebase } from './config/firebase';
import { errorHandler, notFoundHandler } from './middleware/errorHandler';
import bracketRoutes from './routes/brackets';

// Load environment variables
dotenv.config();

const app: Express = express();
const port = process.env.PORT || 3000;

/**
 * Initialize application
 */
async function initializeApp() {
  try {
    // Test database connection
    await testDatabaseConnection();

    // Initialize Firebase
    initializeFirebase();

    Logger.log('Application initialized successfully');
  } catch (error) {
    Logger.error('Failed to initialize application', error);
    process.exit(1);
  }
}

/**
 * Middleware configuration
 */
app.use(cors());
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Request logging middleware
app.use((req, res, next) => {
  Logger.log(`${req.method} ${req.path}`, {
    ip: req.ip,
    userAgent: req.get('user-agent'),
  });
  next();
});

/**
 * Health check endpoint
 */
app.get('/health', (req, res) => {
  res.json({
    success: true,
    message: 'Bracket API is running',
    timestamp: new Date().toISOString(),
    environment: process.env.NODE_ENV || 'development',
  });
});

/**
 * API Routes
 */
app.use('/api/brackets', bracketRoutes);

/**
 * Error handling
 */
app.use(notFoundHandler);
app.use(errorHandler);

/**
 * Start server
 */
async function startServer() {
  try {
    await initializeApp();

    app.listen(port, () => {
      Logger.log(`Server running on port ${port}`);
      Logger.log(`Environment: ${process.env.NODE_ENV || 'development'}`);
      Logger.log(`Health check: http://localhost:${port}/health`);
    });
  } catch (error) {
    Logger.error('Failed to start server', error);
    process.exit(1);
  }
}

// Handle uncaught exceptions
process.on('uncaughtException', (error) => {
  Logger.error('Uncaught Exception', error);
  process.exit(1);
});

// Handle unhandled promise rejections
process.on('unhandledRejection', (reason, promise) => {
  Logger.error('Unhandled Rejection', reason);
  process.exit(1);
});

// Graceful shutdown
process.on('SIGTERM', () => {
  Logger.log('SIGTERM received, shutting down gracefully');
  process.exit(0);
});

process.on('SIGINT', () => {
  Logger.log('SIGINT received, shutting down gracefully');
  process.exit(0);
});

// Start the server
if (require.main === module) {
  startServer();
}

// Export app for testing
export default app;
