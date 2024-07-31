<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class CreateEventCest
{
    public function _before(AcceptanceTester $I) {}

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/organizer/signin');
        $I->fillField('email', 'ridwanmonjur@gmail.com');
        $I->fillField('password', '123456');
        $I->click('Sign in');
        $I->see('Create an event');
    }
}
