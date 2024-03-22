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
use App\Models\Organizer;
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

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        $userId = Auth::id();
        $count = 6;
        $events = EventDetail::generateParticipantFullQueryForFilter($request)->with('tier', 'type', 'game', 'joinEvents')->paginate($count);

        $output = [
            'events' => $events,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'id' => $userId,
        ];

        if ($request->ajax()) {
            $view = view('Participant.HomeScroll', $output)->render();
            return response()->json(['html' => $view]);
        } else {
            return view('Participant.Home', $output);
        }
    }

    public function teamList(Request $request)
    {
        $user_id = $request->attributes->get('user')->id;
        [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ] = Team::getUserTeamAndTeamMembersAndPluckIds($user_id);
        
        if ($teamIdList) {
            $membersCount = DB::table('teams')
                ->leftJoin('team_members', function($join) {
                    $join->on('teams.id', '=', 'team_members.team_id')
                        ->where('team_members.status', '=', 'accepted');
                })
                ->whereIn('teams.id', $teamIdList)
                ->groupBy('teams.id')
                ->selectRaw('teams.id as team_id, COALESCE(COUNT(team_members.id), 0) as member_count')
                ->pluck('member_count', 'team_id')
                ->toArray();
            
            $count = $teamList->count();
            // dd($teamIdList, $membersCount);
    
            return view('Participant.TeamList', compact('teamList', 'count', 'membersCount' ));
        } else {
            session()->flash('errorMessage', 'You have 0 teams! Create a team first.');
            return view('Participant.CreateTeam');
        }
    }

    public function teamManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)
            ->with(['members', 'awards'])->first();
        
        if ($selectTeam) {
            $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
            $joinEvents = JoinEvent::getJoinEventsForTeam($selectTeam->id)
                ->with(['eventDetails', 'results', 'roster' => function ($q) {
                    $q->with('user');
                }])
                ->with('eventDetails.tier', 'eventDetails.game')
                ->get();

            $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
            $followCounts =  DB::table('users')
                ->leftJoin('follows', function($q)  {
                    $q->on('users.id', '=', 'follows.organizer_user_id');
                })
                ->whereIn('users.id', $userIds)
                ->selectRaw('users.id as organizer_user_id, COALESCE(COUNT(follows.organizer_user_id), 0) as count')
                ->groupBy('users.id')
                ->pluck('count', 'organizer_user_id')
                ->toArray();

            $joinEventsHistory = [];
            $joinEventsActive = [];
            foreach ($joinEvents as $joinEvent) {
                $joinEvent->status = $joinEvent->eventDetails->statusResolved();
                $joinEvent->tier = $joinEvent->eventDetails->tier;
                $joinEvent->game = $joinEvent->eventDetails->game;
                if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING'])) {
                    $joinEventsActive[] = $joinEvent;
                } else if ($joinEvent->status == 'ENDED'){
                    $joinEventsHistory[] = $joinEvent;
                }
            }

            $joinEventIds = $joinEvents->pluck('id')->toArray();
            $teamMembers = $selectTeam->members->where('status', 'accepted');

            // dd($joinEvents, $userIds, $followCounts, $user_id, $joinEvents, $teamMembers);

            return view('Participant.TeamManagement', 
                compact('selectTeam', 'joinEvents', 'captain', 'teamMembers', 'joinEventsHistory', 'joinEventsActive', 'followCounts')
            );
        } else {
            return $this->show404Participant('This event is missing or you need to be a member to view events!');
        }
    }

    public function teamMemberManagement(Request $request, $id, $teamId)
    {
        $page = 5;
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $teamId)->where('creator_id', $user_id)
            ->with('members')->first();

            if ($selectTeam) {
                $captain = TeamCaptain::where('teams_id', $selectTeam->id)->first();
                $teamMembers = $selectTeam->members;
                $teamMembersProcessed = TeamMember::processStatus($teamMembers);
                $creator_id = $selectTeam->creator_id;
                $userList = User::getParticipants($request, $teamId)->paginate($page);
                foreach ($userList as $user) {
                    $user->is_in_team = $user->members->isNotEmpty() ? 'yes' : 'no';
                }
                
                return view('Participant.MemberManagement', 
                    compact('selectTeam', 'teamMembersProcessed', 'creator_id', 'userList', 'id', 'captain')
                );
        } else {
            return $this->show404Participant('This event is missing or you need to be a member to view events!');
        }
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
            return $this->show404Participant('This event is missing or you need to be a member to view events!');
        }
    }

    public function registrationManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where('creator_id', $user_id)
            ->orWhere(function ($query) use ($user_id) {
                $query->whereHas('members', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 'accepted');
                });
            })
            ->with(['members', 'awards'])->first();

        if ($selectTeam) {
            $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($id) {
                $query->where('team_id', $id)->where('status', 'accepted');
            })
                ->with('eventDetails', 'user')
                ->with('eventDetails.tier', 'eventDetails.game')
                ->groupBy('event_details_id')
                ->get();
            
                $userIds = $joinEvents->pluck('event_details.user_id')->flatten()->toArray();
                $followCounts = DB::table('users')
                    ->leftJoin('follows', function($q)  {
                        $q->on('users.id', '=', 'follows.organizer_user_id');
                    })
                    ->whereIn('users.id', $userIds)
                    ->selectRaw('users.id as organizer_user_id, COALESCE(COUNT(follows.organizer_user_id), 0) as count')
                    ->groupBy('users.id')
                    ->pluck('count', 'organizer_user_id')
                    ->toArray();

            foreach ($joinEvents as $joinEvent) {
                $joinEvent->status = $joinEvent->eventDetails->statusResolved();
                $joinEvent->tier = $joinEvent->eventDetails->tier;
                $joinEvent->game = $joinEvent->eventDetails->game;
            }

            return view('Participant.RegistrationManagement', compact('selectTeam', 'followCounts', 'joinEvents'));
        } else {
            return redirect()->back()->with('error', "Team not found/ You're not authorized.");
        }
    }

    public function confirmUpdate(Request $request)
    {
        return view('Participant.Notify');
    }

    public function viewEvent(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $userId = $user && $user->id ? $user->id : null;

            $event = EventDetail::with('game', 'type')->withCount('joinEvents')->find($id);

            if (!$event) {
                throw new ModelNotFoundException("Event not found by id: $id");
            }

            $status = $event->statusResolved();

            if (in_array($status, ['DRAFT', 'PREVEW', 'PENDING'])) {
                $lowerStatus = strtolower($status);
                throw new ModelNotFoundException("Can't display event: $id with status: $lowerStatus");
            }

            if ($user) {
                if ($event->sub_action_private == 'private') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $userId;
                    // change this line
                    $checkIfUserIsInvited = true;
                    $checkIfShouldDisallow = !($checkIfUserIsOrganizerOfEvent || $checkIfUserIsInvited);

                    if ($checkIfShouldDisallow) {
                        throw new UnauthorizedException("You're neither organizer nor a participant of event");
                    }
                }

                if ($status == 'SCHEDULED') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $userId;

                    if (!$checkIfUserIsOrganizerOfEvent) {
                        throw new UnauthorizedException('You cannot view a scheduled event');
                    }
                }

                $existingJoint = JoinEvent::where('joiner_id', $userId)
                    ->where('event_details_id', $event->id)
                    ->first();
            } else {
                if ($event->sub_action_private == 'private') {
                    throw new UnauthorizedException('Login to access this event.');
                } else {
                    $existingJoint = null;
                }
            }

            $organizerId = $event?->user_id ?? null;

            if ($organizerId) {
                $followersCount = Follow::where('organizer_user_id', $organizerId)->count();
            } else {
                $followersCount = null;
            }

            if ($user) {
                $user->isFollowing = Follow::where('participant_user_id', $userId)
                    ->where('organizer_user_id', $event->user_id)
                    ->first();
            }

            return view('Participant.ViewEvent', compact('event', 'followersCount', 'user', 'existingJoint'));
        } catch (Exception $e) {
            // dd($e->getMessage());
            return $this->show404Participant($e->getMessage());
        }
    }

    public function followOrganizer(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $organizerId = $request->input('organizer_id');

            $existingFollow = Follow::where('participant_user_id', $userId)->where('organizer_user_id', $organizerId)->first();

            if ($existingFollow) {
                $existingFollow->delete();
                return response()->json([
                    'message' => 'Successfully unfollowed the organizer',
                    'isFollowing' => false
                ], 201);
            } else {
                Follow::create([
                    'participant_user_id' => $userId,
                    'organizer_user_id' => $organizerId,
                ]);

                return response()->json([
                    'message' => 'Successfully followed the organizer',
                    'isFollowing' => true
                ], 201);
               
            }
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function redirectToSelectOrCreateTeamToJoinEvent(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        [
            'teamList' => $selectTeam,
            'teamIdList' => $teamIdList,
        ] = Team::getUserTeamAndTeamMembersAndPluckIds($user_id);

        if ($selectTeam) {
            $joinEvents = JoinEvent::getJoinEventsByTeamIdList($teamIdList)->get();
            $joinEventIds = $joinEvents->pluck('event_details_id')->toArray();
            $hasEvent = array_reduce(
                $joinEventIds,
                function ($carry, $joinEventId) use ($id) {
                    return $carry || $joinEventId === $id;
                },
                false,
            );

            if (!$hasEvent) {
                $count = count($selectTeam);
                return view('Participant.SelectTeamToRegister', compact('selectTeam', 'count', 'id'));
            } else {
                $errorMessage = 'You have already joined this event.';
                session()->flash('errorMessage', $errorMessage);
                return redirect()->back()->withInput();
            }
        } else {
            $errorMessage = 'You have no team. Create a team.';
            return view('Participant.CreateTeamToRegister', ['id' => $id])->with('errorMessage', $errorMessage);
        }
    }

    public function redirectToCreateTeamToJoinEvent(Request $request, $id)
    {
        return view('Participant.CreateTeamToRegister', compact('id'));
    }

    private function processTeamRegistration($request, $eventId, $selectTeam, $teamMembers)
    {
        try {
            $userId = $request->attributes->get('user')->id;
            $participant = Participant::where('user_id', $userId)->firstOrFail();

            $joinEvent = JoinEvent::saveJoinEvent([
                'team_id' => $selectTeam->id,
                'joiner_id' => $userId,
                'joiner_participant_id' => $participant->id,
                'event_details_id' => $eventId,
            ]);

            $rosterList = RosterMember::bulkCreateRosterMembers($joinEvent->id, $teamMembers);
            
            // continue work with Roster Captain
            RosterCaptain::insert([
                'team_member_id' => $teamMembers[0]->id,
                'join_events_id' => $joinEvent->id,
            ]);
            
            $teamMembersProcessed = TeamMember::processStatus($teamMembers);

            return [$joinEvent, $teamMembers, $rosterList, $teamMembersProcessed];
        } catch (Exception $exception) {
            // throw $exception;
            return $this->show404Participant($exception->getMessage());
        }
    }

    public function selectTeamToJoinEvent(Request $request, $id)
    {
        try {
            $userId = $request->attributes->get('user')->id;
            $teamId = $request->input('selectedTeamId');
            $selectTeam = Team::find($teamId);
            $isAlreadyMember = TeamMember::isAlreadyMember($teamId, $userId);

            if ($selectTeam && $isAlreadyMember) {
                $teamMembers = $selectTeam->members;
                $this->processTeamRegistration($request, $id, $selectTeam, $teamMembers);

                if ($selectTeam->creator_id) {
                    return redirect()->route('participant.memberManage.action', ['id'=> $id, 'teamId' => $selectTeam->id])
                    ->with('successMessage', 'Successfully created and joined the event.')
                    ->with('redirectToMemberManage', true);
                } else {
                    return redirect()
                        ->route('participant.event.view', ['id' => $id])
                        ->with('successMessage', 'Successfully joined the event.');
                }
            } else {
                if (is_null($selectTeam)) {
                    throw new ModelNotFoundException("Can't find team with the id!");
                } else {
                    throw new ModelNotFoundException("Can't join a team you're not part of!");
                }
            }
        } catch (Exception $e) {
            // dd($e);
            return $this->show404Participant($e->getMessage());
        }
    }

    public function createTeamToJoinEvent(Request $request, $id)
    {
        DB::beginTransaction();

        try{
            $user_id = $request->attributes->get('user')->id;
            [
                'teamList' => $selectTeam,
                'count' => $count,
            ] = Team::getUserTeamListAndCount($user_id);

            if ($count < 5) {
                $request->validate([
                    'teamName' => 'required|string|max:25',
                    'teamDescription' => 'required'
                ]);

                $teamName = $request->input('teamName');
                $selectTeam = new Team(['teamName' => $teamName]);
                $selectTeam->teamDescription = $request->input('teamDescription');
                $selectTeam->creator_id = $user_id;
                $selectTeam->save();
                TeamMember::bulkCreateTeanMembers($selectTeam->id, [$user_id], 'accepted');
                $teamMembers = $selectTeam->members;
            
                TeamCaptain::insert([
                    'team_member_id' => $teamMembers[0]->id,
                    'teams_id' => $selectTeam->id,
                ]);

                $this->processTeamRegistration($request, $id, $selectTeam, $teamMembers);
                
                DB::commit();
                return redirect()->route('participant.memberManage.action', ['id'=> $id, 'teamId' => $selectTeam->id])
                    ->with('successMessage', 'Successfully created and joined the event.')
                    ->with('redirectToMemberManage', $id);
            } else {
                return redirect()
                    ->back()
                    ->with('error', "You have $count teams, so cannot join.");
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->show404Participant($e->getMessage());
        }
    }
}
