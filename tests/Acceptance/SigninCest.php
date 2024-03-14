<?php


namespace Tests\Acceptance;

use Illuminate\Support\Facades\Auth;
use Tests\Support\AcceptanceTester;

class SigninCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTestOrganizerLogin(AcceptanceTester $I)
    {
        Auth::forgetUser();
        $I->amOnPage('http://localhost:8000/organizer/signin');
        $I->fillField('email','ridwanmonjur@gmail.com');
        $I->fillField('password','123456');
        $I->click('Sign in');
        $I->see('Create an event');
    }

    public function tryToTestParticipantLogin(AcceptanceTester $I)
    {
        Auth::forgetUser();
        $I->amOnPage('http://localhost:8000/participant/signin');
        $I->fillField('email','participant1@gmail.com');
        $I->fillField('password','123456');
        $I->click('Sign in');
        $I->see("What's happening?");
    }

    // public function tryToTestAdminLogin(AcceptanceTester $I)
    // {
    //     $I->amOnPage('http://localhost:8000/admin/login');
    //     $I->fillField('email','participant1@gmail.com');
    //     $I->fillField('password','123456');
    //     $I->click('Sign in');
    //     $I->see("What's happening?");
    // }
}
