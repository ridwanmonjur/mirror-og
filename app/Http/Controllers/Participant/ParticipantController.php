<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Models\ActivityLogs;
use App\Models\EventInvitation;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function viewRequest(Request $request) {
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

    public function viewOwnProfile(Request $request) {
        $user = $request->attributes->get('user');
        $user_id = $user?->id ?? null;
        return $this->viewProfile($request, $user_id, $user, true);
    }

    public function viewProfileById(Request $request, $id) {
        $user = User::findOrFail($id);
        return $this->viewProfile($request, $id, $user, false);
    }

    private function viewProfile(Request $request, $user_id, $userProfile, $isOwnProfile = true) {
   
        [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ] = Team::getUserTeamList($user_id);   
        $pastTeam = Team::getUserPastTeamList($user_id);

        $awardList = Team::getAwardListByTeamIdList($teamIdList);
        $achievementList = Team::getAchievementListByTeamIdList($teamIdList);
        $joinEvents = JoinEvent::getJoinEventsForTeamListWithEventsRosterResults($teamIdList);
        $totalEventsCount = $joinEvents->count();
        ['wins' => $wins, 'streak' => $streak] = 
            JoinEvent::getJoinEventsWinCountForTeamList($teamIdList);
        
        $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
        $followCounts = Follow::getFollowCounts($userIds);
        $isFollowing = Follow::getIsFollowing($user_id, $userIds);
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

    public function editProfile(UpdateParticipantsRequest $request) {
        $participant = Participant::findOrFail($request->validated()['id']);
        $participant->update($request->validated());
        return response()->json([
            'message' => 'Participant updated successfully', 
            'success' => true
        ], 200);
    }
}
