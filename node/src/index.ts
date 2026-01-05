import express, { Express } from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import { Logger } from './utils/logger';
import { testDatabaseConnection } from './config/database';
import { initializeFirebase } from './config/firebase';
import { errorHandler, notFoundHandler } from './middleware/errorHandler';
import bracketRoutes from './routes/brackets';
import authRoutes from './routes/auth';
import tournamentRoutes from './routes/tournaments';
import publicApiRoutes from './routes/publicApi';
import userApiRoutes from './routes/userApi';
import participantApiRoutes from './routes/participantApi';
import organizerApiRoutes from './routes/organizerApi';

// Load environment variables
dotenv.config();

const app: Express = express();
const port = process.env.PORT || 3000;
const environment = process.env.ENVIRONMENT || 'dev';

// Configure CORS origins based on environment
let allowedOrigins: string[] = [];
if (environment === 'prod') {
  allowedOrigins = ['https://driftwood.gg'];
} else if (environment === 'staging') {
  allowedOrigins = ['https://oceansgaming.gg'];
} else {
  // dev
  allowedOrigins = [
    'http://localhost:8000',
    'http://127.0.0.1:8000',
    'http://localhost:5173', // Vite dev server
    'http://127.0.0.1:5173',
  ];
}

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
app.use(
  cors({
    origin: (origin, callback) => {
      // Allow requests with no origin (like mobile apps or curl requests)
      if (!origin) return callback(null, true);

      if (environment === 'dev') {
        // In dev mode, allow all origins
        return callback(null, true);
      }

      if (allowedOrigins.includes(origin)) {
        callback(null, true);
      } else {
        callback(new Error('Not allowed by CORS'));
      }
    },
    credentials: true,
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'],
    allowedHeaders: [
      'Accept',
      'Accept-Language',
      'Content-Language',
      'Content-Type',
      'Authorization',
      'X-Requested-With',
      'X-Firebase-AppCheck',
      'Origin',
      'Referer',
      'User-Agent',
    ],
    exposedHeaders: ['Content-Type', 'Authorization', 'X-Firebase-AppCheck'],
    maxAge: 86400, // 24 hours
  })
);
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Request logging middleware
app.use((req, _res, next) => {
  Logger.log(`${req.method} ${req.path}`, {
    ip: req.ip,
    userAgent: req.get('user-agent'),
  });
  next();
});

/**
 * Health check endpoint
 */
app.get('/health', (_req, res) => {
  res.json({
    status: 'healthy',
    service: 'driftwood-client-auth',
    timestamp: new Date().toISOString(),
    environment: process.env.NODE_ENV || 'development',
  });
});

/**
 * API Routes
 */
// Bracket API (existing)
app.use('/api/brackets', bracketRoutes);

/**
 * Auth Routes (converted from cloud_client_auth/main.py)
 */
app.use('/auth', authRoutes);

/**
 * Tournament Routes (converted from cloud_server_functions/main.py)
 */
app.use('/', tournamentRoutes);

/**
 * Laravel API Routes Conversion
 * Converted from routes/api.php
 */

// Public API Routes (no authentication required)
app.use('/api', publicApiRoutes);

// User API Routes (authenticated, any role)
app.use('/api', userApiRoutes);

// Participant API Routes (participant or admin)
app.use('/api/participant', participantApiRoutes);

// Organizer API Routes (organizer or admin)
app.use('/api/organizer', organizerApiRoutes);

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
process.on('unhandledRejection', (reason) => {
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
