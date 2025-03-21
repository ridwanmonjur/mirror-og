<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateOrganizersRequest;
use App\Models\Address;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Organizer;
use App\Models\OrganizerFollow;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizerController extends Controller
{
    public function viewOwnProfile(Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            $user_id = $user?->id ?? null;
            $user->isFollowing = null;

            return $this->viewProfile($request, $user_id, $user, true);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function viewProfileById(Request $request, $id)
    {
        try {
            $loggedInUser = Auth::user();
            $user = User::findOrFail($id);
            if ($user->role === 'PARTICIPANT') {
                return redirect()->route('public.participant.view', ['id' => $id]);
            }
            if ($user->role === 'ADMIN') {
                return $this->showErrorParticipant('This is an admin view!');
            }

            if ($loggedInUser) {
                // @phpstan-ignore-next-line
                $user->isFollowing = OrganizerFollow::where('participant_user_id', $loggedInUser->id)
                    ->where('organizer_user_id', $user->id)
                    ->first();
            } else {
                // @phpstan-ignore-next-line
                $user->isFollowing = null;
            }

            return $this->viewProfile($request, $loggedInUser ? $loggedInUser->id : null, $user, false);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function editProfile(UpdateOrganizersRequest $request)
    {
        $user = $request->attributes->get('user');
        $validatedData = $request->validated();
        $address = $userProfile = $organizer = null;
        try {
            DB::transaction(function () use ($user, $validatedData) {
                if (isset($validatedData['address'])) {
                    $address = isset($validatedData['address']['id'])
                    ? Address::findOrFail($validatedData['address']['id'])
                    : new Address();

                    $address->fill($validatedData['address']);
                    $address->user_id = $user->id;
                    $address->save();
                   
                }
                $userProfile = User::where('id', $user->id)->first();
                $userProfile->fill($validatedData['userProfile']);
                $userProfile->save();

                $organizer = isset($validatedData['organizer']['id'])
                    ? Organizer::findOrFail($validatedData['organizer']['id'])
                    : new Organizer();

                $organizer->user_id = $user->id;
                $organizer->fill($validatedData['organizer']);
                $organizer->save();
            });

            $user->uploadUserBanner($request);

            return response()->json(
                [
                        'message' => 'User profile updated successfully',
                        'success' => true,
                        'data' => [
                            'organizer' => $organizer,
                            'address' => $address,
                            'userProfile' => $userProfile
                        ]
                ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 400);
        }
    }

    private function viewProfile(Request $request, $logged_user_id, $userProfile, $isOwnProfile = true)
    {
        try {

            $followersCount = OrganizerFollow::where('organizer_user_id', $userProfile->id)->count();
            $joinEvents = EventDetail::where('user_id', $userProfile->id)
                ->whereNotIn('status', ['DRAFT', 'PENDING'])
                ->with(['tier',  'game', 'user'])->get();
            $lastYearEventsCount = EventDetail::whereYear('created_at', now()->year)
                ->where('user_id', $userProfile->id)
                ->whereNotIn('status', ['DRAFT', 'PENDING'])
                ->count();
            $beforeLastYearEventsCount = EventDetail::whereYear('created_at', '<=', now()->year - 1)
                ->where('user_id', $userProfile->id)
                ->whereNotIn('status', ['DRAFT'.'PENDING'])
                ->count();

            $teamsCount = JoinEvent::where('join_status', 'confirmed')
                ->whereIn('event_details_id', function ($query) use ($userProfile) {
                $query->select('id')
                    ->from('event_details')
                    ->whereNotIn('status', ['DRAFT', 'ENDED', 'PENDING'])
                    ->where('user_id', $userProfile->id);
            })
                ->count();

            $tierPrizeCount = DB::table('event_details')
                ->where('event_details.user_id', $userProfile->id)
                ->whereNotIn('status', ['DRAFT', 'PENDING'])
                ->leftJoin('event_tier', 'event_details.event_tier_id', '=', 'event_tier.id')
                ->select(['event_details.id as event_id',
                    'event_details.event_tier_id',
                    'event_tier.tierPrizePool',
                ])
                ->sum('tierPrizePool');

            $userIds = $joinEvents->pluck('user_id')->flatten()->toArray();
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            $isFollowing = OrganizerFollow::getIsFollowing($userProfile->id, $userIds);
            $joinEventsHistory = $joinEventsActive = $values = [];
            ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory]
                = EventDetail::processEvents($joinEvents, $isFollowing);

            return view(
                'Public.OrgProfile',
                compact(
                    'joinEvents',
                    'userProfile',
                    'isOwnProfile',
                    'followersCount',
                    'joinEventsHistory',
                    'joinEventsActive',
                    'followCounts',
                    'lastYearEventsCount',
                    'beforeLastYearEventsCount',
                    'teamsCount',
                    'tierPrizeCount'
                )
            );
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }
}
