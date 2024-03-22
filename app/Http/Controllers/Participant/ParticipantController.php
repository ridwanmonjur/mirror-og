<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use App\Models\EventInvitation;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
{
    public function searchParticipant(Request $request)
    {
        $teamId = $request->teamId;
        $selectTeam = Team::find($teamId);
        $page = 5;
        $userList = User::getParticipants($request, $teamId)
            ->paginate($page);

        foreach ($userList as $user) {
            $user->is_in_team = $user->members->isNotEmpty() ? 'yes' : 'no';
        }

        $outputArray = compact('userList', 'selectTeam');
        $view = view('Participant.MemberManagement.MemberManagementScroll', $outputArray)->render();
        return response()->json(['html' => $view]);
    }

    public function viewRequest(Request $request) {
        $user = $request->attributes->get('user');
        $user_id = $user->id;

        // pending requests
        $invitedTeamList = Team::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'invited');
        })
            ->get();

        $teamIdList = $invitedTeamList->pluck('id')->toArray();
        $membersCount = DB::table('teams')
            ->leftJoin('team_members', 'teams.id', '=', 'team_members.team_id')
            ->whereIn('teams.id', $teamIdList)
            ->where('team_members.status', 'accepted')
            ->groupBy('teams.id')
            ->selectRaw('teams.id as team_id, COALESCE(COUNT(team_members.id), 0) as member_count')
            ->pluck('member_count', 'team_id')
            ->toArray();
        // sentTeam
        $pendingTeamList = Team::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'pending');
        })
            ->with(['members' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->get();

        // invitations
        $invitedEventsList = EventInvitation::where('participant_user_id', $user_id)  
            ->with('event', 'event.tier', 'event.game', 'event.user')
            ->get();

        // dd($invitedTeamList, $membersCount, $pendingTeamList, $pendingTeamList, $invitedEventsList);

        return view('Participant.ParticipantRequest', compact('membersCount', 'invitedTeamList', 'pendingTeamList', 'invitedEventsList'));
    }

    public function viewProfile(Request $request) {
        return view('Participant.Profile');
    }
}
