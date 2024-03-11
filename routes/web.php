<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\InvitationController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\Participant\ParticipantTeamController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organizer\OrganizerCheckoutController;

/* THIS IS THE UNSIGNED VIEW */
// Home
Route::get('/', [AuthController::class, 'showLandingPage'])->name("landing.view");

// Forget, reset password
Route::get('/forget-password', [AuthController::class, 'createForget'])->name("user.forget.view");
Route::get('/reset-password/{token}', [AuthController::class, 'createReset'])->name("user.reset.view");
Route::post('/reset-password', [AuthController::class, 'storeReset'])->name("user.reset.action");
Route::post('/forget-password', [AuthController::class, 'storeForget'])->name("user.forget.action");

// Verify
Route::get('/account/verify-resend/{email}', [AuthController::class, 'verifyResend'])->name('user.verify.resend');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify.action');
Route::get('/account/verify-success/', [AuthController::class, 'verifySuccess'])->name('user.verify.success');

// Logout
Route::get('logout', [AuthController::class, 'logoutAction'])->name("logout.action");
Route::post('/logout', [AuthController::class, 'logout'])->name("participant.logout.action");

// Search bar
Route::get('/event/search', [AuthController::class, 'showLandingPage'])->name('public.search.view');
Route::get('/event/{id}', [ParticipantEventController::class, 'ViewEvent'])->name('public.event.view');

/* THIS IS THE PARTICIPANT VIEW */
Route::group(['prefix' => 'participant'], function () {
	
	// Normal login
	Route::get('/signin', [AuthController::class, 'signIn'])->name("participant.signin.view");
	Route::get('/signup', [AuthController::class, 'signUp'])->name("participant.signup.view");	
	Route::post('/signin', [AuthController::class, 'accessUser'])->name("participant.signin.action");
	Route::post('/signup', [AuthController::class, 'storeUser'])->name("participant.signup.action");
	
	// Google login
	Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name("participant.google.login");
	Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('participant.google.callback');
	
	// Steam login
	Route::get('/auth/steam', [AuthController::class, 'redirectToSteam'])->name("participant.steam.login");
	Route::get('/auth/steam/callback', [AuthController::class, 'handleSteamCallback'])->name("participant.steam.callback");
	
	// General participant functions
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'check-permission:participant|admin'], function () {
			
			// Home page
			Route::get('/home', [ParticipantEventController::class, 'home'])->name("participant.home.view");
			
			// Team management
			Route::get('/team/list', [ParticipantEventController::class, 'teamList'])->name("participant.team.view");
			Route::get('/team/create', [ParticipantTeamController::class, 'createTeamView'])->name("participant.team.create");
			Route::get('/team/{id}/edit', [ParticipantTeamController::class, 'editTeamView'])->name("participant.team.edit");
			Route::get('/team/confirm', [ParticipantEventController::class, 'confirmUpdate']);
			Route::get('/team/{id}/manage/member', [ParticipantEventController::class, 'teamMemberManagement'])->name("participant.member.manage");
			Route::get('/team/{id}/manage', [ParticipantEventController::class, 'teamManagement'])->name("participant.team.manage");
			Route::get('/team/{id}/register', [ParticipantEventController::class, 'registrationManagement'])->name("participant.registrationManagement.view");
			Route::post('/team/create', [ParticipantTeamController::class, 'teamStore'])->name("participant.team.store");
			Route::post('/team/{id}/editStore', [ParticipantTeamController::class, 'teamEditStore'])->name("participant.team.editStore");
			Route::post('/team/member/{id}/approve', [ParticipantTeamController::class, 'approveTeamMember'])->name('participant.member.approve');
			Route::post('/team/member/{id}/disapprove', [ParticipantTeamController::class, 'disapproveTeamMember'])->name('participant.member.disapprove');
			Route::post('/team/roster/{id}/approve', [ParticipantTeamController::class, 'approveRosterMember'])->name('participant.roster.approve');
			Route::post('/team/roster/{id}/disapprove', [ParticipantTeamController::class, 'disapproveRosterMember'])->name('participant.roster.disapprove');
			Route::post('/team/captain/store', [ParticipantEventController::class, 'makeCaptain'])->name('participant.captain.add');
			Route::post('/team/captain/delete', [ParticipantEventController::class, 'deleteCaptain'])->name('participant.captain.remove');
			
			// Event management
			Route::get('/event/{id}/team/{teamId}/manage/roster', [ParticipantEventController::class, 'rosterMemberManagement'])->name('participant.roster.manage');
			Route::post('/event/{id}/join/team/select', [ParticipantEventController::class, 'selectTeamToJoinEvent'])->name('participant.selectTeamToJoin.action');
			Route::post('/event/{id}/join/team/create', [ParticipantEventController::class, 'createTeamToJoinEvent'])->name('participant.createTeamToJoinEvent.action');
			Route::post('/event/{id}/join/redirect/selectOrCreateTeamToJoinEvent', [ParticipantEventController::class, 'redirectToSelectOrCreateTeamToJoinEvent'])->name('participant.event.selectOrCreateTeam.redirect');
			Route::post('/event/{id}/join/redirect/createTeamToJoinEvent', [ParticipantEventController::class, 'redirectToCreateTeamToJoinEvent'])->name('participant.event.createTeam.redirect');
			Route::get('/event/{id}', [ParticipantEventController::class, 'viewEvent'])->name('participant.event.view');

			// Organizer management
			Route::post('/organizer/follow', [ParticipantEventController::class, 'followOrganizer'])->name('participant.organizer.follow');
			Route::delete('/organizer/unfollow', [ParticipantEventController::class, 'unfollowOrganizer'])->name('participant.organizer.unfollow');
			
		});
	});
});

/* THIS IS THE ORGANIZER VIEW */
Route::group(['prefix' => 'organizer'], function () {
	
	// Normal login
	Route::get('/signin', [AuthController::class, 'organizerSignin'])->name("organizer.signin.view");
	Route::get('/signup', [AuthController::class, 'organizerSignup'])->name("organizer.signup.view");
	Route::post('/signin', [AuthController::class, 'accessUser'])->name("organizer.signin.action");
	Route::post('/signup', [AuthController::class, 'storeUser'])->name("organizer.signup.action");
	
	// Google login
	Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name("organizer.google.login");
	Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name("organizer.google.callback");
	
	// Steam login
	Route::get('/auth/steam', [AuthController::class, 'redirectToSteam'])->name("organizer.steam.login");
	Route::get('/auth/steam/callback', [AuthController::class, 'handleSteamCallback'])->name("organizer.steam.callback");
	
	// General organizer functions
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'check-permission:organizer|admin'], function () {
			
			// Organizer home
			Route::get('/home', [OrganizerEventController::class, 'home'])->name("organizer.home.view");
			
			// Event manage
			Route::resource('/event', OrganizerEventController::class, [
				'index' => "event.index",
				'create' => "event.create",
				'store' => "event.store",
				'show' => "event.show",
				'edit' => "event.edit",
				'update' => "event.update",
			]);

			// Invite page
			Route::get('/event/{id}/invitation', [InvitationController::class, 'index'])->name('event.invitation.index');
			// Update invite
			Route::post('event/{id}/updateForm', [OrganizerEventController::class, 'updateForm'])->name('event.updateForm');
			// Success page
			Route::get('event/{id}/success', [OrganizerEventController::class, 'showSuccess'])
				->middleware('prevent-back-button')->name("organizer.success.view");
			// Live page
			Route::get('event/{id}/live', [OrganizerEventController::class, 'showLive'])->middleware('prevent-back-button')
				->name("organizer.live.view");
			// Checkout page
			Route::get('event/{id}/checkout', [OrganizerCheckoutController::class, 'showCheckout'])
				->middleware('prevent-back-button')->name("organizer.checkout.view");
			Route::get('event/{id}/checkout/transition', [OrganizerCheckoutController::class, 'showCheckoutTransition'])
				->name("organizer.checkout.transition");
		});
	});
});



