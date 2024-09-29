<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organizer\OrganizerCheckoutController;
use App\Http\Controllers\Organizer\OrganizerController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerEventResultsController;
use App\Http\Controllers\Organizer\OrganizerInvitationController;
use App\Http\Controllers\Participant\ParticipantCheckoutController;
use App\Http\Controllers\Participant\ParticipantController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\Participant\ParticipantRosterController;
use App\Http\Controllers\Participant\ParticipantTeamController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/* THIS IS THE UNSIGNED VIEW */
// Home
Route::get('/', [AuthController::class, 'showLandingPage'])->name('landing.view');
Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');

// Forget, reset password
Route::get('/forget-password', [AuthController::class, 'createForget'])->name('user.forget.view');
Route::get('/reset-password/{token}', [AuthController::class, 'createReset'])->name('user.reset.view');
Route::post('/reset-password', [AuthController::class, 'storeReset'])->name('user.reset.action');
Route::post('/forget-password', [AuthController::class, 'storeForget'])->name('user.forget.action');

// Verify
Route::get('/account/verify-resend/{email}', [AuthController::class, 'verifyResend'])->name('user.verify.resend');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify.action');
Route::get('/account/verify-success/', [AuthController::class, 'verifySuccess'])->name('user.verify.success');

// Countries and games
Route::get('/countries', [AuthController::class, 'countryList'])->name('country.view');
Route::get('/games', [AuthController::class, 'gameList'])->name('game.view');

// Logout
Route::get('logout', [AuthController::class, 'logoutAction'])->name('logout.action');
Route::post('/logout', [AuthController::class, 'logout'])->name('participant.logout.action');

// Search bar
Route::get('/event/search', [AuthController::class, 'showLandingPage'])->name('public.search.view');
Route::get('/event/{id}', [ParticipantEventController::class, 'ViewEvent'])->name('public.event.view');
Route::get('/view/team/{id}', [ParticipantTeamController::class, 'teamManagement'])->name('public.team.view');
Route::get('/view/participant/{id}', [ParticipantController::class, 'viewProfileById'])->name('public.participant.view')
    ->middleware('prevent-back-history');
Route::get('/view/organizer/{id}', [OrganizerController::class, 'viewProfileById'])->name('public.organizer.view')
    ->middleware('prevent-back-history');

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [OrganizerController::class, 'viewOwnProfile'])->name('admin.profile.view')
            ->middleware('prevent-back-history');
    });
});

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/auth/steam/callback', [AuthController::class, 'handleSteamCallback']);

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'check-permission:participant|admin|organizer'], function () {
        Route::get('user/{id}/stats', [UserController::class, 'showStats'])->name('user.stats');
        Route::post('user/{id}/background', [UserController::class, 'replaceBackground'])->name('user.userBackground.action');
        Route::get('profile/message', [ChatController::class, 'message'])->name('user.message.view');
    });
});

/* THIS IS THE PARTICIPANT VIEW */
Route::group(['prefix' => 'participant'], function () {
    // Normal login
    Route::get('/signin', [AuthController::class, 'signIn'])->name('participant.signin.view');
    Route::get('/signup', [AuthController::class, 'signUp'])->name('participant.signup.view');
    Route::post('/signin', [AuthController::class, 'accessUser'])->name('participant.signin.action');
    Route::post('/signup', [AuthController::class, 'storeUser'])->name('participant.signup.action');

    // Social login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('participant.google.login');
    Route::get('/auth/steam', [AuthController::class, 'redirectToSteam'])->name('participant.steam.login');

    // General participant functions
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => 'check-permission:participant|admin'], function () {
            // Home page
            Route::get('/home', [ParticipantEventController::class, 'home'])->name('participant.home.view');

            // Request page
            Route::get('/request', [ParticipantController::class, 'viewRequest'])->name('participant.request.view');

            // Friends
            Route::post('/friends', [ParticipantController::class, 'updateFriend'])->name('participant.friends.update');
            Route::post('/follow', [ParticipantController::class, 'followParticipant'])->name('participant.participant.follow');

            // Team management
            Route::get('/team/list', [ParticipantTeamController::class, 'teamList'])
                ->middleware('prevent-back-history')
                ->name('participant.team.view');
            Route::get('/team/create', [ParticipantTeamController::class, 'createTeamView'])->name('participant.team.create');
            Route::get('/team/{id}/edit', [ParticipantTeamController::class, 'editTeamView'])->name('participant.team.edit');
            Route::get('/team/confirm', [ParticipantEventController::class, 'confirmUpdate']);
            Route::get('/team/{id}/manage/member', [ParticipantTeamController::class, 'teamMemberManagement'])->name('participant.member.manage');
            Route::get('/team/{id}/manage', [ParticipantTeamController::class, 'teamManagement'])->name('participant.team.manage');
            Route::get('/team/{id}/register', [ParticipantEventController::class, 'registrationManagement'])->name('participant.register.manage');

            Route::post('/team/roster/approve', [ParticipantRosterController::class, 'approveRosterMember'])->name('participant.roster.approve');
            Route::post('/team/roster/disapprove', [ParticipantRosterController::class, 'disapproveRosterMember'])->name('participant.roster.disapprove');
            Route::post('/team/roster/captain', [ParticipantRosterController::class, 'captainRosterMember'])->name('participant.roster.captain');
            Route::post('/team/roster/deleteCaptain', [ParticipantRosterController::class, 'deleteCaptainRosterMember'])->name('participant.roster.deleteCaptain');
            Route::post('/team/create', [ParticipantTeamController::class, 'teamStore'])->name('participant.team.store');
            Route::post('/team/{id}/editStore', [ParticipantTeamController::class, 'teamEditStore'])->name('participant.team.editStore');
            Route::post('/team/{id}/banner', [ParticipantTeamController::class, 'replaceBanner'])->name('participant.teamBanner.action');
            Route::post('/team/member/{id}/pending', [ParticipantTeamController::class, 'pendingTeamMember'])->name('participant.member.pending');

            // Event management
            Route::get('/event/{id}/team/{teamId}/manage/roster', [ParticipantTeamController::class, 'rosterMemberManagement'])
                ->middleware('prevent-back-history')->name('participant.roster.manage');
            Route::post('/event/{id}/team/{teamId}/manage/roster', [ParticipantEventController::class, 'showSuccess'])
                ->middleware('prevent-back-history')->name('participant.event.success');
            Route::post('/event/member', [ParticipantTeamController::class, 'teamMemberManagementRedirected'])->name('participant.memberManage.action');
            Route::get('/event/{id}', [ParticipantEventController::class, 'viewEvent'])->name('participant.event.view');
            Route::post('/event/{id}/join/team/select', [ParticipantEventController::class, 'selectTeamToJoinEvent'])->name('participant.selectTeamToJoin.action');
            Route::post('/event/{id}/join/team/create', [ParticipantEventController::class, 'createTeamToJoinEvent'])->name('participant.createTeamToJoinEvent.action');
            Route::post('/event/{id}/join/redirect/selectOrCreateTeamToJoinEvent', [ParticipantEventController::class, 'redirectToSelectOrCreateTeamToJoinEvent'])->middleware('prevent-back-history')
                ->name('participant.event.selectOrCreateTeam.redirect');
            Route::post('/event/{id}/join/redirect/createTeamToJoinEvent', [ParticipantEventController::class, 'redirectToCreateTeamToJoinEvent'])
                ->name('participant.event.createTeam.redirect');
            Route::get('event/checkout/transition', [ParticipantCheckoutController::class, 'showCheckoutTransition'])->name('participant.checkout.transition');
            Route::post('event/checkout', [ParticipantCheckoutController::class, 'showCheckout'])->name('participant.checkout.action')
                ->middleware('prevent-back-history');
            Route::post('event/confirmOrCancel', [ParticipantEventController::class, 'confirmOrCancel'])->name('participant.confirmOrCancel.action');

            // Profile
            Route::get('/profile', [ParticipantController::class, 'viewOwnProfile'])->name('participant.profile.view')
                ->middleware('prevent-back-history');
        });
    });
});

/* THIS IS THE ORGANIZER VIEW */
Route::group(['prefix' => 'organizer'], function () {
    // Normal login
    Route::get('/signin', [AuthController::class, 'organizerSignin'])->name('organizer.signin.view');
    Route::get('/signup', [AuthController::class, 'organizerSignup'])->name('organizer.signup.view');
    Route::post('/signin', [AuthController::class, 'accessUser'])->name('organizer.signin.action');
    Route::post('/signup', [AuthController::class, 'storeUser'])->name('organizer.signup.action');

    // Social login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('organizer.google.login');
    Route::get('/auth/steam', [AuthController::class, 'redirectToSteam'])->name('organizer.steam.login');

    // General organizer functions
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => ['check-permission:organizer|admin', 'prevent-back-history']], function () {
            // Organizer home
            Route::get('/home', [OrganizerEventController::class, 'home'])->name('organizer.home.view');
            Route::get('/event/{id}/results', [OrganizerEventResultsController::class, 'index'])->name('event.awards.index');
            Route::get('/event/{id}/matches', [OrganizerEventResultsController::class, 'viewMatches'])->name('event.matches.index');

            // Event manage
            Route::resource('/event', OrganizerEventController::class, [
                'index' => 'event.index',
                'create' => 'event.create',
                'store' => 'event.store',
                'show' => 'event.show',
                'edit' => 'event.edit',
                'update' => 'event.update',
            ]);

            // Invite page
            Route::get('/event/{id}/invitation', [OrganizerInvitationController::class, 'index'])->name('event.invitation.index');
            Route::post('event/{id}/updateForm', [OrganizerEventController::class, 'updateForm'])->name('event.updateForm');
            // Success page
            Route::get('event/{id}/success', [OrganizerEventController::class, 'showSuccess'])->middleware('prevent-back-button')
                ->name('organizer.success.view');
            // Live page
            Route::get('event/{id}/live', [OrganizerEventController::class, 'showLive'])->middleware('prevent-back-button')
                ->name('organizer.live.view');
            // Checkout page
            Route::get('event/{id}/checkout', [OrganizerCheckoutController::class, 'showCheckout'])->middleware('prevent-back-button')
                ->name('organizer.checkout.view');
            Route::get('event/{id}/checkout/transition', [OrganizerCheckoutController::class, 'showCheckoutTransition'])->name('organizer.checkout.transition');

            Route::get('/profile', [OrganizerController::class, 'viewOwnProfile'])->name('organizer.profile.view')
                ->middleware('prevent-back-history');
        });
    });
});
