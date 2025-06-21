<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Shared\ImageVideoController;
use App\Http\Controllers\Open\BetaController;
use App\Http\Controllers\Organizer\OrganizerController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerEventResultsController;
use App\Http\Controllers\Organizer\OrganizerInvitationController;
use App\Http\Controllers\Participant\ParticipantCheckoutController;
use App\Http\Controllers\Participant\ParticipantController;
use App\Http\Controllers\Participant\ParticipantEventController;
use App\Http\Controllers\Participant\ParticipantTeamController;
use App\Http\Controllers\Shared\FirebaseController;
use App\Http\Controllers\Shared\SocialController;
use App\Http\Controllers\Shared\StripeController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\UserController;
use App\Models\ImageVideo;
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

Route::get('/user/{id}/logs', [ParticipantController::class, 'getActivityLogs'])->name('activity-logs.index');
Route::get('/user/{id}/connections', [SocialController::class, 'getConnections'])->name('user.connections.index')->middleware('prevent-back-history');
Route::post('/event/{id}/invitation', [OrganizerInvitationController::class, 'store'])->name('event.invitation.store');
Route::post('/event/{id}/inviteDestroy', [OrganizerInvitationController::class, 'destroy'])->name('event.invitation.destroy');

Route::prefix('media')->group(function () {
    Route::post('/', [ImageVideoController::class, 'upload']);
    Route::get('/stream/{media}', [ImageVideoController::class, 'stream'])->name('media.stream');
    Route::delete('/{media}', [ImageVideoController::class, 'destroy']);
});

Route::put('/interest', [BetaController::class, 'interestedAction'])->name('public.interest.action');

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'check-jwt-permission:organizer|admin|participant'], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/teams/search', [ParticipantTeamController::class, 'search']);
        Route::post('/event/{id}/brackets', [ParticipantEventController::class, 'validateBracket'])->name('event.matches.validate');

        Route::get('/user/firebase-token', [FirebaseController::class, 'createToken']);
        Route::get('/user/{id}/reports', [SocialController::class, 'getReports'])->name('users.report.view');
        Route::get('/user/notifications', [UserController::class, 'viewNotifications'])->name('notifications.index');
        Route::post('/user/withdraw', [StripeController::class, 'processWithdrawal'])->name('wallet.withdraw');
        Route::post('/user/unlink', [UserController::class, 'unlinkBankAccount'])->name('wallet.unlink');
        Route::post('/user/likes', [ParticipantEventController::class, 'likeEvent'])->name('participant.events.like');
        Route::post('/user/participants', [ParticipantController::class, 'searchParticipant'])->name('user.teams.index');
        Route::post('/user/firebase', [ChatController::class, 'getFirebaseUsers'])->name('user.firebase.readAll');
        Route::post('/user/settings', [UserController::class, 'changeSettings'])->name('user.settings.action');
        Route::post('/user/notifications', [UserController::class, 'createNotification'])->name('notifications.store'); 
        Route::post('/user/notifications/{id}', [UserController::class, 'markAsRead'])->name('notifications.actopn');

        Route::post('/user/{id}/background', [UserController::class, 'replaceBackground'])->name('user.userBackgroundApi.action');
        Route::post('/user/{id}/star', [SocialController::class, 'toggleStar'])->name('users.star.action');
        Route::post('/user/{id}/block', [FirebaseController::class, 'toggleBlock'])->name('users.block.action');
        Route::post('/user/{id}/report', [SocialController::class, 'report'])->name('users.report.action');
        Route::post('/card/intent', [StripeController::class,  'stripeCardIntentCreate'])->name('stripe.stripeCardIntentCreate');
    });
});

Route::group(['prefix' => 'participant'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => 'check-jwt-permission:participant|admin'], function () {
            Route::post('/events', [ParticipantEventController::class, 'index'])->name('events.index');
            Route::post('/organizer/follow', [SocialController::class, 'followOrganizer'])->name('participant.organizer.follow');
            Route::post('/profile', [ParticipantController::class, 'editProfile'])->name('participant.profile.update');
            Route::post('/team', [ParticipantTeamController::class, 'editTeam'])->name('participant.team.update');

            Route::post('/team/{id}/user/{userId}/invite', [ParticipantTeamController::class, 'inviteMember'])->name('participant.member.invite');
            Route::post('/team/{id}/member/{memberId}/captain', [ParticipantTeamController::class, 'captainMember'])->name('participant.member.captain');
            Route::post('/team/{id}/member/{memberId}/deleteCaptain', [ParticipantTeamController::class, 'deleteCaptain'])->name('participant.member.deleteCaptain');

            // TODO check memebers
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
            Route::delete('/event/achievements/{achievementId}', [OrganizerEventResultsController::class, 'destroyAchievements'])->name('event.achievements.destroy');
            Route::post('/event/{id}/destroy', [OrganizerEventController::class, 'destroy'])->name('event.destroy.action');
            Route::post('/event/{id}/results', action: [OrganizerEventResultsController::class, 'store'])->name('event.results.store');
            Route::post('/event/{id}/notifications', action: [OrganizerEventController::class, 'storeNotify'])->name('event.notify.store');
            
            Route::post('/event/{id}/matches', [OrganizerEventResultsController::class, 'upsertBracket'])->name('event.matches.upsert');
            Route::post('/event/{id}/awards', [OrganizerEventResultsController::class, 'storeAward'])->name('event.awards.store');
            Route::delete('/event/{id}/awards/{awardId}', [OrganizerEventResultsController::class, 'destroyAward'])->name('event.awards.destroy');
            Route::post('/event/{id}/achievements', [OrganizerEventResultsController::class, 'storeAchievements'])->name('event.achievements.store');
            Route::post('/profile', [OrganizerController::class, 'editProfile'])->name('organizer.profile.update');
        });
    });
});


