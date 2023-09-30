<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController;

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
Route::get('/', [AuthController::class, 'showLandingPage'])->name("landingPage");

Route::group(['prefix'=>'participant'], function () {
	Route::get('/signin', [AuthController::class, 'signIn'])->name("signinView");
	Route::get('/signup', [AuthController::class, 'signUp'])->name("signupView"); 
	Route::post('/signin', [AuthController::class, 'accessParticipant'])->name("signinAction");
	Route::post('/signup', [AuthController::class, 'storeParticipant'])->name("signupAction"); 
});
Route::group(['prefix'=>'organizer'], function () {
	Route::get('/signin', [AuthController::class, 'organizerSignin'])->name("organizerSigninView"); 
	Route::get('/signup', [AuthController::class, 'organizerSignup'])->name("organizerSignupView");
	Route::post('/signin', [AuthController::class, 'accessOrganizer'])->name("organizerSigninAction"); 
	Route::post('/signup', [AuthController::class, 'storeOrganizer'])->name("organizerSignupAction");
	Route::group(['prefix'=>'organizer'], function () {
		Route::resource('/event', EventController::class);
		Route::get('/event/manage', [EventController::class, 'manage']);
	});
});


Route::get('/dashboard', [AuthController::class, 'dashboard']); // Route for Dashboard Page



Route::redirect('/login', '/admin/users')->name('login');

Route::group(['middleware'=>'auth'], function () {
	Route::get('/authenticated',['middleware'=>'check-permission','uses'=>'PermissionController@showAuthenticated']);
	Route::get('/noauth',['middleware'=>'check-permission','uses'=>'PermissionController@showNoAuth']);
	Route::get('/permissions-admin-participant',
		['middleware'=>'check-permission:participant|admin','uses'=>'PermissionController@showParticipantPage']
	);
	Route::get('/permissions-admin-organizer',
		['middleware'=>'check-permission:admin|organizer','uses'=>'PermissionController@showOrganizerPage']
	);
});

// Route::redirect('/laravel/login', '/admin')->name('admin');


