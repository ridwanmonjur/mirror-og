<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FriendRequest;
use App\Http\Requests\User\LikeRequest;
use App\Http\Requests\User\UpdateParticipantsRequest;
use App\Models\ActivityLogs;
use App\Models\EventInvitation;
use App\Models\EventJoinResults;
use App\Models\Friend;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\OrganizerFollow;
use App\Models\Participant;
use App\Models\ParticipantFollow;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;

class ParticipantController extends Controller
{
    public function searchParticipant(Request $request)
    {
        try {
            $page = 5;
            $userList = User::getParticipants($request)->paginate($page);
            foreach ($userList as $user) {
                // @phpstan-ignore-next-line
                $user->is_in_team = $user->members->isNotEmpty();
            }

            return response()->json(['data' => $userList, 'success' => true]);
        } catch (Exception $e) {
            return response()->json(['data' => [], 'success' => false, 'error' => $e->getMessage()], 400);
        }
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

            if ($user->role === 'ORGANIZER') {
                return redirect()->route('public.organizer.view', ['id' => $id]);
            }
            if ($user->role === 'ADMIN') {
                return $this->showErrorParticipant('This is an admin view!');
            }

            return $this->viewProfile($request, $loggedInUser ? $loggedInUser->id : null, $user, false);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function editProfile(UpdateParticipantsRequest $request)
    {
        $validatedData = $request->validated();
        $participant = Participant::findOrFail($validatedData['participant']['id']);
        $participant->update($validatedData['participant']);
        $user = User::findOrFail($validatedData['user']['id']);
        $user->update($validatedData['user']);
        $user->uploadUserBanner($request);
       
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

    private function viewProfile(Request $request, $logged_user_id, $userProfile, $isOwnProfile = true)
    {
        try {
            [
                'teamList' => $teamList,
                'teamIdList' => $teamIdList,
            ] = Team::getUserTeamList($userProfile->id);
            $pastTeam = Team::getUserPastTeamList($userProfile->id);

            $joinEvents = JoinEvent::getJoinEventsForTeamListWithEventsRosterResults($teamIdList);
            $totalEventsCount = $joinEvents->count();
            ['wins' => $wins, 'streak' => $streak] =
                JoinEvent::getPlayerJoinEventsWinCountForTeamList($teamIdList,  $userProfile->id);

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
            $joinEventAndTeamList = EventJoinResults::getEventJoinListResults($joinEventIds);

            return view(
                'Participant.PlayerProfile',
                compact(
                    'joinEvents', 
                    'userProfile',
                    'teamList',
                    'isOwnProfile',
                    'joinEventsHistory',
                    'joinEventsActive',
                    'followCounts',
                    'totalEventsCount',
                    'wins',
                    'streak',
                    'joinEventAndTeamList',
                    'pastTeam',
                    'friend',
                    'isFollowingParticipant'
                )
            );
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function getActivityLogs(Request $request) {
        $userId = $request->input('userId');
        $duration = $request->input('duration');
        $page = $request->input('page', 1);
        $perPage = 5;

        $activityLogs = ActivityLogs::retrievePaginatedActivityLogs($userId,
             $duration, 
             $perPage, 
             $page
        );
        
        return response()->json([
            'items' => $activityLogs->items(),
            'hasMore' => $activityLogs->hasMorePages()
        ]);
    }
}
