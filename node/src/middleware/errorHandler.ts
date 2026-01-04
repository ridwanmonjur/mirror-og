import { Request, Response, NextFunction } from 'express';
import { Logger } from '../utils/logger';
import { ZodError } from 'zod';

/**
 * Custom error class for API errors
 */
export class ApiError extends Error {
  statusCode: number;
  isOperational: boolean;

  constructor(message: string, statusCode: number = 500, isOperational: boolean = true) {
    super(message);
    this.statusCode = statusCode;
    this.isOperational = isOperational;
    Error.captureStackTrace(this, this.constructor);
  }
}

/**
 * Global error handling middleware
 *
 * Catches all errors and returns consistent JSON response
 */
export function errorHandler(
  error: Error | ApiError | ZodError,
  _req: Request,
  res: Response,
  _next: NextFunction
): void {
  // Log all errors
  Logger.error('Error occurred', error);

  // Handle Zod validation errors
  if (error instanceof ZodError) {
    const validationErrors = error.errors.map((err) => ({
      field: err.path.join('.'),
      message: err.message,
    }));

    res.status(400).json({
      success: false,
      message: 'Validation error',
      errors: validationErrors,
    });
    return;
  }

  // Handle custom API errors
  if (error instanceof ApiError) {
    res.status(error.statusCode).json({
      success: false,
      message: error.message,
    });
    return;
  }

  // Handle MySQL errors
  if (error.name === 'QueryFailedError' || (error as any).code?.startsWith('ER_')) {
    Logger.error('Database error', error);
    res.status(500).json({
      success: false,
      message: 'Database error occurred',
    });
    return;
  }

  // Handle unknown errors (500)
  const statusCode = (error as any).statusCode || 500;
  const message = process.env.NODE_ENV === 'production'
    ? 'Internal server error'
    : error.message;

  res.status(statusCode).json({
    success: false,
    message,
    ...(process.env.NODE_ENV !== 'production' && { stack: error.stack }),
  });
}

/**
 * Middleware to catch 404 errors
 */
export function notFoundHandler(req: Request, res: Response, _next: NextFunction): void {
  res.status(404).json({
    success: false,
    message: `Route not found: ${req.method} ${req.path}`,
  });
}

/**
 * Async handler wrapper to catch promise rejections
 */
export function asyncHandler(
  fn: (req: Request, res: Response, next: NextFunction) => Promise<unknown>
) {
  return (req: Request, res: Response, next: NextFunction) => {
    Promise.resolve(fn(req, res, next)).catch(next);
  };
}
