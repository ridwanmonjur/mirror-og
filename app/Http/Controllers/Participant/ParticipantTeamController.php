<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\EventDetail;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\TeamMember;
use App\Models\Participant;
use App\Models\RosterMember;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class ParticipantTeamController extends Controller
{
    public function teamMemberManagement(Request $request, $id)
    {
        $page = 5;
        $user = $request->attributes->get('user');
        if ($user) {
            $user_id = $user->id;
        } else {
            $user = auth()->user();
            $user_id = $user->id;
        }

        $selectTeam = Team::where('id', $id)->where('creator_id', $user_id)
            ->with('members')->first();
        
        if ($selectTeam) {
            $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
            $teamMembers = $selectTeam->members;
            $teamMembersProcessed = TeamMember::processStatus($teamMembers);
            $creator_id = $selectTeam->creator_id;
            $userList = User::where('role', 'PARTICIPANT')->with([
                'members' => function ($q) use ($id) {
                    $q->where('team_id', $id);
                }
            ])->paginate($page);

            foreach ($userList as $user) {
                $user->is_in_team = isset($user->members[0]) ? 'yes' : 'no';
            }

            return view('Participant.MemberManagement', 
                compact('selectTeam', 'teamMembersProcessed', 'creator_id', 'captain', 'userList')
            );
        } else {
            return $this->show404Participant('This event is missing or you need to be a member to view events!');
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

    public function inviteMember(Request $request, $id, $userId)
    {
        TeamMember::insert([
            'user_id' => $userId,
            'team_id' => $id,
            'status' => 'invited'
        ]);
        
        return response()->json(['success' => true, 'message' => 'Team member created'], 201);
    }

    public function deleteInviteMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if ($member) {
            $member->delete();
            return response()->json(['success' => true, 'message' => 'Team member invitation withdrawn']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        }
    }

    public function rejectInviteMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if ($member) {
            $member->status = 'rejected';
            $member->rejector = 'invitee';
            $member->save();
            return response()->json(['success' => true, 'message' => 'Team member invitation withdrawn']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        }
    }

    public function approveTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if (!$member || $member->rejector != 'team') {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        } else {
            $member->status = 'accepted';
            $member->rejector = null;
            $member->save();
            return response()->json(['success' => true, 'message' => 'Team member status updated to accepted']);
        }
    }

    public function pendingTeamMember(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $member = TeamMember::where('team_id', $id)->where('user_id', $user->id)->first();
        if (!$member) {
            TeamMember::insert([
                'user_id' => $user->id,
                'team_id' => $id,
                'status' => 'pending'
            ]);

            return redirect()->back()->with('successJoin', 'Your request to this team was successful!');
        } else {
            return redirect()->back()->with('errorJoin', 'Your request to this team failed!');
        }
    }

    public function disapproveTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        $team = Team::where('id', $member->team_id)->first();
        if ($member && $team) {
            if ($team->creator_id == $member->user_id) {
                return response()->json(['success' => false, 'message' => "Can't remove creator from team."], 400);
            }

            $member->status = 'rejected';
            $member->rejector = 'team';
            $member->save();
            return response()->json(['success' => true, 'message' => 'Team member status updated to rejected']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        }
    }

    public function captainMember(Request $request, $id, $memberId)
    {
        $existingCaptain = TeamCaptain::where('teams_id', $id)
            ->first();
        
        if ($existingCaptain) {
            $existingCaptain->delete();
        } 
        
        TeamCaptain::insert([
            'teams_id' => $id,
            'team_member_id' => $memberId,
        ]);
        
        return response()->json(['success' => 'true'], 200);
    }

    public function deleteCaptain(Request $request, $id, $memberId)
    {
        $existingCaptain = TeamCaptain::where('teams_id', $id)
            ->where('team_member_id', $memberId)
            ->first();

        if ($existingCaptain) {
            $existingCaptain->delete();
        } 
        
        return response()->json(['success' => 'true'], 200);
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

        return $team;
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
                
                $team = $this->validateAndSaveTeam($request, $team, $user_id);
                
                TeamCaptain::create([
                    'userID' => $user_id,
                    'team_id' => $team->id,
                ]);
                
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
            $existingTeam = Team::where('teamName', $request->input('teamName'))->first(); 
            
            if (isset($existingTeam)) {
                if ($existingTeam['id'] != $team->id) {
                    throw ValidationException::withMessages(['teamName' => 'Team name already exists. Please choose a different name.']);
                } 
            }

            $this->validateAndSaveTeam($request, $team, $user_id);
            return redirect()->route('participant.team.view', ['id' => $user_id]);
            
        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage());
        }
    }

    public function replaceBanner(Request $request, $id) {
        try {
            $request->validate([
                'file' => 'required|file'
            ]);
            $team = Team::findOrFail($id);
            Team::destroyTeanBanner($team->teamBanner);
            $file = $request->file('file');
            $fileNameInitial = 'teamBanner-' . time() . '.' . $file->getClientOriginalExtension();
            $fileName = "images/team/$fileNameInitial";
            $file->storeAs('images/team/', $fileNameInitial);
            $team->teamBanner = $fileName;
            $fileName = asset('/storage'. '/'. $fileName);
            $team->save();
            return response()->json(['success' => true, 'message' => 'Succeeded', 'data' => compact('fileName')], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

   
}
