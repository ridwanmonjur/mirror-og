<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{TransactionHistory, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TransactionHistoryModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $transaction = TransactionHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $transaction->user);
        $this->assertEquals($user->id, $transaction->user->id);
    }

    /** @test */
    public function it_stores_transaction_name()
    {
        $transaction = TransactionHistory::factory()->create([
            'name' => 'Event Entry Fee',
        ]);

        $this->assertEquals('Event Entry Fee', $transaction->name);
    }

    /** @test */
    public function it_stores_transaction_type()
    {
        $transaction = TransactionHistory::factory()->create([
            'type' => 'payment',
        ]);

        $this->assertEquals('payment', $transaction->type);
    }

    /** @test */
    public function it_stores_transaction_link()
    {
        $transaction = TransactionHistory::factory()->create([
            'link' => '/events/123',
        ]);

        $this->assertEquals('/events/123', $transaction->link);
    }

    /** @test */
    public function it_casts_amount_to_decimal()
    {
        $transaction = TransactionHistory::factory()->create([
            'amount' => 99.99,
        ]);

        $this->assertEquals('99.99', $transaction->amount);
        $this->assertIsString($transaction->amount);
    }

    /** @test */
    public function it_stores_transaction_summary()
    {
        $transaction = TransactionHistory::factory()->create([
            'summary' => 'Payment for tournament entry',
        ]);

        $this->assertEquals('Payment for tournament entry', $transaction->summary);
    }

    /** @test */
    public function it_casts_is_positive_to_boolean()
    {
        $transaction = TransactionHistory::factory()->create([
            'isPositive' => true,
        ]);

        $this->assertTrue($transaction->isPositive);
        $this->assertIsBool($transaction->isPositive);
    }

    /** @test */
    public function it_casts_date_to_datetime()
    {
        $date = now();
        $transaction = TransactionHistory::factory()->create([
            'date' => $date,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $transaction->date);
    }

    /** @test */
    public function it_has_formatted_date_accessor()
    {
        $transaction = TransactionHistory::factory()->create([
            'date' => '2025-05-21 20:05:00',
        ]);

        $formattedDate = $transaction->formatted_date;

        $this->assertMatchesRegularExpression('/\d+ \w+ \d{4}/', $formattedDate);
    }

    /** @test */
    public function it_has_formatted_time_accessor()
    {
        $transaction = TransactionHistory::factory()->create([
            'date' => '2025-05-21 20:05:00',
        ]);

        $formattedTime = $transaction->formatted_time;

        $this->assertMatchesRegularExpression('/\d+:\d+ (AM|PM)/', $formattedTime);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $transaction = new TransactionHistory();
        $this->assertNull($transaction->timestamps);
    }

    /** @test */
    public function it_has_appended_attributes()
    {
        $transaction = TransactionHistory::factory()->create();
        $array = $transaction->toArray();

        $this->assertArrayHasKey('formatted_date', $array);
        $this->assertArrayHasKey('formatted_time', $array);
    }

    /** @test */
    public function it_cursor_paginates_results()
    {
        $user = $this->createParticipant();

        TransactionHistory::factory()->count(20)->create(['user_id' => $user->id]);

        $query = TransactionHistory::where('user_id', $user->id);
        $result = $query->cursorPaginated(10);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('has_more', $result);
        $this->assertArrayHasKey('next_cursor', $result);
        $this->assertArrayHasKey('per_page', $result);
        $this->assertCount(10, $result['data']);
        $this->assertTrue($result['has_more']);
        $this->assertNotNull($result['next_cursor']);
    }

    /** @test */
    public function it_cursor_paginates_with_cursor()
    {
        $user = $this->createParticipant();

        $transactions = TransactionHistory::factory()->count(20)->create(['user_id' => $user->id]);
        $cursor = $transactions[10]->id;

        $query = TransactionHistory::where('user_id', $user->id);
        $result = $query->cursorPaginated(5, $cursor);

        $this->assertCount(5, $result['data']);
        $this->assertTrue($result['has_more']);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $transaction = TransactionHistory::factory()->create([
            'name' => 'Prize Payout',
            'type' => 'credit',
            'link' => '/events/456',
            'amount' => 500.00,
            'summary' => 'Won first place',
            'isPositive' => true,
            'date' => now(),
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Prize Payout', $transaction->name);
        $this->assertEquals('credit', $transaction->type);
        $this->assertEquals('/events/456', $transaction->link);
        $this->assertEquals('500.00', $transaction->amount);
        $this->assertEquals('Won first place', $transaction->summary);
        $this->assertTrue($transaction->isPositive);
        $this->assertEquals($user->id, $transaction->user_id);
    }

    /** @test */
    public function it_tracks_positive_and_negative_transactions()
    {
        $user = $this->createParticipant();

        $credit = TransactionHistory::factory()->create([
            'user_id' => $user->id,
            'isPositive' => true,
        ]);

        $debit = TransactionHistory::factory()->create([
            'user_id' => $user->id,
            'isPositive' => false,
        ]);

        $this->assertTrue($credit->isPositive);
        $this->assertFalse($debit->isPositive);
    }
}
