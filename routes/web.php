<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PermissionController;
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
Route::get('/', [AuthController::class, 'showLandingPage'])->name("landing.view");

Route::get('/forget-password', [AuthController::class, 'createForget'])->name("user.forget.view");
Route::post('/forget-password', [AuthController::class, 'storeForget'])->name("user.forget.action");
Route::get('/reset-password', [AuthController::class, 'createReset'])->name("user.reset.view");
Route::post('/reset-password', [AuthController::class, 'storeReset'])->name("user.reset.action");

// TODO: Add verify email login in login
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify.action'); 
Route::get('/account/verify-success/', [AuthController::class, 'verifySuccess'])->name('user.verify.success'); 

Route::post('/logout', [AuthController::class, 'logout'])->name("participant.logout.action");

Route::group(['prefix' => 'participant'], function () {
	Route::get('/signin', [AuthController::class, 'signIn'])->name("participant.signin.view");
	Route::get('/signup', [AuthController::class, 'signUp'])->name("participant.signup.view");
	Route::post('/signin', [AuthController::class, 'accessParticipant'])->name("participant.signin.action");
	Route::post('/signup', [AuthController::class, 'storeParticipant'])->name("participant.signup.action");
	Route::group(['middleware' => 'auth'], function () {
		Route::get('/authDone', [PermissionController::class, 'showAuthenticated']);
		Route::get(
			'/permissions',
			['middleware' => 'check-permission:participant|admin', 'uses' => 'PermissionController@showParticipantPage']
		);
	});
});
Route::group(['prefix' => 'organizer'], function () {
	Route::get('/signin', [AuthController::class, 'organizerSignin'])->name("organizer.signin.view");
	Route::get('/signup', [AuthController::class, 'organizerSignup'])->name("organizer.signup.view");
	Route::post('/signin', [AuthController::class, 'accessOrganizer'])->name("organizer.signin.action");
	Route::post('/signup', [AuthController::class, 'storeOrganizer'])->name("organizer.signup.action");
	Route::group(['middleware' => 'auth'], function () {
		Route::resource('/event', EventController::class);
		Route::get('/event/manage', [EventController::class, 'manage']);
		Route::get('/authDone', [PermissionController::class, 'showAuthenticated']);
		Route::get(
			'/permissions',
			['middleware' => 'check-permission:admin|organizer', 'uses' => 'PermissionController@showOrganizerPage']
		);
	});
});

Route::group(['middleware' => ['auth']], function() {
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
