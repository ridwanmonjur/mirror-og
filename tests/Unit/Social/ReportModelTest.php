<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Report, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_reporter()
    {
        $reporter = $this->createParticipant();
        $reported = $this->createParticipant();

        $report = Report::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported->id,
            'reason' => 'spam',
            'description' => 'User is spamming',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(User::class, $report->reporter);
        $this->assertEquals($reporter->id, $report->reporter->id);
    }

    /** @test */
    public function it_belongs_to_reported_user()
    {
        $reporter = $this->createParticipant();
        $reported = $this->createParticipant();

        $report = Report::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported->id,
            'reason' => 'spam',
            'description' => 'User is spamming',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(User::class, $report->reportedUser);
        $this->assertEquals($reported->id, $report->reportedUser->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $reporter = $this->createParticipant();
        $reported = $this->createParticipant();

        $report = Report::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported->id,
            'reason' => 'harassment',
            'description' => 'User is harassing others',
            'status' => 'pending',
            'admin_notes' => 'Investigating',
        ]);

        $this->assertEquals($reporter->id, $report->reporter_id);
        $this->assertEquals($reported->id, $report->reported_user_id);
        $this->assertEquals('harassment', $report->reason);
        $this->assertEquals('User is harassing others', $report->description);
        $this->assertEquals('pending', $report->status);
        $this->assertEquals('Investigating', $report->admin_notes);
    }

    /** @test */
    public function it_can_update_status()
    {
        $reporter = $this->createParticipant();
        $reported = $this->createParticipant();

        $report = Report::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported->id,
            'reason' => 'spam',
            'description' => 'Spamming',
            'status' => 'pending',
        ]);

        $report->update(['status' => 'resolved']);

        $this->assertEquals('resolved', $report->fresh()->status);
    }

    /** @test */
    public function it_can_add_admin_notes()
    {
        $reporter = $this->createParticipant();
        $reported = $this->createParticipant();

        $report = Report::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported->id,
            'reason' => 'spam',
            'description' => 'Spamming',
            'status' => 'pending',
        ]);

        $report->update(['admin_notes' => 'User has been warned']);

        $this->assertEquals('User has been warned', $report->fresh()->admin_notes);
    }
}
