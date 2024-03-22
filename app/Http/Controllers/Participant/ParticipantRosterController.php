<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\RosterCaptain;
use App\Models\RosterMember;
use Illuminate\Http\Request;

class ParticipantRosterController extends Controller
{
    public function approveRosterMember(Request $request)
    {   
        $request->validate([
            'user_id' => 'required',
            'join_events_id' => 'required',
            'team_member_id' => 'required',
        ]);

        RosterMember::insert([
            'user_id' => $request->user_id,
            'join_events_id' => $request->join_events_id,
            'team_member_id' => $request->team_member_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Roster status updated to accepted']);
    }

    public function disapproveRosterMember(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'join_events_id' => 'required',
            'team_member_id' => 'required',
        ]);

        $user_id = $request->input('user_id');
        $join_events_id = $request->input('join_events_id');
        $team_member_id = $request->input('team_member_id');
    
        RosterMember::where([
            'user_id' => $user_id,
            'join_events_id' => $join_events_id,
            'team_member_id' => $team_member_id,
        ])->delete();

        return response()->json(['success' => true, 'message' => 'Roster status deleted']);
    }

    public function captainRosterMember(Request $request)
    {
        $request->validate([
            'join_events_id' => 'required',
            'team_member_id' => 'required',
        ]);

        RosterCaptain::insert([
            'join_events_id' => $request->join_events_id,
            'team_member_id' => $request->team_member_id,
        ]);
           
        return response()->json(['success' => true, 'message' => 'Roster captain created']);
    }

    public function deleteCaptainRosterMember(Request $request)
    {
        $request->validate([
            'join_events_id' => 'required',
            'team_member_id' => 'required',
        ]);

        RosterCaptain::find([
            'join_events_id' => $request->join_events_id,
            'team_member_id' => $request->team_member_id,
        ])->delete();
           
        return response()->json(['success' => true, 'message' => 'Roster captain deleted']);
    }

}
