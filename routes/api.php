<?php

use App\Http\Controllers\Organizer\InvitationController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('stripe.')
    ->controller(StripeController::class)
    ->prefix('stripe')
    ->group(function () {
        Route::post('payment', 'organizerTeamPay')->name('organizerTeamPay');
    });


Route::post('/event/{id}/invitation', [InvitationController::class, 'store'])
    ->name("event.invitation.store");
   
   
// Route::any('/admin', 'AdminController@index')->middleware('check-permission:admin');
