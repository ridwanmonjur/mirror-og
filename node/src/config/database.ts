import mysql from 'mysql2/promise';
import { Logger } from '../utils/logger';

// Database configuration from environment variables
const dbConfig = {
  host: process.env.DB_HOST || '127.0.0.1',
  port: parseInt(process.env.DB_PORT || '3306', 10),
  user: process.env.DB_USERNAME || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_DATABASE || 'driftwood',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  enableKeepAlive: true,
  keepAliveInitialDelay: 0,
};

// Create connection pool
const pool = mysql.createPool(dbConfig);

// Test connection on startup
export async function testDatabaseConnection(): Promise<void> {
  try {
    const connection = await pool.getConnection();
    Logger.log('Database connection established successfully');
    connection.release();
  } catch (error) {
    Logger.error('Failed to connect to database', error);
    throw error;
  }
}

// Get connection from pool
export function getConnection() {
  return pool.getConnection();
}

// Query helper
export async function query<T = any>(sql: string, params?: any[]): Promise<T> {
  try {
    const [rows] = await pool.execute(sql, params);
    return rows as T;
  } catch (error) {
    Logger.error('Database query error', error);
    Logger.error(`SQL: ${sql}`, { params });
    throw error;
  }
}

// Export pool for advanced usage
export default pool;
