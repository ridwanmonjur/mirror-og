<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\FriendRequest;
use App\Http\Requests\LikeRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Models\EventInvitation;
use App\Models\Follow;
use App\Models\Friend;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $view = view('Participant.MemberManagementPartials.MemberManagementScroll', $outputArray)->render();

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
        $user = $request->attributes->get('user');
        $user_id = $user?->id ?? null;

        return $this->viewProfile($request, $user_id, $user, true);
    }

    public function viewProfileById(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $loggedInUser = Auth::user();

        if ($user->role != 'PARTICIPANT') {
            return redirect()->route('public.organizer.view', ['id' => $id]);
        }

        return $this->viewProfile($request, $loggedInUser ? $loggedInUser->id : null, $user, false);
    }

    private function viewProfile(Request $request, $logged_user_id, $userProfile, $isOwnProfile = true)
    {

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
        $followCounts = Follow::getFollowCounts($userIds);
        if ($logged_user_id) {
            $isFollowing = Follow::getIsFollowing($logged_user_id, $userIds);
        } else {
            $isFollowing = [];
        }
        $joinEventsHistory = $joinEventsActive = $values = [];
        ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory]
            = JoinEvent::processEvents($joinEvents, $isFollowing);

        $joinEventIds = $joinEvents->pluck('id')->toArray();

        return view('Participant.PlayerProfile',
            compact('joinEvents', 'userProfile', 'teamList', 'isOwnProfile',
                'joinEventsHistory', 'joinEventsActive', 'followCounts', 'totalEventsCount',
                'wins', 'streak', 'awardList', 'achievementList', 'pastTeam'
            )
        );

    }

    public function editProfile(UpdateParticipantsRequest $request)
    {
        $participant = Participant::findOrFail($request->validated()['id']);
        $participant->update($request->validated());

        return response()->json([
            'message' => 'Participant updated successfully',
            'success' => true,
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
        if (array_key_exists('delete', $validatedData)) {
            Friend::where($validatedData['add.user1_id'])->delete();

            return response()->json(['success' => true, 'message' => 'Friend withdrawn']);
        } elseif (array_key_exists('add', $validatedData)) {
            try {
                Friend::create([
                    'user1_id' => $user->id,
                    'user2_id' => $validatedData['add.user1_id'],
                    'status' => 'pending',
                    'actor_id' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully send a friend request',
                ], 201);
            } catch (Exception $e) {
                if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                    $errorMessage = 'You have had a previous friend request!';
                } else {
                    $errorMessage = 'Your request to this participant failed!';
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 400);

            }
        } else {
            try {
                $friend = Friend::findOrFail($validatedData['update.id']);
                $status = $validatedData['update.status'];
                $isPermitted = $status == 'left';
                if (! $isPermitted) {
                    if ($status == 'accepted' || $status == 'rejected') {
                        $isPermitted = $friend->status == 'pending' && $user->id != $friend->actor_id;
                    }
                }

                if (! $isPermitted) {
                    return response()->json(['success' => false, 'message' => 'This request is not allowed.'], 400);
                }

                $friend->update([
                    'actor_id' => $user->id,
                    'status' => $status,
                ]);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
        }
    }
}
