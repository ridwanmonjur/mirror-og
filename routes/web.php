<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\InvitationController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group([
	'prefix' => 'admin',
	'middleware' => ['check-permission:admin'],
	'excluded_middleware' => ['login'],
], function () {
});
Route::get('/artisan/storage', function () {
	$exitCode = Artisan::call('storage:link', []);
	echo $exitCode; // 0 exit code for no errors.});
});
Route::get('/artisan/migrate', function () {
	Artisan::call('migrate');
	dd('migrated!');
});

Route::get('/', [AuthController::class, 'showLandingPage'])->name("landing.view");
Route::get('logout', [AuthController::class, 'logoutAction'])->name("logout.action");


Route::get('/forget-password', [AuthController::class, 'createForget'])->name("user.forget.view");
Route::post('/forget-password', [AuthController::class, 'storeForget'])->name("user.forget.action");
Route::get('/reset-password/{token}', [AuthController::class, 'createReset'])->name("user.reset.view");
Route::post('/reset-password', [AuthController::class, 'storeReset'])->name("user.reset.action");

// Add verify email login in login
Route::get('/account/verify-resend/{email}', [AuthController::class, 'verifyResend'])->name('user.verify.resend');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify.action');
Route::get('/account/verify-success/', [AuthController::class, 'verifySuccess'])->name('user.verify.success');

Route::post('/logout', [AuthController::class, 'logout'])->name("participant.logout.action");

Route::get('/event/search', [AuthController::class, 'showLandingPage'])->name('public.search.view');
Route::get('/event/{id}', [ParticipantEventController::class, 'ViewEvent'])->name('public.event.view');

Route::group(['prefix' => 'participant'], function () {
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
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'check-permission:participant|admin'], function () {
			Route::get('/home', [ParticipantEventController::class, 'home'])->name("participant.home.view");
			Route::get('/team-list/{id}', [ParticipantEventController::class, 'teamList'])->name("participant.team.view");
			Route::get('/create-team/{id}', [ParticipantEventController::class, 'createTeamView']);
			Route::post('/team-management', [ParticipantEventController::class, 'TeamStore']);
			Route::get('/team-manage/{id}', [ParticipantEventController::class, 'teamManagement'])->name("participant.teamManagement.view");
			Route::get('/registration-manage/{id}', [ParticipantEventController::class, 'registrationManagement'])->name("participant.registrationManagement.view");
			Route::get('/selectTeam', [ParticipantEventController::class, 'SelectTeamtoRegister']);
			Route::post('/home', [ParticipantEventController::class, 'TeamtoRegister']);
			Route::get('/confirm', [ParticipantEventController::class, 'ConfirmUpdate']);
			Route::get('/event/{id}', [ParticipantEventController::class, 'ViewEvent']);
			Route::post('/events/{id}', [ParticipantEventController::class, 'JoinEvent'])->name('join.store');
			Route::post('/follow-organizer', [ParticipantEventController::class, 'FollowOrganizer'])->name('follow.organizer');
			Route::delete('participant/unfollow-organizer', [ParticipantEventController::class, 'unfollowOrganizer'])->name('unfollow.organizer');
		});
	});
});
Route::group(['prefix' => 'organizer'], function () {
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
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'check-permission:organizer|admin'], function () {
			Route::get('/home', [EventController::class, 'home'])->name("organizer.home.view");
			Route::resource('/event', EventController::class, [
				'index' => "event.index",
				'create' => "event.create",
				'store' => "event.store",
				'show' => "event.show",
				'edit' => "event.edit",
				'update' => "event.update",
			]);
			Route::get('/event/{id}/invitation', [InvitationController::class, 'index'])
				->name('event.invitation.index');
			Route::post('event/{id}/updateForm', [EventController::class, 'updateForm'])->name('event.updateForm');
			Route::get('event/{id}/success', [EventController::class, 'showSuccess'])
				->middleware('prevent-back-button')
				->name("organizer.success.view");
			Route::get('event/{id}/live', [EventController::class, 'showLive'])
				->middleware('prevent-back-button')
				->name("organizer.live.view");
		});
	});
});

Route::group(['middleware' => ['auth']], function () {
	Route::get('/email/verify', 'AuthController@show')->name('verification.notice');
	Route::get('/email/verify/{id}/{hash}', 'AuthController@verify')->name('verification.verify')->middleware(['signed']);
	Route::post('/email/resend', 'AuthController@resend')->name('verification.resend');
});

Route::get('/dashboard', [AuthController::class, 'dashboard']); // Route for Dashboard Page

