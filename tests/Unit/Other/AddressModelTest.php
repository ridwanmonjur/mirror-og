<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Address, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $address = Address::create([
            'user_id' => $user->id,
            'addressLine1' => '123 Main St',
            'addressLine2' => 'Apt 4B',
            'city' => 'Kuala Lumpur',
            'country' => 'Malaysia',
        ]);

        $this->assertInstanceOf(User::class, $address->user);
        $this->assertEquals($user->id, $address->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $address = Address::create([
            'user_id' => $user->id,
            'addressLine1' => '456 Oak Ave',
            'addressLine2' => 'Suite 200',
            'city' => 'Penang',
            'country' => 'Malaysia',
        ]);

        $this->assertEquals($user->id, $address->user_id);
        $this->assertEquals('456 Oak Ave', $address->addressLine1);
        $this->assertEquals('Suite 200', $address->addressLine2);
        $this->assertEquals('Penang', $address->city);
        $this->assertEquals('Malaysia', $address->country);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $address = new Address();

        $this->assertFalse($address->timestamps);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $address = new Address();

        $this->assertEquals('user_address', $address->getTable());
    }

    /** @test */
    public function it_can_have_optional_address_line_2()
    {
        $user = $this->createParticipant();

        $address = Address::create([
            'user_id' => $user->id,
            'addressLine1' => '789 Pine St',
            'addressLine2' => null,
            'city' => 'Johor Bahru',
            'country' => 'Malaysia',
        ]);

        $this->assertNull($address->addressLine2);
    }
}
