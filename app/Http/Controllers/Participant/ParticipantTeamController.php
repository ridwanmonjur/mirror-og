<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\OrganizerFollow;
use App\Models\JoinEvent;
use App\Models\RosterCaptain;
use App\Models\RosterMember;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Io238\ISOCountries\Models\Country;

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
        } else {
            $membersCount = 0;
            $count = 0;
        }

        if ($request->expectsJson) {
            return response()->json(['data' => [
                'teamList' => $teamList, 'count' => $count,  'membersCount' => $membersCount
            ], 'sucess' => true], 200);
        } else {
            return view('Participant.TeamList', compact('teamList', 'count', 'membersCount'));
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
            ->with(['members' => function ($query) {
                $query->where('status', 'accepted')
                    ->with('user', 'user.participant');
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
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            if ($user_id) {
                $isFollowing = OrganizerFollow::getIsFollowing($user_id, $userIds);
            } else {
                $isFollowing = [];
            }

            $joinEventsHistory = $joinEventsActive = $values = [];
            ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory]
                = JoinEvent::processEvents($joinEvents, $isFollowing);
            // dd($joinEvents, $activeEvents, $historyEvents);

            $joinEventIds = $joinEvents->pluck('id')->toArray();

            return view('Participant.TeamManagement',
                compact('selectTeam', 'joinEvents', 'captain',
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
        $teamMembersProcessed = TeamMember::getProcessedTeamMembers($selectTeam->id);
        $creator_id = $selectTeam->creator_id;
        $userList = [];
        return view('Participant.MemberManagement', compact('selectTeam', 'redirect', 
            'teamMembersProcessed', 'creator_id', 'id', 'captain', 'userList'
        ));
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
                compact('selectTeam', 'joinEvent', 'teamMembers', 'creator_id', 
                'rosterMembersKeyedByMemberId', 'rosterMembers', 'id', 'captain'
            ));
        } else {
            return $this->showErrorParticipant('This event is missing or you need to be a member to view events!');
        }
    }

    public function createTeamView()
    {
        return view('Participant.CreateTeam');
    }

    public function editTeam(UpdateTeamRequest $request)
    {
        try{
            $validatedData = $request->validated();
            if (isset($validatedData['country']) &&  isset($validatedData['country']['value'])) {
                $validatedData['country'] = $validatedData['country']['value'];
            }

            $team = Team::findOrFail($request['id']);
            $team->update($validatedData);
            if (isset($team->country)) {
                $country = Country::select('emoji_flag', 'name', 'id')
                    ->findOrFail($team->country);
            } else {
                $country = null;
            }

            return response()->json([
                'message' => 'Team updated successfully',
                'success' => true,
                'country' => $country,
            ], 200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['success' => false, 'message' => $errorMessage], 403);
        }
    }

    public function inviteMember(Request $request, $id, $userId)
    {
        try {
            TeamMember::create([
                'user_id' => $userId,
                'team_id' => $id,
                'status' => 'pending',
                'actor' => 'team',
            ]);
        } catch (Exception $e) {
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                $errorMessage = 'You have had a previous pending invitation or successful member!';
            } else {
                $errorMessage = 'Your request to this participant failed!';
            }

            return response()->json(['success' => false, 'message' => $errorMessage], 403);
        }

        return response()->json(['success' => true, 'message' => 'Team member invited'], 201);
    }

    public function pendingTeamMember(Request $request, $id)
    {
        try {
            $user = $request->attributes->get('user');
            TeamMember::create([
                'user_id' => $user->id,
                'team_id' => $id,
                'status' => 'pending',
                'actor' => 'user',
            ]);

            return redirect()->back()->with('successJoin', 'Your request to this team was sent!');
        } catch (Exception $e) {
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                $errorMessage = 'You have requested before!';
            } else {
                $errorMessage = 'Your request to this team failed!';
            }

            return redirect()->back()->with('errorJoin', $errorMessage);
        }
    }

    public function withdrawInviteMember(Request $request, $id)
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
            $member->actor = 'user';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Team member invitation withdrawn']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
        }
    }

    public function updateTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if (! $member) {
            return response()->json(['success' => false, 'message' => 'Team member not found'], 400);
        }

        $status = $request->status;
        $isPermitted = $status == 'left';
        if (! $isPermitted) {
            if ($status == 'accepted' || $status == 'rejected') {
                $isPermitted = $member->status == 'pending' && $request->actor != $member->actor;
            }
        }

        if (! $isPermitted) {
            return response()->json(['success' => false, 'message' => 'This request is not allowed.'], 400);
        }

        $team = Team::where('id', $member->team_id)->first();

        if ($team->creator_id == $member->user_id) {
            return response()->json(['success' => false, 'message' => "Can't modify creator of the team"], 400);
        }

        $member->status = $status;
        $member->actor = $request->actor;
        $member->save();

        return response()->json(['success' => true, 'message' => "Team member status updated to $status"]);
    }

    public function captainMember(Request $request, $id, $memberId)
    {
        try {
            $existingCaptain = TeamCaptain::where('teams_id', $id)->first();
            if ($existingCaptain) {
                $existingCaptain->delete();
            }

            TeamCaptain::insert([
                'teams_id' => $id,
                'team_member_id' => $memberId,
            ]);

            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

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
            'teamDescription' => 'required',
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
            } else {
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
                        'teamName' => 'Team name already exists. Please choose a different name.',
                    ]);
                }
            }

            $this->validateAndSaveTeam($request, $team, $user_id);

            return redirect()->route('participant.team.view', ['id' => $user_id]);

        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage());
        }
    }

    public function replaceBanner(Request $request, $id)
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $team = Team::findOrFail($id);
            $oldBanner = $team->teamBanner;
            $fileName = $team->uploadTeamBanner($request);
            Team::destroyTeanBanner($oldBanner);

            return response()->json(['success' => true, 'message' => 'Succeeded', 'data' => compact('fileName')], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
