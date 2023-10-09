<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\Organizer\PermissionController;
use App\Mail\TestEmail;

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

// Route::any('/admin', 'AdminController@index')->middleware('check-permission:admin');

// Route::group([
// 	'prefix' => 'admin',
// 	'middleware' => ['check-permission:admin'],
// 	'excluded_middleware' => ['login'],
// ], function () {

// });

Route::get('/', [AuthController::class, 'showLandingPage'])->name("landing.view");

Route::get('/forget-password', [AuthController::class, 'createForget'])->name("user.forget.view");
Route::post('/forget-password', [AuthController::class, 'storeForget'])->name("user.forget.action");
Route::get('/reset-password/{token}', [AuthController::class, 'createReset'])->name("user.reset.view");
Route::post('/reset-password', [AuthController::class, 'storeReset'])->name("user.reset.action");

// Add verify email login in login
Route::get('/account/verify-resend/{email}', [AuthController::class, 'verifyResend'])->name('user.verify.resend');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify.action');
Route::get('/account/verify-success/', [AuthController::class, 'verifySuccess'])->name('user.verify.success');

Route::post('/logout', [AuthController::class, 'logout'])->name("participant.logout.action");

Route::group(['prefix' => 'participant'], function () {
	Route::get('/signin', [AuthController::class, 'signIn'])->name("participant.signin.view");
	Route::get('/signup', [AuthController::class, 'signUp'])->name("participant.signup.view");
	Route::post('/signin', [AuthController::class, 'accessUser'])->name("participant.signin.action");
	Route::post('/signup', [AuthController::class, 'storeUser'])->name("participant.signup.action");
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'check-permission:participant|admin'], function () {
			Route::get('/home', [ParticipantEventController::class, 'home'])->name("participant.home.view");
		});
		Route::get('/authDone', [PermissionController::class, 'showAuthenticated']);
		Route::get(
			'/permissions',
			['middleware' => 'check-permission:admin|organizer', 'uses' => 'PermissionController@showOrganizerPage']
		);
	});
});
Route::group(['prefix' => 'organizer'], function () {
	Route::get('/signin', [AuthController::class, 'organizerSignin'])->name("organizer.signin.view");
	Route::get('/signup', [AuthController::class, 'organizerSignup'])->name("organizer.signup.view");
	Route::post('/signin', [AuthController::class, 'accessUser'])->name("organizer.signin.action");
	Route::post('/signup', [AuthController::class, 'storeUser'])->name("organizer.signup.action");
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'check-permission:organizer|admin'], function () {
			Route::get('/home', [EventController::class, 'home'])->name("organizer.home.view");
			Route::resource('/event', EventController::class, [
				'index' => "event.index",
				'create' => "event.create"
			]);
		});
		// Route::get('/event/view/manage/', [EventController::class, 'manage'])->name('organizer.event.manage');
		Route::get('/authDone', [PermissionController::class, 'showAuthenticated']);
		Route::get(
			'/permissions',
			['middleware' => 'check-permission:admin|organizer', 'uses' => 'PermissionController@showOrganizerPage']
		);
	});
});

Route::group(['middleware' => ['auth']], function () {
	/**
	 * Verification Routes
	 */
	Route::get('/email/verify', 'VerificationController@show')->name('verification.notice');
	Route::get('/email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify')->middleware(['signed']);
	Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
});

Route::get('/dashboard', [AuthController::class, 'dashboard']); // Route for Dashboard Page

// Route::redirect('/login', '/admin/login')->name('login');

// Route::redirect('/laravel/login', '/admin')->name('admin');
