<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\RosterMember;

use Illuminate\Http\Request;

class ParticipantTeamController extends Controller
{
    public function approveTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);

        if ($member && $member->status === 'pending') {
            $member->status = 'accepted';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Member status updated to accepted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or member not found'], 400);
        }
    }

    public function approveRosterMember(Request $request, $id)
    {
        $member = TeamMember::find($id);

        if ($member && $member->status === 'pending') {
            $member->status = 'accepted';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Member status updated to accepted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or member not found'], 400);
        }
    }

    public function disapproveTeamMember(Request $request, $id)
    {
        $member = RosterMember::find($id);

        if ($member && $member->status != 'rejected') {
            $member->status = 'rejected';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Member status updated to rejected']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or member not found'], 400);
        }
    }

    public function disapproveRosterMember(Request $request, $id)
    {
        $member = RosterMember::find($id);

        if ($member && $member->status !== 'rejected') {
            $member->status = 'accepted';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Member status updated to rejected']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or member not found'], 400);
        }
    }

}
