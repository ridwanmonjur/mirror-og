<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\{Mail, DB, Hash};
use App\Mail\{ResetPasswordMail, VerifyUserMail};
use Carbon\Carbon;

class AuthResetAndVerifyControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function it_displays_reset_password_form()
    {
        $response = $this->get('/reset-password?token=test-token');

        $response->assertStatus(200);
        $response->assertViewIs('Auth.ResetPassword');
        $response->assertViewHas('token', 'test-token');
    }

    /** @test */
    public function it_validates_required_fields_on_password_reset()
    {
        $response = $this->post('/store-reset', []);

        $response->assertSessionHasErrors(['token', 'password', 'confirmPassword']);
    }

    /** @test */
    public function it_validates_password_minimum_length()
    {
        $response = $this->post('/store-reset', [
            'token' => 'test-token',
            'password' => '12345',
            'confirmPassword' => '12345',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_validates_password_confirmation_match()
    {
        $user = User::factory()->create();
        $token = 'valid-token';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->post('/store-reset', [
            'token' => $token,
            'password' => 'newpassword123',
            'confirmPassword' => 'different',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Password confirmation does not match.');
    }

    /** @test */
    public function it_resets_password_with_valid_token()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $token = 'valid-reset-token';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->post('/store-reset', [
            'token' => $token,
            'password' => 'newpassword123',
            'confirmPassword' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('Auth.ResetSuccess');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));

        // Token should be deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_rejects_invalid_reset_token()
    {
        $response = $this->post('/store-reset', [
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'confirmPassword' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid token or email address.');
    }

    /** @test */
    public function it_rejects_expired_reset_token()
    {
        $user = User::factory()->create();
        $token = 'expired-token';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'expires_at' => now()->subHour(), // Expired
        ]);

        $response = $this->post('/store-reset', [
            'token' => $token,
            'password' => 'newpassword123',
            'confirmPassword' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Token has expired. Please request a new password reset.');
    }

    /** @test */
    public function it_sends_password_reset_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response = $this->post('/store-forget', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Password reset link sent. Please check your email.');

        // Check token was created
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);

        Mail::assertQueued(ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo('test@example.com');
        });
    }

    /** @test */
    public function it_rejects_password_reset_for_nonexistent_email()
    {
        $response = $this->post('/store-forget', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'User not found with this email.');

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_validates_email_format_on_forgot_password()
    {
        $response = $this->post('/store-forget', [
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_updates_existing_reset_token()
    {
        $user = User::factory()->create();

        // Create initial token
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => 'old-token',
            'expires_at' => now()->addHour(),
        ]);

        // Request new token
        $this->post('/store-forget', ['email' => $user->email]);

        // Should still have only one token for this email
        $tokens = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->get();

        $this->assertCount(1, $tokens);
        $this->assertNotEquals('old-token', $tokens->first()->token);
    }

    /** @test */
    public function it_verifies_account_with_valid_token()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'email_verified_token' => 'valid-verify-token',
        ]);

        $response = $this->get('/verify-account/valid-verify-token');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verified_token);
    }

    /** @test */
    public function it_rejects_invalid_verification_token()
    {
        $response = $this->get('/verify-account/invalid-token');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_handles_already_verified_account()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'email_verified_token' => 'some-token',
        ]);

        $response = $this->get('/verify-account/some-token');

        $response->assertRedirect();
        // Should still succeed even if already verified
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_resends_verification_email()
    {
        $user = User::factory()->create([
            'email' => 'resend@example.com',
            'email_verified_at' => null,
            'email_verified_token' => 'existing-token',
        ]);

        $response = $this->get('/verify-resend/resend@example.com');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertQueued(VerifyUserMail::class, function ($mail) {
            return $mail->hasTo('resend@example.com');
        });
    }

    /** @test */
    public function it_rejects_resend_for_nonexistent_email()
    {
        $response = $this->get('/verify-resend/nonexistent@example.com');

        $response->assertRedirect();
        $response->assertSessionHas('error');

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_rejects_resend_for_already_verified_email()
    {
        $user = User::factory()->create([
            'email' => 'verified@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->get('/verify-resend/verified@example.com');

        $response->assertRedirect();
        $response->assertSessionHas('error');

        Mail::assertNothingQueued();
    }

    /** @test */
    public function reset_token_expires_after_one_day()
    {
        $user = User::factory()->create();

        $this->post('/store-forget', ['email' => $user->email]);

        $token = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        $expiresAt = Carbon::parse($token->expires_at);
        $expectedExpiry = now()->addDay();

        $this->assertTrue($expiresAt->diffInMinutes($expectedExpiry) < 1);
    }

    /** @test */
    public function it_generates_unique_reset_tokens()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $this->post('/store-forget', ['email' => 'user1@example.com']);
        $this->post('/store-forget', ['email' => 'user2@example.com']);

        $token1 = DB::table('password_reset_tokens')
            ->where('email', 'user1@example.com')
            ->value('token');

        $token2 = DB::table('password_reset_tokens')
            ->where('email', 'user2@example.com')
            ->value('token');

        $this->assertNotEquals($token1, $token2);
    }
}
