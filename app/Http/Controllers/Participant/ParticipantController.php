<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function searchParticipant(Request $request)
    {
        $teamId = $request->teamId;
        $page = 5;
        $userList = User::where('role', 'PARTICIPANT')->with([
                'members' => function ($q) use ($teamId) {
                $q->where('team_id', $teamId);
            }
        ])->paginate($page);

        foreach ($userList as $user) {
            $user->is_in_team = $user->members->isNotEmpty() ? 'yes' : 'no';
        }
       
        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        $view = view('Organizer.ManageEvent.ManageEventScroll', $outputArray)->render();
        return response()->json(['html' => $view]);
    }

}
