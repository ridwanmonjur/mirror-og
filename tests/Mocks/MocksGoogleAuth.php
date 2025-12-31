<?php

namespace Tests\Mocks;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

trait MocksGoogleAuth
{
    /**
     * Mock Google OAuth authentication
     */
    protected function mockGoogleAuth($email, $name, $googleId = 'google_123456789')
    {
        $user = Mockery::mock(SocialiteUser::class);
        $user->shouldReceive('getEmail')->andReturn($email);
        $user->shouldReceive('getName')->andReturn($name);
        $user->shouldReceive('getId')->andReturn($googleId);
        $user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        // Set properties
        $user->email = $email;
        $user->name = $name;
        $user->id = $googleId;

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($user);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('redirect')->andReturn(redirect('http://localhost/auth/google'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        return $user;
    }

    /**
     * Mock Google OAuth redirect
     */
    protected function mockGoogleRedirect()
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirect')
            ->andReturn(redirect('https://accounts.google.com/oauth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        return $provider;
    }

    /**
     * Cleanup Mockery
     */
    protected function tearDownGoogleAuth()
    {
        if (class_exists(Mockery::class)) {
            Mockery::close();
        }
    }
}
