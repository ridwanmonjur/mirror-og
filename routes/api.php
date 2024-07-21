<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organizer\OrganizerController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerEventResultsController;
use App\Http\Controllers\Organizer\OrganizerInvitationController;
use App\Http\Controllers\Participant\ParticipantController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\Participant\ParticipantTeamController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\UserController;
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

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'check-jwt-permission:organizer|admin|participant'], function () {
        Route::post('/user/participants', [ParticipantController::class, 'searchParticipant'])->name('user.teams.index');
        Route::put('/user/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('user.notifications.read');
        Route::put('/user/notifications/read', [NotificationController::class, 'markAllAsRead'])->name('user.notifications.readAll');
        Route::post('/user/firebase', [ChatController::class, 'getFirebaseUsers'])->name('user.firebase.readAll');
        Route::post('/user/{id}/banner', [UserController::class, 'replaceBanner'])->name('participant.userBanner.action');
        Route::post('/user/{id}/background', [UserController::class, 'replaceBackground'])->name('user.userBackgroundApi.action');
    });
});

Route::group(['prefix' => 'participant'], function () {

    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => 'check-jwt-permission:participant|admin'], function () {
            Route::post('/events', [ParticipantEventController::class, 'index'])->name('events.index');
            Route::post('/events/likes', [ParticipantController::class, 'likeEvent'])->name('participant.events.like');
            Route::post('/organizer/follow', [ParticipantEventController::class, 'followOrganizer'])->name('participant.organizer.follow');
            Route::post('/profile', [ParticipantController::class, 'editProfile'])->name('participant.profile.update');
            Route::post('/team', [ParticipantTeamController::class, 'editTeam'])->name('participant.team.update');
            Route::post('/team/{id}/user/{userId}/invite', [ParticipantTeamController::class, 'inviteMember'])->name('participant.member.invite');
            Route::post('/team/{id}/member/{memberId}/captain', [ParticipantTeamController::class, 'captainMember'])->name('participant.member.captain');
            Route::post('/team/{id}/member/{memberId}/deleteCaptain', [ParticipantTeamController::class, 'deleteCaptain'])->name('participant.member.deleteCaptain');
            Route::post('/team/member/{id}/update', [ParticipantTeamController::class, 'updateTeamMember'])->name('participant.member.update');
            Route::post('/team/member/{id}/deleteInvite', [ParticipantTeamController::class, 'withdrawInviteMember'])->name('participant.member.deleteInvite');
            Route::post('/team/member/{id}/rejectInvite', [ParticipantTeamController::class, 'rejectInviteMember'])->name('participant.member.rejectInvite');
        });
    });
});



Route::group(['prefix' => 'organizer'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => 'check-jwt-permission:organizer|admin'], function () {
            Route::post('events/search', [OrganizerEventController::class, 'search'])->name('event.search.view');
            Route::post('/event/{id}/results', [OrganizerEventResultsController::class, 'store'])->name('event.results.store');
            Route::post('/event/{id}/awards', [OrganizerEventResultsController::class, 'storeAward'])->name('event.awards.store');
            Route::delete('/event/{id}/awards/{awardId}', [OrganizerEventResultsController::class, 'destroyAward'])->name('event.awards.destroy');
            Route::post('/event/{id}/achievements', [OrganizerEventResultsController::class, 'storeAchievements'])->name('event.achievements.store');
            Route::delete('/event/{id}/achievements/{achievementId}', [OrganizerEventResultsController::class, 'destroyAchievements'])->name('event.achievements.destroy');
            Route::put('notifications/{id}/read', [AuthController::class, 'markAsRead'])->name('organizer.notifications.read');
            Route::put('notifications/read', [AuthController::class, 'markAllAsRead'])->name('organizer.notifications.readAll');
            Route::post('/profile', [OrganizerController::class, 'editProfile'])->name('organizer.profile.update');
        });
    });
});

Route::name('stripe.')
    ->controller(StripeController::class)
    ->prefix('stripe')
    ->group(function () {
        Route::post('card/intent', 'stripeCardIntentCreate')->name('stripeCardIntentCreate');
    });

Route::post('/event/{id}/invitation', [OrganizerInvitationController::class, 'store'])->name('event.invitation.store');

// Route::any('/admin', 'AdminController@index')->middleware('check-permission:admin');
