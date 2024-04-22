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
use App\Models\RosterCaptain;
use App\Models\RosterMember;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class ParticipantTeamController extends Controller
{

    public function teamList(Request $request)
    {
        $user_id = $request->attributes->get('user')->id;
        [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ] = Team::getUserTeamListAndPluckIds($user_id);
        
        if ($teamIdList) {
            $membersCount = Team::getTeamMembersCountForEachTeam($teamIdList);
            $count = $teamList->count();
            return view('Participant.TeamList', compact('teamList', 'count', 'membersCount' ));
        } else {
            session()->flash('errorMessage', 'You have 0 teams! Create a team first.');
            $membersCount = 0;
            $count = 0;
            return view('Participant.TeamList', compact('teamList', 'count', 'membersCount' ));
        }
    }
    

    public function teamManagement(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        if (is_null($user)) {
            $user = Auth::user(); 
        } 

        $user_id = $user?->id ?? null;
        $selectTeam = Team::where('id', $id)
            ->with(['members' => function($query) {
                $query->where('status', 'accepted')
                    ->with('user');
            }])->first();        
        // dd($selectTeam);
        if ($selectTeam) {
            $awardList = $selectTeam->getAwardListByTeam();
            $achievementList = $selectTeam->getAchievementListByTeam();
            $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
            $joinEvents = JoinEvent::getJoinEventsForTeamWithEventsRosterResults($selectTeam->id);
            $totalEventsCount = $joinEvents->count();
            ['wins' => $wins, 'streak' => $streak] = 
                JoinEvent::getJoinEventsWinCountForTeam($selectTeam->id);
            
            $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
            $followCounts = Follow::getFollowCounts($userIds);
            if ($user_id) {
                $isFollowing = Follow::getIsFollowing($user_id, $userIds);
            } else {
                $isFollowing = [];
            }

            $joinEventsHistory = $joinEventsActive = $values = [];
            ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory] 
                = JoinEvent::processEvents($joinEvents, $isFollowing);
            // dd($joinEvents, $activeEvents, $historyEvents);

            $joinEventIds = $joinEvents->pluck('id')->toArray();
            $teamMembers = $selectTeam->members;

            return view('Participant.TeamManagement', 
                compact('selectTeam', 'joinEvents', 'captain', 'teamMembers',
                    'joinEventsHistory', 'joinEventsActive', 'followCounts', 'totalEventsCount',
                    'wins', 'streak', 'awardList', 'achievementList'
                )
            );
        } else {
            return $this->showErrorParticipant('This event is missing or cannot be retrieved!');
        }
    }

    public function teamMemberManagementRedirected(Request $request, $id, $teamId)
    {
        $page = 5;
        $user = $request->attributes->get('user') ?? auth()->user();
        $selectTeam = Team::where('id', $teamId)
            ->where('creator_id', $user->id)->with('members')->first();
        if ($selectTeam) {
            return $this->handleTeamManagement($selectTeam, $id, $request, $page, true);
        } else {
            return redirect()->route('participant.team.manage', ['id' => $id]);
        }
    }

    public function teamMemberManagement(Request $request, $id)
    {
        $page = 5;
        $user = $request->attributes->get('user') ?? auth()->user();
        $user_id = $user->id;
        $selectTeam = Team::where('id', $id)
            ->where('creator_id', $user_id)->with('members')->first();
        if ($selectTeam) {
            return $this->handleTeamManagement($selectTeam, $id, $request, $page, false);
        } else {
            return $this->showErrorParticipant('This event is missing or you need to be a member to view events!');
        }
    }
    
    protected function handleTeamManagement($selectTeam, $id, $request, $page, $redirect = false)
    {
        $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
        $teamMembersProcessed = TeamMember::processStatus($selectTeam->members);
        $creator_id = $selectTeam->creator_id;
        $userList = User::getParticipants($request, $selectTeam->id)->paginate($page);
        foreach ($userList as $user) {
            $user->is_in_team = $user->members->isNotEmpty() ? 'yes' : 'no';
        }

        return view('Participant.MemberManagement', compact('selectTeam', 'redirect', 'teamMembersProcessed', 'creator_id', 'userList', 'id', 'captain'));
    }

    public function rosterMemberManagement(Request $request, $id, $teamId)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $teamId)->where('creator_id', $user_id)
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

            return view('Participant.RosterManagement', 
                compact('selectTeam', 'joinEvent', 'teamMembers', 'creator_id', 'rosterMembersKeyedByMemberId', 'rosterMembers', 'id', 'captain')
            );
        } else {
            return $this->showErrorParticipant('This event is missing or you need to be a member to view events!');
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
        if (!$member || $member->rejector == 'invitee') {
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
        DB::transaction(function() use ($id, $memberId) {
            $existingCaptain = TeamCaptain::where('teams_id', $id)->first();
            if ($existingCaptain) {
                $existingCaptain->delete();
            } 
            
            TeamCaptain::insert([
                'teams_id' => $id,
                'team_member_id' => $memberId,
            ]);

            return response()->json(['success' => 'true'], 200);
        });
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
                TeamMember::bulkCreateTeanMembers($team->id, [$user_id], 'accepted');
                $teamMembers = $team->members;
            
                TeamCaptain::insert([
                    'team_member_id' => $teamMembers[0]->id,
                    'teams_id' => $team->id,
                ]);
                
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
                    throw ValidationException::withMessages([
                        'teamName' => 'Team name already exists. Please choose a different name.'
                    ]);
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
            $oldBanner = $team->teamBanner;
            $file = $request->file('file');
            $fileNameInitial = 'teamBanner-' . time() . '.' . $file->getClientOriginalExtension();
            $fileName = "images/team/$fileNameInitial";
            $file->storeAs('images/team/', $fileNameInitial);
            $team->teamBanner = $fileName;
            $fileName = asset('/storage'. '/'. $fileName);
            $team->save();
            Team::destroyTeanBanner($oldBanner);

            return response()->json(['success' => true, 'message' => 'Succeeded', 'data' => compact('fileName')], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
