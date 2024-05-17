<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\RosterCaptain;
use App\Models\RosterMember;
use Illuminate\Database\QueryException as DatabaseQueryException;
use Illuminate\Http\Request;

class ParticipantRosterController extends Controller
{
    public function approveRosterMember(Request $request)
    {
        try {
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
        } catch (DatabaseQueryException $e) {
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                return response()->json(['success' => false, 'message' => 'Failed to update data: Duplicate entry', 'error' => $e->getMessage()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
            }
        }
    }

    public function disapproveRosterMember(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'join_events_id' => 'required',
                'team_member_id' => 'required',
                'teams_id' => 'required',
            ]);

            $user_id = $request->input('user_id');
            $join_events_id = $request->input('join_events_id');
            $team_member_id = $request->input('team_member_id');

            $member = RosterMember::where([
                'user_id' => $user_id,
                'join_events_id' => $join_events_id,
                'team_member_id' => $team_member_id,
            ])->first();
            if ($member) {
                $member->delete();
            }

            $captain = RosterCaptain::where([
                'join_events_id' => $request->join_events_id,
                'team_member_id' => $request->team_member_id,
                'teams_id' => $request->teams_id,
            ])->first();
            if ($captain) {
                $captain->delete();
            }

            return response()->json(['success' => true, 'message' => 'Roster status deleted']);
        } catch (DatabaseQueryException $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
        }
    }

    public function captainRosterMember(Request $request)
    {
        try {
            $request->validate([
                'join_events_id' => 'required',
                'team_member_id' => 'required',
                'teams_id' => 'required',
            ]);

            $captain = RosterCaptain::where([
                'join_events_id' => $request->join_events_id,
                'teams_id' => $request->teams_id,
            ])->first();
            if ($captain) {
                $captain->delete();
            }

            RosterCaptain::insert([
                'join_events_id' => $request->join_events_id,
                'team_member_id' => $request->team_member_id,
                'teams_id' => $request->teams_id,
            ]);

            return response()->json(['success' => true, 'message' => 'Roster captain created']);
        } catch (DatabaseQueryException $e) {
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                return response()->json(['success' => false, 'message' => 'Failed to update data: Duplicate entry', 'error' => $e->getMessage()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
            }
        }
    }

    public function deleteCaptainRosterMember(Request $request)
    {
        try {
            $request->validate([
                'join_events_id' => 'required',
                'team_member_id' => 'required',
                'teams_id' => 'required',
            ]);

            $captain = RosterCaptain::where([
                'join_events_id' => $request->join_events_id,
                'team_member_id' => $request->team_member_id,
                'teams_id' => $request->teams_id,
            ])->first();
            if ($captain) {
                $captain->delete();
            }

            return response()->json(['success' => true, 'message' => 'Roster captain deleted']);
        } catch (DatabaseQueryException $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
        }
    }
}
