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

Route::get('/', [AuthController::class, 'signIn']); // Route for Sign In Page

Route::get('/signup', [AuthController::class, 'signUp']); // Route for Sign In Page

Route::get('/dashboard', [AuthController::class, 'dashboard']); // Route for Dashboard Page

Route::resource('/event', EventController::class);
Route::get('/event/organizer/manage', [EventController::class, 'manage']);

Route::redirect('/login', '/admin/users')->name('login');

// Route::redirect('/laravel/login', '/admin')->name('admin');


