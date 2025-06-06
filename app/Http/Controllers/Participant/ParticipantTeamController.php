<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamSearchRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Jobs\HandleFollowsFriends;
use App\Models\EventJoinResults;
use App\Models\JoinEvent;
use App\Models\OrganizerFollow;
use App\Models\RosterCaptain;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Models\TeamProfile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
            return response()->json(
                [
                    'data' => [
                        'teamList' => $teamList,
                        'count' => $count,
                        'membersCount' => $membersCount,
                    ],
                    'sucess' => true,
                ],
                200,
            );
        }
        return view('Participant.TeamList2', compact('teamList', 'count', 'membersCount'));
    }

    public function teamManagement(Request $request, $id)
    {
        $user = Auth::user();

        $user_id = $user?->id ?? null;
        $selectTeam = Team::where('id', $id)
            ->with([
                'members' => function ($query) {
                    $query->where('status', 'accepted')->with('user', 'user.participant');
                },
            ])
            ->first();
        // dd($selectTeam);
        if ($selectTeam) {
            $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
            $joinEvents = JoinEvent::getJoinEventsForTeamWithEventsRosterResults($selectTeam->id);
            $totalEventsCount = $joinEvents->count();
            ['wins' => $wins, 'streak' => $streak] = JoinEvent::getJoinEventsWinCountForTeam($selectTeam->id);

            $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            $isFollowing = $user_id ? OrganizerFollow::getIsFollowing($user_id, $userIds) : [];

            $joinEventsHistory = $joinEventsActive = $values = [];
            ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory] = JoinEvent::processEvents($joinEvents, $isFollowing);
            // dd($joinEvents, $activeEvents, $historyEvents);

            $joinEventIds = $joinEvents->pluck('id')->toArray();
            $joinEventAndTeamList = EventJoinResults::getEventJoinListResults($joinEventIds);

            return view('Public.TeamProfile', compact('selectTeam', 'joinEvents', 'captain', 'joinEventsHistory', 'joinEventsActive', 'followCounts', 'joinEventAndTeamList', 'totalEventsCount', 'wins', 'streak'));
        }
        return $this->showErrorParticipant('This event is missing or cannot be retrieved!');
    }

    public function teamFollow(Request $request, $id)
    {
        try {
            $user = $request->attributes->get('user');
            $selectTeam = Team::getTeamAndMembersByTeamId($id);

            $profile = TeamProfile::where('team_id', $id)
                ->select(['id', 'team_id', 'follower_count'])
                ->first();

            if (!$profile) {
                $profile = new TeamProfile();
                $profile->follower_count = 0;
                $profile->team_id = $selectTeam->id;
            }

            $exisitngFollowCount = DB::table('team_follows')
                ->where('team_id', $selectTeam->id)
                ->select(['id', 'team_id'])
                ->count();

            $result = DB::table('team_follows')->where('user_id', $user->id)->where('team_id', $selectTeam->id)->delete();

            if ($result > 0) {
                $profile->follower_count = $exisitngFollowCount - 1;
            }

            if ($result === 0) {
                DB::table('team_follows')->insert([
                    'user_id' => $user->id,
                    'team_id' => $selectTeam->id,
                ]);

                $profile->follower_count = $exisitngFollowCount + 1;

                dispatch(
                    new HandleFollowsFriends('FollowTeam', [
                        'team' => $selectTeam,
                        'user' => $user,
                        'isFollow' => true,
                    ]),
                );
            }

            $profile->save();

            $cacheKey = sprintf(config('cache.keys.user_team_follows'), $user->id);
            Cache::forget($cacheKey);
            return back();
        } catch (Exception $e) {
            session()->flash('errorJoin', $e->getMessage());
            return back();
        }
    }

    public function teamMemberManagementRedirected(Request $request)
    {
        $page = 5;
        $user = $request->attributes->get('user') ?? auth()->user();
        $teamId = $request->teamId;
        $selectTeam = Team::where('id', $teamId)->with('members')->first();
        if ($selectTeam) {
            return $this->handleTeamManagement($selectTeam, $request->eventId, $request, $page, true);
        }
        return $this->showErrorParticipant('This event is missing or cannot be retrieved!');
    }

    public function teamMemberManagement(Request $request, $id)
    {
        $page = 5;
        $user = $request->attributes->get('user') ?? auth()->user();
        $user_id = $user->id;
        $selectTeam = Team::where('id', $id)->where('creator_id', $user_id)->with('members')->first();
        if ($selectTeam) {
            return $this->handleTeamManagement($selectTeam, $id, $request, $page, false);
        }
        return $this->showErrorParticipant('This event is missing or you need to be a member to view events!');
    }

    public function editTeam(UpdateTeamRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $team = Team::findOrFail($request['id']);
            $team->teamName = $request['teamName'];
            $team->slugify();
            $team->update($validatedData);
            if (isset($team->country)) {
                $country = Country::select('emoji_flag', 'name', 'id')->findOrFail($team->country);
            } else {
                $country = null;
            }

            $team->uploadTeamBanner($request);

            return response()->json(
                [
                    'message' => 'Team updated successfully',
                    'success' => true,
                    'country' => $country,
                ],
                200,
            );
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(
                    [
                        'message' => 'This team name was taken. Please change to another name.',
                        'success' => false,
                    ],
                    422,
                );
            }

            return response()->json(
                [
                    'message' => 'Error updating team: ' . $e->getMessage(),
                    'success' => false,
                ],
                400,
            );
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
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
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
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
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
        }
        return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
    }

    public function rejectInviteMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if ($member) {
            $member->status = 'rejected';
            $member->actor = 'user';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Team member invitation withdrawn']);
        }
        return response()->json(['success' => false, 'message' => 'Invalid operation or team member not found'], 400);
    }

    public function updateTeamMember(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Team member not found'], 400);
        }

        $status = $request->status;
        $isSameActor = $request->actor === $member->actor;

        $permissionRules = [
            'left' => true,
            'accepted' => [
                'pending' => !$isSameActor,
                'rejected' => $isSameActor,
                'left' => $isSameActor,
            ],
            'rejected' => [
                'pending' => !$isSameActor,
                'rejected' => $isSameActor,
                'accepted' => $isSameActor,
            ],
        ];

        $errorMessages = [
            'accepted' => [
                'pending' => $isSameActor ? 'You cannot accept your own pending request' : '',
                'rejected' => !$isSameActor ? 'Only the original requester can accept after rejection' : '',
                'left' => !$isSameActor ? 'Only the original member can accept after leaving' : '',
                'accepted' => 'Request is already accepted',
            ],
            'rejected' => [
                'pending' => $isSameActor ? 'You cannot reject your own pending request' : '',
                'rejected' => !$isSameActor ? 'Only the original requester can modify a rejected request' : '',
                'accepted' => !$isSameActor ? 'Only the accepted member can reject their request' : '',
            ],
        ];

        $isPermitted = $permissionRules[$status] ?? false;
        if (is_array($isPermitted)) {
            $isPermitted = $isPermitted[$member->status] ?? false;
        }

        if (!$isPermitted) {
            $message = 'This request is not allowed. ';
            if (isset($errorMessages[$status][$member->status])) {
                $message .= $errorMessages[$status][$member->status];
            }
            return response()->json(
                [
                    'success' => false,
                    'message' => trim($message),
                    'details' => [
                        'requested_status' => $status,
                        'current_status' => $member->status,
                        'is_same_actor' => $isSameActor,
                    ],
                ],
                400,
            );
        }

        $team = Team::where('id', $member->team_id)->first();

        if ($team->creator_id === $member->user_id) {
            return response()->json(['success' => false, 'message' => "Can't modify creator of the team"], 400);
        }

        $member->status = $status;
        $member->actor = $request->actor;
        $member->save();

        if ($member->status == 'left') {
            $captain = TeamCaptain::where([
                'team_member_id' => $id,
                'teams_id' => $member->team_id,
            ])->first();

            if ($captain) {
                $captain->delete();
            }
        }

        return response()->json(['success' => true, 'message' => "Team member status updated to {$status}"]);
    }

    public function captainMember(Request $request, $id, $memberId)
    {
        try {
            $existingCaptain = TeamCaptain::where('teams_id', $id)->first();

            $teamMember = TeamMember::findOrFail($memberId);

            if ($existingCaptain) {
                if ($existingCaptain->team_member_id != $teamMember->id) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Only captain can remove himself as captain!',
                        ],
                        400,
                    );
                }

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
        $existingCaptain = TeamCaptain::where('teams_id', $id)->where('team_member_id', $memberId)->first();

        $user_id = $request->attributes->get('user')->id;

        $teamMember = TeamMember::where('user_id', $user_id)->where('id', $memberId)->first();

        if (!$teamMember) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Only captain can remove himself as captain!',
                ],
                400,
            );
        }

        if ($existingCaptain) {
            $existingCaptain->delete();
        }

        return response()->json(['success' => 'true'], 200);
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

                $team = Team::validateAndSaveTeam($request, $team, $user_id);
                TeamMember::bulkCreateTeanMembers($team->id, [$user_id], 'accepted');
                $teamMembers = $team->members;

                TeamCaptain::insert([
                    'team_member_id' => $teamMembers[0]->id,
                    'teams_id' => $team->id,
                ]);

                return redirect()->route('participant.team.view', ['id' => $user_id]);
            }
            return back()->with('errorMessage', "You can't create more than 5 teams!");
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $errorMessage = 'This team name was taken. Please change to another name.';
            } else {
                $errorMessage = 'Error updating team: ' . $e->getMessage();
            }
            return back()->with('errorMessage', $errorMessage);
        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage());
        }
    }

    /**
     * Get paginated teams for select dropdown
     *
     * @param TeamSearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(TeamSearchRequest $request)
    {
        $params = $request->searchParams();

        $teams = Team::paginatedSearch($params['query'], $params['cursor'], $params['perPage'] + 1)->get();

        $hasMore = $teams->count() > $params['perPage'];
        if ($hasMore) {
            $teams->pop();
        }

        $nextCursor = $hasMore ? $teams->last()->id : null;

        $response = [
            'data' => $teams,
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
        ];

        return response()->json($response);
    }

    protected function handleTeamManagement($selectTeam, $eventId, $request, $page, $redirect = false)
    {
        $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
        $teamMembersProcessed = TeamMember::getProcessedTeamMembers($selectTeam->id);
        $creator_id = $selectTeam->creator_id;
        $userList = [];
        // dd($teamMembersProcessed);

        return view('Participant.MemberManagement', compact('selectTeam', 'redirect', 'teamMembersProcessed', 'creator_id', 'eventId', 'captain', 'userList'));
    }
}
