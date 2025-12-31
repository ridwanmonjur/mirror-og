<?php

namespace Tests\Mocks;

use Illuminate\Support\Facades\Mail;

trait MocksEmail
{
    /**
     * Fake all mail sending
     */
    protected function mockMailSending()
    {
        Mail::fake();
    }

    /**
     * Assert a specific mailable was sent
     */
    protected function assertMailSent($mailable, $callback = null)
    {
        Mail::assertSent($mailable, $callback);
    }

    /**
     * Assert a specific mailable was queued
     */
    protected function assertMailQueued($mailable, $callback = null)
    {
        Mail::assertQueued($mailable, $callback);
    }

    /**
     * Assert a specific mailable was NOT sent
     */
    protected function assertMailNotSent($mailable)
    {
        Mail::assertNotSent($mailable);
    }

    /**
     * Assert NO mail was sent
     */
    protected function assertNoMailSent()
    {
        Mail::assertNothingSent();
    }

    /**
     * Assert no mail was queued
     */
    protected function assertNoMailQueued()
    {
        Mail::assertNothingQueued();
    }

    /**
     * Assert mailable sent to specific recipient
     */
    protected function assertMailSentTo($recipient, $mailable)
    {
        Mail::assertSent($mailable, function ($mail) use ($recipient) {
            return $mail->hasTo($recipient);
        });
    }

    /**
     * Get sent mail count
     */
    protected function getSentMailCount($mailable)
    {
        return Mail::sent($mailable)->count();
    }
}
