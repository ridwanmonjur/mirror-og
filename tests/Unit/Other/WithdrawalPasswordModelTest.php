<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\WithdrawalPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WithdrawalPasswordModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $password = WithdrawalPassword::create([
            'password' => 'secure_password_123',
        ]);

        $this->assertEquals('secure_password_123', $password->password);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $password = new WithdrawalPassword();

        $this->assertNull($password->timestamps);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $password = new WithdrawalPassword();

        $this->assertEquals('csv_passwords', $password->getTable());
    }

    /** @test */
    public function it_can_create_password()
    {
        $password = WithdrawalPassword::create([
            'password' => 'test_password',
        ]);

        $this->assertDatabaseHas('csv_passwords', [
            'password' => 'test_password',
        ]);
    }

    /** @test */
    public function it_can_update_password()
    {
        $password = WithdrawalPassword::create([
            'password' => 'old_password',
        ]);

        $password->update(['password' => 'new_password']);

        $this->assertEquals('new_password', $password->fresh()->password);
    }
}
