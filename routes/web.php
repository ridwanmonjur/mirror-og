<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthResetAndVerifyController;
use App\Http\Controllers\Open\BetaController;
use App\Http\Controllers\Open\MiscController;
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
use App\Http\Controllers\Shared\FirebaseController;
use App\Http\Controllers\Shared\ImageVideoController;
use App\Http\Controllers\Shared\SocialController;
use App\Http\Controllers\Shared\StripeController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/* THIS IS THE UNSIGNED VIEW */
// Home

Route::get('/', [MiscController::class, 'showLandingPage'])->name('public.landing.view');
// Route::view('/closedbeta', 'Public.ClosedBeta')->name('public.closedBeta.view');
Route::view('/about', 'Public.About')->name('public.about.view');
Route::view('/contact', 'Public.Contact')->name('public.contact.view');

// Forget, reset password
Route::view('/forget-password', view: 'Auth.ForgetPassword')->name('user.forget.view');
Route::get('/reset-password/{token}', [AuthResetAndVerifyController::class, 'createReset'])->name('user.reset.view');
Route::post('/reset-password', [AuthResetAndVerifyController::class, 'storeReset'])->name('user.reset.action');
Route::post('/forget-password', [AuthResetAndVerifyController::class, 'storeForget'])->name('user.forget.action');

// Verify
Route::get('/account/verify-resend/{email}', [AuthResetAndVerifyController::class, 'verifyResend'])->name('user.verify.resend');
Route::get('/account/verify/{token}/mail/{newEmail}', [UserController::class, 'changeEmail'])->name('user.changeEmail.action');
Route::get('/account/verify/{token}', [AuthResetAndVerifyController::class, 'verifyAccount'])->name('user.verify.action');
Route::view('/account/verify-success/', 'Auth.VerifySuccess')->name('user.verify.success');

Route::get('/interestedUser/verify/{token}', [BetaController::class, 'verifyInterestedUser'])->name('interestedUser.verify.action');

Route::get('/countries', [MiscController::class, 'countryList'])->name('country.view');
// Route::get('/games', [MiscController::class, 'gameList'])->name('game.view');
Route::get('/seed/event', [MiscController::class, 'seedBrackets']);
Route::get('/seed/joins', [MiscController::class, 'seedJoins']);
Route::get('/seed/results/{evenId}', [FirebaseController::class, 'seedResults']);
Route::get('/seed/tasks', [MiscController::class, 'allTasks']);
Route::get('/download-withdrawal-csv/{token}', [MiscController::class, 'downloadWithdrawalCsv'])->name('download.withdrawal.csv');

// Shop - Enable in non-production environments
if (config('app.env') !== 'production') {
    Route::get('/shop', [App\Http\Controllers\Shop\ShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/{product}', [App\Http\Controllers\Shop\ShopController::class, 'show'])->name('shop.show');
}

// Logout
Route::get('logout', [AuthController::class, 'logoutAction'])->name('logout.action');

// Search bar
Route::get('/event/search', [MiscController::class, 'showLandingPage'])->name('public.search.view');
Route::get('/event/{id}/{title?}', [ParticipantEventController::class, 'ViewEvent'])->name('public.event.view');
Route::get('/view/team/{id}/{title?}', [ParticipantTeamController::class, 'teamManagement'])->name('public.team.view');
Route::get('/view/participant/{id}/{title?}', [ParticipantController::class, 'viewProfileById'])->name('public.participant.view');
Route::get('/view/organizer/{id}/{title?}', [OrganizerController::class, 'viewProfileById'])->name('public.organizer.view');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::feeds();

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => ['check-permission:participant', 'prevent-back-history']], function () {
        Route::get('/wallet', [StripeController::class, 'showWalletDashboard'])->name('wallet.dashboard');
        Route::get('/wallet/payment-method', [StripeController::class, 'showPaymentMethodForm'])->name('wallet.payment-method');
        Route::post('/wallet/payment-method', [StripeController::class, 'savePaymentMethod'])->name('wallet.save-payment-method');
        Route::get('/wallet/topupCallback', [StripeController::class, 'topupCallback'])->name('wallet.topupCallback');
        Route::get('/wallet/transactions', [StripeController::class, 'showTransactions'])->name('wallet.transactions');
        Route::get('/wallet/coupons', [StripeController::class, 'showCoupons'])->name('wallet.coupons');

        Route::post('/wallet/checkout', [StripeController::class, 'checkoutTopup'])->name('wallet.checkout');
        Route::post('/wallet/redeem-coupon', [StripeController::class, 'redeemCoupon'])->name('wallet.redeem-coupon');
    });
    Route::group(['middleware' => 'check-permission:participant|organizer'], function () {
        // Notifications page
        Route::view('/user/notifications', 'Users.Notifications')->name('user.notif.view');
        Route::post('/media', [ImageVideoController::class, 'upload']);
        Route::get('/user/message', [ChatController::class, 'message'])->name('user.message.view');
        Route::get('/user/settings', [UserController::class, 'settings'])->name('user.settings.view');
        Route::post('user/{id}/background', [UserController::class, 'replaceBackground'])->name('user.userBackground.action');
    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => ['auth', 'prevent-back-history']], function () {
        Route::redirect('/profile', '/admin', 301)->name('admin.profile.view');
        Route::get('/brackets/{eventId}', [OrganizerEventResultsController::class, 'viewBrackets'])->name('filament.pages.brackets.index');
        // Route::get('/disputes/{eventId}', [FirebaseController::class, 'showDisputes']);
        // Route::post('/brackets/{eventId}', [FirebaseController::class, 'storeBrackets'])->name('filament.pages.brackets.store');
        Route::group(['middleware' => 'check-permission:admin'], function () {
            Route::get('/onboardBeta', [BetaController::class, 'viewOnboardBeta'])->name('admin.onboardBeta.view');
            Route::post('/onboardBeta', [BetaController::class, 'postOnboardBeta'])->name('admin.onboardBeta.action');
        });
    });
});

/* THIS IS THE PARTICIPANT VIEW */
Route::group(['prefix' => 'participant'], function () {
    // Normal login
    Route::get('/signin', [AuthController::class, 'participantSignIn'])->name('participant.signin.view');
    Route::view('/signup', 'Auth.ParticipantSignUp')->name('participant.signup.view');
    Route::post('/signin', [AuthController::class, 'accessUser'])->name('participant.signin.action');
    Route::post('/signup', [AuthController::class, 'storeUser'])->name('participant.signup.action');

    // Social login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('participant.google.login');

    // General participant functions
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => ['check-permission:participant|admin', 'prevent-back-history']], function () {
            Route::get('/home', [MiscController::class, 'showLandingPage'])->name('participant.home.view');

            Route::post('/friends', [SocialController::class, 'updateFriend'])->name('participant.friends.update');
            Route::post('/follow', [SocialController::class, 'followParticipant'])->name('participant.participant.follow');

            // Team management
            Route::get('/team/list', [ParticipantTeamController::class, 'teamList'])->name('participant.team.view');
            Route::get('/team/search', [ParticipantTeamController::class, 'teamList'])->name('participant.team.search');
            Route::view('/team/create', 'Participant.CreateTeam')->name('participant.team.create');
            Route::get('/team/{id}/edit', [ParticipantTeamController::class, 'editTeamView'])->name('participant.team.edit');
            Route::get('/team/confirm', [ParticipantEventController::class, 'confirmUpdate']);
            Route::get('/team/{id}/manage/member', [ParticipantTeamController::class, 'teamMemberManagement'])->name('participant.member.manage');
            Route::get('/team/{id}/manage', [ParticipantTeamController::class, 'teamManagement'])->name('participant.team.manage');
            Route::get('/team/{id}/register', [ParticipantEventController::class, 'registrationManagement'])->name('participant.register.manage');

            Route::post('/team/roster/approve', [ParticipantRosterController::class, 'approveRosterMember'])->name('participant.roster.approve');
            Route::post('/team/roster/disapprove', [ParticipantRosterController::class, 'disapproveRosterMember'])->name('participant.roster.disapprove');
            Route::post('/team/roster/vote', [ParticipantRosterController::class, 'voteForEvent'])->name('participant.roster.vote');

            Route::post('/team/roster/captain', [ParticipantRosterController::class, 'captainRosterMember'])->name('participant.roster.captain');
            Route::post('/team/create', [ParticipantTeamController::class, 'teamStore'])->name('participant.team.store');
            Route::post('/team/{id}/follow', [ParticipantTeamController::class, 'teamFollow'])->name('participant.team.follow');
            // TODO check memebers
            Route::post('/team/member/{id}/pending', [ParticipantTeamController::class, 'pendingTeamMember'])->name('participant.member.pending');

            // Event management
            Route::post('/event/member', [ParticipantTeamController::class, 'teamMemberManagementRedirected'])->name('participant.memberManage.action');
            Route::get('/event/{id}', [ParticipantEventController::class, 'viewEvent'])->name('participant.event.view');
            Route::get('/eventv2/{id}', [ParticipantEventController::class, 'viewEvent'])->name('participant.eventv2.view');

            Route::post('/event/{id}/join/team/select', [ParticipantEventController::class, 'selectTeamToJoinEvent'])->name('participant.selectTeamToJoin.action');
            Route::post('/event/{id}/join/team/create', [ParticipantEventController::class, 'createTeamToJoinEvent'])->name('participant.createTeamToJoinEvent.action');
            Route::post('/event/{id}/join/redirect/selectOrCreateTeamToJoinEvent', [ParticipantEventController::class, 'redirectToSelectOrCreateTeamToJoinEvent'])->name('participant.event.selectOrCreateTeam.redirect');
            Route::post('/event/{id}/join/redirect/createTeamToJoinEvent', [ParticipantEventController::class, 'redirectToCreateTeamToJoinEvent'])->name('participant.event.createTeam.redirect');
            Route::get('event/checkout/transition', [ParticipantCheckoutController::class, 'showCheckoutTransition'])->name('participant.checkout.transition');
            Route::post('event/checkout', [ParticipantCheckoutController::class, 'showCheckout'])->name('participant.checkout.action');
            Route::post('/event/walletCheckout', action: [ParticipantCheckoutController::class, 'walletCheckout'])->name('participant.walletCheckout.action');

            Route::post('/event/confirmOrCancel', [ParticipantEventController::class, 'confirmOrCancel'])->name('participant.confirmOrCancel.action');

            // Profile
            Route::get('/profile', [ParticipantController::class, 'viewOwnProfile'])->name('participant.profile.view');
        });
    });
});

/* THIS IS THE ORGANIZER VIEW */
Route::group(['prefix' => 'organizer'], function () {
    // Normal login
    Route::get('/signin', [AuthController::class, 'organizerSignin'])->name('organizer.signin.view');

    Route::view('/signup', 'Auth.OrganizerSignUp')->name('organizer.signup.view');
    Route::post('/signin', [AuthController::class, 'accessUser'])->name('organizer.signin.action');
    Route::post('/signup', [AuthController::class, 'storeUser'])->name('organizer.signup.action');

    // Social login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('organizer.google.login');

    // General organizer functions
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => ['check-permission:organizer|admin', 'prevent-back-history']], function () {
            // Organizer home
            Route::get('/home', [OrganizerEventController::class, 'home'])->name('organizer.home.view');
            Route::get('/event/{id}/results', [OrganizerEventResultsController::class, 'index'])->name('event.awards.index');
            Route::get('/event/{id}/matches', [OrganizerEventResultsController::class, 'viewBrackets'])->name('event.matches.index');

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
            Route::get('event/{id}/success', [OrganizerEventController::class, 'showSuccess'])->name('organizer.success.view');

            // Live page
            Route::get('event/{event}/live', [OrganizerEventController::class, 'show'])->name('organizer.live.view');
            // Checkout page
            Route::get('event/{id}/checkout', [OrganizerCheckoutController::class, 'showCheckout'])->name('organizer.checkout.view');
            Route::get('event/{id}/checkout/transition', [OrganizerCheckoutController::class, 'showCheckoutTransition'])->name('organizer.checkout.transition');

            Route::get('/profile', [OrganizerController::class, 'viewOwnProfile'])->name('organizer.profile.view');
        });
    });
});

Route::middleware(['auth',  'prevent-back-history'])->group(function () {
    Route::group(['middleware' => ['check-permission:participant|organizer']], function () {
        // Shop routes - Enable in non-production environments
        if (config('app.env') !== 'production') {
            Route::get('/orders', [App\Http\Controllers\Shop\OrdersController::class, 'index'])->name('orders.index');
            Route::get('/cart', [App\Http\Controllers\Shop\CartController::class, 'index'])->name('cart.index');
            Route::post('/cart/{product}', [App\Http\Controllers\Shop\CartController::class, 'store'])->name('cart.store');
            Route::patch('/cart/{product}', [App\Http\Controllers\Shop\CartController::class, 'update'])->name('cart.update');
            Route::delete('/cart/{product}', [App\Http\Controllers\Shop\CartController::class, 'destroy'])->name('cart.destroy');

            // Cart2 routes with wallet and discount support
            Route::get('/checkout', [App\Http\Controllers\Shop\CheckoutController::class, 'showCheckout'])->name('checkout.index');
            Route::post('/walletCheckout', [App\Http\Controllers\Shop\CheckoutController::class, 'walletCheckout'])->name('shop.walletCheckout');
            Route::get('/checkout/transition', [App\Http\Controllers\Shop\CheckoutController::class, 'showCheckoutTransition'])->name('shop.checkout.transition');
            Route::get('/thankyou', [App\Http\Controllers\Shop\CheckoutController::class, 'thankyou'])->name('confirmation.index');
        }
    });
});
