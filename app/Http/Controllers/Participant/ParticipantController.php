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
        $participant_id = $user->participant->id;

        // pending requests
        $invitedTeamList = Team::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'invited');
        })
            ->get();

        $teamIdList = $invitedTeamList->pluck('id')->toArray();
        $membersCount = TeamMember::whereIn('team_id', $teamIdList)
            ->where('status', 'accepted')
            ->groupBy('team_id')
            ->select('team_id', DB::raw('COUNT(*) as member_count'))
            ->get()
            ->pluck('member_count', 'team_id');

        // sentTeam
        $pendingTeamList = Team::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'pending');
        })
            ->with(['members' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->get();

        // invitations
        $invitedEventsList = EventInvitation::where('participant_id', $participant_id)  
            ->with('event')
            ->get();

        // dd($invitedTeamList, $pendingTeamList, $pendingTeamList, $invitedEventsList);

        return view('Participant.ParticipantRequest', compact('membersCount', 'invitedTeamList', 'pendingTeamList', 'invitedEventsList'));
    }
}
