import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';

describe('Auth API - Integration Tests', () => {
  beforeAll(async () => {
    // Initialize Firebase (will use emulator from env vars)
    initializeFirebase();
  });

  describe('POST /auth/token', () => {
    it('should create authentication token with valid uid', async () => {
      const response = await request(app)
        .post('/auth/token')
        .send({
          uid: '123',
          role: 'PARTICIPANT',
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('token');
      expect(response.body).toHaveProperty('jwt_token');
      expect(response.body).toHaveProperty('expires_at');
    });

    it('should create token with default role when not provided', async () => {
      const response = await request(app)
        .post('/auth/token')
        .send({
          uid: '456',
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('token');
      expect(response.body).toHaveProperty('jwt_token');
    });

    it('should create token with teamId when provided', async () => {
      const response = await request(app)
        .post('/auth/token')
        .send({
          uid: '789',
          role: 'PARTICIPANT',
          teamId: 100,
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('token');
      expect(response.body).toHaveProperty('jwt_token');
    });

    it('should reject request without uid', async () => {
      const response = await request(app)
        .post('/auth/token')
        .send({
          role: 'PARTICIPANT',
        });

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('detail');
      expect(response.body.detail).toContain('uid is required');
    });

    it('should reject request with empty uid', async () => {
      const response = await request(app)
        .post('/auth/token')
        .send({
          uid: '',
          role: 'PARTICIPANT',
        });

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('detail');
    });

    it('should accept different role types', async () => {
      const roles = ['PARTICIPANT', 'ORGANIZER', 'ADMIN'];

      for (const role of roles) {
        const response = await request(app)
          .post('/auth/token')
          .send({
            uid: `user_${role}`,
            role,
          });

        expect(response.status).toBe(200);
        expect(response.body).toHaveProperty('token');
      }
    });
  });

  describe('GET /health', () => {
    it('should return healthy status', async () => {
      const response = await request(app).get('/health');

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('status', 'healthy');
      expect(response.body).toHaveProperty('service', 'driftwood-client-auth');
      expect(response.body).toHaveProperty('timestamp');
    });

    it('should include environment information', async () => {
      const response = await request(app).get('/health');

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('environment');
    });
  });
});
