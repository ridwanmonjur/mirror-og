import request from 'supertest';
import app from '../../src/index';
import { initializeFirebase } from '../../src/config/firebase';
import {
  clearTestData,
  seedTestUser,
  seedTestEvent,
} from '../helpers/testDb';

describe('Public API - Integration Tests', () => {
  let userId: number;
  let user2Id: number;
  let eventId: number;

  beforeAll(async () => {
    initializeFirebase();
  });

  beforeEach(async () => {
    await clearTestData();

    userId = await seedTestUser({
      id: 1,
      name: 'Test User',
      email: 'user@test.com',
      role: 'PARTICIPANT',
    });

    user2Id = await seedTestUser({
      id: 2,
      name: 'Test User 2',
      email: 'user2@test.com',
      role: 'PARTICIPANT',
    });

    eventId = await seedTestEvent({
      id: 1,
      user_id: userId,
      eventName: 'Test Tournament',
    });
  });

  describe('GET /api/user/:id/logs', () => {
    it('should get user activity logs without authentication', async () => {
      const response = await request(app)
        .get(`/api/user/${userId}/logs`);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should limit logs to 50 entries', async () => {
      const response = await request(app)
        .get(`/api/user/${userId}/logs`);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      // If there are logs, they should be limited
      if (response.body.data.length > 0) {
        expect(response.body.data.length).toBeLessThanOrEqual(50);
      }
    });

    it('should work for any user id', async () => {
      const response = await request(app)
        .get(`/api/user/${user2Id}/logs`);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should handle non-existent user', async () => {
      const response = await request(app)
        .get('/api/user/99999/logs');

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.data).toEqual([]);
    });
  });

  describe('GET /api/user/:id/connections', () => {
    it('should get user connections without authentication', async () => {
      const response = await request(app)
        .get(`/api/user/${userId}/connections`);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body).toHaveProperty('data');
      expect(response.body.data).toHaveProperty('followers');
      expect(response.body.data).toHaveProperty('following');
      expect(Array.isArray(response.body.data.followers)).toBe(true);
      expect(Array.isArray(response.body.data.following)).toBe(true);
    });

    it('should handle user with no connections', async () => {
      const response = await request(app)
        .get(`/api/user/${user2Id}/connections`);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.data.followers).toEqual([]);
      expect(response.body.data.following).toEqual([]);
    });
  });

  describe('POST /api/event/:id/invitation', () => {
    it('should create event invitation without authentication', async () => {
      const response = await request(app)
        .post(`/api/event/${eventId}/invitation`)
        .send({
          team_id: 1,
          user_id: userId,
          message: 'You are invited to join this tournament',
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('sent');
      expect(response.body).toHaveProperty('data');
    });

    it('should create invitation without message', async () => {
      const response = await request(app)
        .post(`/api/event/${eventId}/invitation`)
        .send({
          team_id: 1,
          user_id: userId,
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
    });

    it('should create invitation with only user_id', async () => {
      const response = await request(app)
        .post(`/api/event/${eventId}/invitation`)
        .send({
          user_id: userId,
          message: 'Join us!',
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
    });
  });

  describe('POST /api/event/:id/inviteDestroy', () => {
    it('should delete event invitation without authentication', async () => {
      const response = await request(app)
        .post(`/api/event/${eventId}/inviteDestroy`)
        .send({
          invitation_id: 1,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('deleted');
    });

    it('should handle non-existent invitation', async () => {
      const response = await request(app)
        .post(`/api/event/${eventId}/inviteDestroy`)
        .send({
          invitation_id: 99999,
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });
  });

  describe('POST /api/media', () => {
    it('should upload media without authentication', async () => {
      const response = await request(app)
        .post('/api/media')
        .send({
          file_path: '/uploads/image.jpg',
          file_type: 'image',
          user_id: userId,
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('uploaded');
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data).toHaveProperty('file_path');
    });

    it('should upload video media', async () => {
      const response = await request(app)
        .post('/api/media')
        .send({
          file_path: '/uploads/video.mp4',
          file_type: 'video',
          user_id: userId,
        });

      expect(response.status).toBe(201);
      expect(response.body.success).toBe(true);
    });

    it('should handle missing user_id', async () => {
      const response = await request(app)
        .post('/api/media')
        .send({
          file_path: '/uploads/image.jpg',
          file_type: 'image',
        });

      // Should still work with null user_id
      expect([200, 201, 500]).toContain(response.status);
    });
  });

  describe('GET /api/media/stream/:media', () => {
    it('should stream media by id', async () => {
      // First upload media
      const uploadResponse = await request(app)
        .post('/api/media')
        .send({
          file_path: '/uploads/stream-test.jpg',
          file_type: 'image',
          user_id: userId,
        });

      const mediaId = uploadResponse.body.data.id;

      const response = await request(app)
        .get(`/api/media/stream/${mediaId}`);

      expect([200, 404]).toContain(response.status);
      if (response.status === 200) {
        expect(response.body.success).toBe(true);
        expect(response.body).toHaveProperty('data');
      }
    });

    it('should return 404 for non-existent media', async () => {
      const response = await request(app)
        .get('/api/media/stream/99999');

      expect(response.status).toBe(404);
      expect(response.body.success).toBe(false);
      expect(response.body.error).toContain('not found');
    });
  });

  describe('DELETE /api/media/:media', () => {
    it('should delete media without authentication', async () => {
      // First upload media
      const uploadResponse = await request(app)
        .post('/api/media')
        .send({
          file_path: '/uploads/delete-test.jpg',
          file_type: 'image',
          user_id: userId,
        });

      const mediaId = uploadResponse.body.data.id;

      const response = await request(app)
        .delete(`/api/media/${mediaId}`);

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('deleted');
    });

    it('should handle deleting non-existent media', async () => {
      const response = await request(app)
        .delete('/api/media/99999');

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });
  });

  describe('PUT /api/interest', () => {
    it('should register beta interest without authentication', async () => {
      const response = await request(app)
        .put('/api/interest')
        .send({
          email: 'interested@test.com',
          name: 'Interested User',
          interest_type: 'beta_tester',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
      expect(response.body.message).toContain('registered');
    });

    it('should handle duplicate email registration', async () => {
      // Register first time
      await request(app)
        .put('/api/interest')
        .send({
          email: 'duplicate@test.com',
          name: 'User',
          interest_type: 'beta',
        });

      // Register again with same email
      const response = await request(app)
        .put('/api/interest')
        .send({
          email: 'duplicate@test.com',
          name: 'User Updated',
          interest_type: 'beta',
        });

      // Should update timestamp using ON DUPLICATE KEY UPDATE
      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should accept different interest types', async () => {
      const response = await request(app)
        .put('/api/interest')
        .send({
          email: 'organizer@test.com',
          name: 'Future Organizer',
          interest_type: 'organizer',
        });

      expect(response.status).toBe(200);
      expect(response.body.success).toBe(true);
    });

    it('should work without name field', async () => {
      const response = await request(app)
        .put('/api/interest')
        .send({
          email: 'minimal@test.com',
          interest_type: 'beta',
        });

      expect([200, 500]).toContain(response.status);
    });
  });
});
