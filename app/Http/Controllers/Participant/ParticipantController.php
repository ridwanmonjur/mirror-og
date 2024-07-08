<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\FriendRequest;
use App\Http\Requests\LikeRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Models\EventInvitation;
use App\Models\OrganizerFollow;
use App\Models\Friend;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\Participant;
use App\Models\ParticipantFollow;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;

class ParticipantController extends Controller
{
    public function searchParticipant(Request $request)
    {
        // TODO LIVEWIRE

        $teamId = $request->teamId;
        $selectTeam = Team::find($teamId);
        $page = 5;
        $userList = User::getParticipants($request, $teamId)->paginate($page);
        foreach ($userList as $user) {
            $user->is_in_team = $user->members->isNotEmpty() ? 'yes' : 'no';
        }

        $outputArray = compact('userList', 'selectTeam');
        $view = view('Participant.__MemberManagementPartials.MemberManagementScroll', $outputArray)->render();

        return response()->json(['html' => $view]);
    }

    public function viewRequest(Request $request)
    {
        $user = $request->attributes->get('user');
        $user_id = $user->id;

        // pending requests
        $invitedTeamAndMemberList = Team::join('team_members', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $user_id)
            ->where(function ($query) {
                $query->where([
                    ['team_members.status', 'pending'],
                    ['team_members.actor', 'team'],
                ])->orWhere([
                    ['team_members.status', 'rejected'],
                    ['team_members.actor', 'user'],
                ]);
            })
            ->select('teams.*', 'team_members.*')
            ->get();

        $teamIdList = $invitedTeamAndMemberList->pluck('team_id')->toArray();
        $membersCount = DB::table('teams')
            ->leftJoin('team_members', 'teams.id', '=', 'team_members.team_id')
            ->whereIn('teams.id', $teamIdList)
            ->where(function ($query) {
                $query->where([
                    ['team_members.status', 'pending'],
                    ['team_members.actor', 'team'],
                ])->orWhere([
                    ['team_members.status', 'rejected'],
                    ['team_members.actor', 'user'],
                ]);
            })
            ->groupBy('teams.id')
            ->selectRaw('teams.id as team_id, COALESCE(COUNT(team_members.id), 0) as member_count')
            ->pluck('member_count', 'team_id')
            ->toArray();

        // sentTeam
        $pendingTeamAndMemberList = Team::join('team_members', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $user_id)
            ->where([
                ['team_members.status', 'pending'],
                ['team_members.actor', 'user'],
            ])
            ->select('teams.*', 'team_members.*')
            ->get();

        // invitations
        $teamMembersList = TeamMember::where('user_id', $user_id)->pluck('team_id')->unique();
        $invitedEventsList = EventInvitation::whereIn('team_id', $teamMembersList)
            ->with('event', 'event.tier', 'event.game', 'event.user')
            ->get();

        // dd($invitedTeamAndMemberList, $membersCount, $pendingTeamAndMemberList, $pendingTeamAndMemberList, $invitedEventsList);
        return view('Participant.ParticipantRequest', compact('membersCount', 'invitedTeamAndMemberList', 'pendingTeamAndMemberList', 'invitedEventsList'));
    }

    public function viewOwnProfile(Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            $user_id = $user?->id ?? null;

            return $this->viewProfile($request, $user_id, $user, true);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function viewProfileById(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $loggedInUser = Auth::user();

            if ($user->role == 'ORGANIZER') {
                return redirect()->route('public.organizer.view', ['id' => $id]);
            } elseif ($user->role == 'ADMIN') {
                return $this->showErrorParticipant('This is an admin view!');
            }

            return $this->viewProfile($request, $loggedInUser ? $loggedInUser->id : null, $user, false);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    private function viewProfile(Request $request, $logged_user_id, $userProfile, $isOwnProfile = true)
    {
        try {
            [
                'teamList' => $teamList,
                'teamIdList' => $teamIdList,
            ] = Team::getUserTeamList($userProfile->id);
            $pastTeam = Team::getUserPastTeamList($userProfile->id);

            $awardList = Team::getAwardListByTeamIdList($teamIdList);
            $achievementList = Team::getAchievementListByTeamIdList($teamIdList);
            $joinEvents = JoinEvent::getJoinEventsForTeamListWithEventsRosterResults($teamIdList);
            $totalEventsCount = $joinEvents->count();
            ['wins' => $wins, 'streak' => $streak] =
                JoinEvent::getJoinEventsWinCountForTeamList($teamIdList);

            $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            if ($logged_user_id) {
                $isFollowingOrganizerList = OrganizerFollow::getIsFollowing($logged_user_id, $userIds);
                $friend = Friend::checkFriendship($logged_user_id, $userProfile->id);
                $isFollowingParticipant = ParticipantFollow::checkFollow($logged_user_id, $userProfile->id);
            } else {
                $isFollowingOrganizerList = [];
                $friend = null;
                $isFollowingParticipant = null;
            }
            $joinEventsHistory = $joinEventsActive = $values = [];
            ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory]
                = JoinEvent::processEvents($joinEvents, $isFollowingOrganizerList);

            $joinEventIds = $joinEvents->pluck('id')->toArray();

            return view('Participant.PlayerProfile',
                compact('joinEvents', 'userProfile', 'teamList', 'isOwnProfile',
                    'joinEventsHistory', 'joinEventsActive', 'followCounts', 'totalEventsCount',
                    'wins', 'streak', 'awardList', 'achievementList', 'pastTeam', 'friend',
                    'isFollowingParticipant'
                )
            );
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }

    }

    public function editProfile(UpdateParticipantsRequest $request)
    {
        $validatedData = $request->validated();
        $participant = Participant::findOrFail($validatedData['participant']['id']);
        if (isset($validatedData['participant']['region']) && isset($validatedData['participant']['region']['value'])) {
            $validatedData['participant']['region'] = $validatedData['participant']['region']['value'];
        }
        
        $participant->update($validatedData['participant']);
        $user = User::findOrFail($validatedData['user']['id']);
        $user->update($validatedData['user']);
        if (isset($participant->region)) {
            $region = Country::select('emoji_flag', 'name', 'id')
                ->findOrFail($participant->region);
        } else {
            $region = null;
        }

        return response()->json([
            'message' => 'Participant updated successfully',
            'success' => true,
            'age' => $participant->age,
            'region' => $region,
        ], 200);
    }

    public function likeEvent(LikeRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->attributes->get('user');
        $existingLike = Like::where('user_id', $user->id)
            ->where('event_id', $validatedData['event_id'])
            ->first();

        if ($existingLike) {
            // dispatch(new HandleFollows('Unlike', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Unlike',
            // ]));

            $existingLike->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unliked the event',
                'isLiked' => false,
            ], 201);
        } else {
            Like::create([
                'user_id' => $user->id,
                'event_id' => $validatedData['event_id'],
            ]);

            // dispatch(new HandleFollows('Like', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Like',
            //     'log' => '<span class="notification-gray"> User'
            //     . ' <span class="notification-black">' . $user->name . '</span> started following '
            //     . ' <span class="notification-black">' . $organizer->name . '.</span> '
            //     . '</span>'
            // ]));

            return response()->json([
                'success' => true,
                'message' => 'Successfully liked the event',
                'isLiked' => true,
            ], 201);
        }
    }

    public function updateFriend(FriendRequest $request)
    {
        $user = $request->attributes->get('user');
        $validatedData = $request->validated();
        if (array_key_exists('deleteUserId', $validatedData)) {
            $friend = Friend::checkFriendship($validatedData['deleteUserId'], $user->id);
            $friend?->deleteOrFail();

            session()->flash('successMessage', 'Your request has been deleted.');

            return back();

        } elseif (array_key_exists('addUserId', $validatedData)) {

            try {

                Friend::create([
                    'user1_id' => $user->id,
                    'user2_id' => intval($validatedData['addUserId']),
                    'status' => 'pending',
                    'actor_id' => $user->id,
                ]);

                session()->flash('successMessage', 'Successfully created a friendship');

                return back();

            } catch (Exception $e) {
                if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                    $errorMessage = 'You have had a previous friend request!';
                } else {
                    $errorMessage = 'Your request to this participant failed!';
                }

                session()->flash('errorMessage', $errorMessage);

                return back();
            }
        } else {
            try {
                $friend = Friend::checkFriendship($validatedData['updateUserId'], $user->id)->firstOrFail();
                $status = $validatedData['updateStatus'];
                $isPermitted = $status == 'left';
                if (! $isPermitted) {
                    if ($status == 'accepted' || $status == 'rejected') {
                        $isPermitted = ($friend->status == 'pending' && $user->id != $friend->actor_id) ||
                        ($friend->status == 'left' && $user->id == $friend->actor_id) ||
                        ($friend->status == 'rejected' && $user->id == $friend->actor_id);
                    }
                }

                if (! $isPermitted) {
                    session()->flash('errorMessage', 'This request is not allowed.');

                    return back();
                }

                $friend->update([
                    'actor_id' => $user->id,
                    'status' => $status,
                ]);

                $message = [
                    'left' => 'Successfully removed friendship.',
                    'accepted' => 'Successfully accepted friendship request.',
                    'rejected' => 'Successfully rejected friendship request.',
                ];

                session()->flash('successMessage', $message[$status]);

                return back();
            } catch (Exception $e) {
                return $this->showErrorParticipant($e->getMessage());
            }
        }
    }

    public function followParticipant(Request $request)
    {

        $user = $request->attributes->get('user');
        $userId = $user->id;
        $participantId = $request->participant_id;
        $existingFollow = ParticipantFollow::checkFollow($user->id, $participantId);

        if ($existingFollow) {
            // dispatch(new HandleFollows('Unfollow', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Follow',
            // ]));

            $existingFollow->delete();

            $message = 'Successfully unfollowed the participant';
        } else {
            ParticipantFollow::create([
                'participant_follower' => $userId,
                'participant_followee' => $participantId,
            ]);
            
            $message = 'Successfully followed the participant';
            // dispatch(new HandleFollows('Unfollow', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Follow',
            //     'log' => '<span class="notification-gray"> User'
            //     . ' <span class="notification-black">' . $user->name . '</span> started following '
            //     . ' <span class="notification-black">' . $organizer->name . '.</span> '
            //     . '</span>'
            // ]));
        }
        
        session()->flash('successMessage', $message);
        return back();
    }
}
