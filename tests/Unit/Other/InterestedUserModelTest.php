<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{InterestedUser, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InterestedUserModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $interested = InterestedUser::create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'email_verified_token' => 'token123',
            'pass_text' => 'encrypted_pass',
        ]);

        $this->assertEquals('test@example.com', $interested->email);
        $this->assertNotNull($interested->email_verified_at);
        $this->assertEquals('token123', $interested->email_verified_token);
        $this->assertEquals('encrypted_pass', $interested->pass_text);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $interested = new InterestedUser();

        $this->assertEquals('interested_user', $interested->getTable());
    }

    /** @test */
    public function it_belongs_to_user_by_email()
    {
        $user = $this->createParticipant();

        $interested = InterestedUser::create([
            'email' => $user->email,
            'email_verified_token' => 'token123',
        ]);

        $this->assertInstanceOf(User::class, $interested->user);
        $this->assertEquals($user->email, $interested->user->email);
    }

    /** @test */
    public function it_can_store_verification_token()
    {
        $interested = InterestedUser::create([
            'email' => 'newuser@example.com',
            'email_verified_token' => 'verification_token_xyz',
        ]);

        $this->assertEquals('verification_token_xyz', $interested->email_verified_token);
    }

    /** @test */
    public function it_can_mark_email_as_verified()
    {
        $interested = InterestedUser::create([
            'email' => 'verify@example.com',
            'email_verified_at' => null,
            'email_verified_token' => 'token123',
        ]);

        $interested->update(['email_verified_at' => now()]);

        $this->assertNotNull($interested->fresh()->email_verified_at);
    }
}
