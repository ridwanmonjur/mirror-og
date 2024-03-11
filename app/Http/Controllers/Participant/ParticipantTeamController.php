<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Team;
use App\Models\Captain;
use App\Models\EventDetail;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\TeamMember;
use App\Models\Participant;
use App\Models\RosterMember;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class ParticipantTeamController extends Controller
{

    public function createTeamView()
    {
        return view('Participant.CreateTeam');
    }

    public function editTeamView($id)
    {
        $team = Team::findOrFail($id);
        
        return view('Participant.EditTeam', [
            'team'=> $team
        ]);
    }

    public function approveTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);

        if ($member) {
            $member->status = 'accepted';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Team member status updated to accepted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        }
    }

    public function approveRosterMember(Request $request, $id)
    {
        $member = RosterMember::find($id);

        if ($member) {
            $member->status = 'accepted';
            try {
                $member->save();
            } catch (\Exception $e) {
                // Log or handle the exception
                dd($member, $e);
            }

            return response()->json(['success' => true, 'message' => 'Roster status updated to accepted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or roster member not found'], 400);
        }
    }

    public function disapproveTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);

        if ($member) {
            $member->status = 'rejected';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Team member status updated to rejected']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        }
    }

    public function disapproveRosterMember(Request $request, $id)
    {
        $member = RosterMember::find($id);

        if ($member) {
            $member->status = 'rejected';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Roster member status updated to rejected']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or roster member not found'], 400);
        }
    }

    private function validateAndSaveTeam($request, $team, $user_id)
    {
        $request->validate([
            'teamName' => 'required|string|max:25',
            'teamDescription' => 'required'
        ]);

        $existingTeam = Team::where('teamName', $team->teamName)->first();
        
        if ($existingTeam && $team->id == $existingTeam->id) {
            throw ValidationException::withMessages(['teamName' => 'Team name already exists. Please choose a different name.']);
        }

        $team->teamName = $request->input('teamName');
        $team->teamDescription = $request->input('teamDescription');
        $team->creator_id = $user_id;
        $team->save();
    }

    public function teamStore(Request $request)
    {
        try {
            $team = new Team();
            $user_id = $request->attributes->get('user')->id;
            $this->validateAndSaveTeam($request, $team, $user_id);
            TeamMember::bulkCreateTeanMembers($team->id, [$user_id], 'accepted');
            return redirect()->route('participant.team.view', ['id' => $user_id]);
        } catch (Exception $e) {
            return $this->show404Participant($e->getMessage());
        }
    }

    public function teamEditStore(Request $request, $id)
    {
        try {
            $team = Team::findOrFail($id);
            $user_id = $request->attributes->get('user')->id;
            $this->validateAndSaveTeam($request, $team, $user_id);
            return redirect()->route('participant.team.view', ['id' => $user_id]);
        } catch (Exception $e) {
            return $this->show404Participant($e->getMessage());
        }
    }

 

   
}
