<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Participant;
use App\Models\Organizer;
use App\Models\NotificationCounter;
use App\Models\Wallet;

trait CreatesTestUsers
{
    /**
     * Create a participant user with all related records
     */
    protected function createParticipant(array $attributes = [])
    {
        $user = User::factory()->create(array_merge([
            'role' => 'PARTICIPANT',
            'email_verified_at' => now(),
        ], $attributes));

        // Create participant profile
        Participant::factory()->create(['user_id' => $user->id]);

        // Create notification counter
        NotificationCounter::factory()->create(['user_id' => $user->id]);

        // Create wallet with default balance
        Wallet::factory()->create([
            'user_id' => $user->id,
            'usable_balance' => 0.00,
            'total_balance' => 0.00,
        ]);

        return $user->fresh(['participant', 'wallet']);
    }

    /**
     * Create an organizer user with all related records
     */
    protected function createOrganizer(array $attributes = [])
    {
        $user = User::factory()->create(array_merge([
            'role' => 'ORGANIZER',
            'email_verified_at' => now(),
        ], $attributes));

        // Create organizer profile
        Organizer::factory()->create(['user_id' => $user->id]);

        // Create notification counter
        NotificationCounter::factory()->create(['user_id' => $user->id]);

        // Create wallet
        Wallet::factory()->create([
            'user_id' => $user->id,
            'usable_balance' => 0.00,
            'total_balance' => 0.00,
        ]);

        return $user->fresh(['organizer', 'wallet']);
    }

    /**
     * Create an admin user
     */
    protected function createAdmin(array $attributes = [])
    {
        return User::factory()->create(array_merge([
            'role' => 'ADMIN',
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * Create an unverified user (email not verified)
     */
    protected function createUnverifiedUser($role = 'PARTICIPANT', array $attributes = [])
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'email_verified_at' => null,
        ], $attributes));
    }

    /**
     * Create a participant with wallet balance
     */
    protected function createParticipantWithBalance($balance = 100.00, array $attributes = [])
    {
        $user = $this->createParticipant($attributes);

        $user->wallet->update([
            'usable_balance' => $balance,
            'total_balance' => $balance,
        ]);

        return $user->fresh(['wallet']);
    }

    /**
     * Create an organizer with wallet balance
     */
    protected function createOrganizerWithBalance($balance = 100.00, array $attributes = [])
    {
        $user = $this->createOrganizer($attributes);

        $user->wallet->update([
            'usable_balance' => $balance,
            'total_balance' => $balance,
        ]);

        return $user->fresh(['wallet']);
    }

    /**
     * Create multiple participants at once
     */
    protected function createParticipants($count = 3, array $attributes = [])
    {
        $participants = [];

        for ($i = 0; $i < $count; $i++) {
            $participants[] = $this->createParticipant($attributes);
        }

        return collect($participants);
    }

    /**
     * Create multiple organizers at once
     */
    protected function createOrganizers($count = 3, array $attributes = [])
    {
        $organizers = [];

        for ($i = 0; $i < $count; $i++) {
            $organizers[] = $this->createOrganizer($attributes);
        }

        return collect($organizers);
    }
}
