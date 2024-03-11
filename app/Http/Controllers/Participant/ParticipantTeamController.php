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
    public function teamMemberManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where('creator_id', $user_id)
            ->with('members')->first();
        // dd($selectTeam);
        if ($selectTeam) {
            $teamMembers = $selectTeam->members;
            $teamMembersProcessed = TeamMember::processStatus($teamMembers);
            $creator_id = $selectTeam->creator_id;
            return view('Participant.MemberManagement', 
                compact('selectTeam', 'teamMembersProcessed', 'creator_id')
            );
        } else {
            return $this->show404Participant('You need to be a member to view events!');
        }
    }

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
            [
                'count' => $count,
            ] = Team::getUserTeamListAndCount($user_id);

            if ($count < 5) {
                $existingTeam = Team::where('teamName', $request->input('teamName'))->exists(); 
            
                if ($existingTeam) {
                    throw ValidationException::withMessages(['teamName' => 'Team name already exists. Please choose a different name.']);
                }
                
                $this->validateAndSaveTeam($request, $team, $user_id);
                TeamMember::bulkCreateTeanMembers($team->id, [$user_id], 'accepted');
                return redirect()->route('participant.team.view', ['id' => $user_id]);
            } else  {
                return back()->with('errorMessage', "You can't create more than 5 teams!");
            }
            
        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage());
        }
    }

    public function teamEditStore(Request $request, $id)
    {
        try {
            $team = Team::findOrFail($id);
            $user_id = $request->attributes->get('user')->id;
            $existingTeam = Team::where('teamName', $request->input('teamName'))->get(); 
            $isError = false;
            if (isset($existingTeam[0])) {
                $isError = $existingTeam->id != $team->id;
            } else {
                
            } 

            if ($isError) {
                throw ValidationException::withMessages(['teamName' => 'Team name already exists. Please choose a different name.']);
            } else {
                $this->validateAndSaveTeam($request, $team, $user_id);
                return redirect()->route('participant.team.view', ['id' => $user_id]);
            }
            
        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage());
        }
    }

 

   
}
