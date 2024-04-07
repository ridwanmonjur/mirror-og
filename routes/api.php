<?php

use App\Http\Controllers\Organizer\InvitationController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Participant\ParticipantController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Participant\ParticipantTeamController;
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

Route::group(['prefix' => 'participant'], function () {
    Route::post('events', [ParticipantEventController::class, 'index'])->name('event.index');
    Route::post('/team/{id}/member/search', [ParticipantController::class, 'searchParticipant'])->name('participant.search');
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/team/{id}/member/search', [ParticipantTeamController::class, 'teamMemberManagement'])->name('participant.search');

		Route::group(['middleware' => 'check-permission:participant|admin'], function () {
            Route::get('/team/search/{search}', [ParticipantEventController::class, 'teamSearch'])->name("participant.team.search");
        });
    });
});



Route::group(['prefix' => 'organizer'], function () {
    Route::post('events/search', [OrganizerEventController::class, 'search'])->name('event.search.view');
});

Route::name('stripe.')
    ->controller(StripeController::class)
    ->prefix('stripe')
    ->group(function () {
        Route::post('card/intent', 'stripeCardIntentCreate')->name('stripeCardIntentCreate');
    });

Route::post('/event/{id}/invitation', [InvitationController::class, 'store'])->name('event.invitation.store');

// Route::any('/admin', 'AdminController@index')->middleware('check-permission:admin');
