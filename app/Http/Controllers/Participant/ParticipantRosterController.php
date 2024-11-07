<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\JoinEvent;
use App\Models\RosterCaptain;
use App\Models\RosterMember;
use App\Models\Team;
use Illuminate\Database\QueryException as DatabaseQueryException;
use Illuminate\Http\Request;

class ParticipantRosterController extends Controller
{
    public function rosterMemberManagement(Request $request, $id, $teamId)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $teamId)
            ->whereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 'accepted');
            })
            ->first();

        $joinEvent = JoinEvent::where('team_id', intval($teamId))->where('event_details_id', intval($id))->first();

        if ($selectTeam && $joinEvent) {
            $captain = RosterCaptain::where('join_events_id', $joinEvent->id)->first();
            $creator_id = $selectTeam->creator_id;
            $teamMembers = $selectTeam->members->where('status', 'accepted');
            $memberIds = $teamMembers->pluck('id')->toArray();
            $rosterMembers = RosterMember::whereIn('team_member_id', $memberIds)
                ->where('join_events_id', $joinEvent->id)->get();

            $rosterMembersKeyedByMemberId = RosterMember::keyByMemberId($rosterMembers);
            $isRedirect = $request->redirect === 'true';

            return view(
                'Participant.RosterManagement',
                compact(
                    'selectTeam',
                    'joinEvent',
                    'teamMembers',
                    'creator_id',
                    'isRedirect',
                    'rosterMembersKeyedByMemberId',
                    'rosterMembers',
                    'id',
                    'captain'
                )
            );
        }
        return $this->showErrorParticipant('This event is missing or you need to be a member to view events!');
    }

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
                'team_id' => $request->team_id,
            ]);

            return response()->json(['success' => true, 'message' => 'Roster status updated to accepted']);
        } catch (DatabaseQueryException $e) {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return response()->json(['success' => false, 'message' => 'Failed to update data: Duplicate entry', 'error' => $e->getMessage()]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
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
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return response()->json(['success' => false, 'message' => 'Failed to update data: Duplicate entry', 'error' => $e->getMessage()]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
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
