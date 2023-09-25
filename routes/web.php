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

Route::get('/', [AuthController::class, 'signIn']);
Route::get('/organizerSignin', [AuthController::class, 'organizerSignin']); 
Route::get('/signup', [AuthController::class, 'signUp']); 
Route::get('/organizerSignup', [AuthController::class, 'organizerSignup']);
Route::post('/', [AuthController::class, 'accessParticipant'])->name("signinAction");
Route::post('/organizerSignin', [AuthController::class, 'accessOrganizer'])->name("organizerSigninAction"); 
Route::post('/signup', [AuthController::class, 'storeParticipant'])->name("signupAction"); 
Route::post('/organizerSignup', [AuthController::class, 'storeOrganizer'])->name("organizerSignupAction");

Route::get('/dashboard', [AuthController::class, 'dashboard']); // Route for Dashboard Page

Route::resource('/event', EventController::class);
Route::get('/event/organizer/manage', [EventController::class, 'manage']);

Route::redirect('/login', '/admin/users')->name('login');

// Route::redirect('/laravel/login', '/admin')->name('admin');


