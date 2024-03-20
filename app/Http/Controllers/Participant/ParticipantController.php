<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

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
        return view('Participant.ParticipantRequest');
    }
}
