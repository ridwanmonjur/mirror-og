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
            $joinEvents = JoinEvent::getJoinEventsByTeamIdList($teamIdList)->get();
            $count = count($teamList);
    
            foreach ($teamList as $team) {
                $userIds = [];
    
                foreach ($joinEvents as $joinEvent) {
                    if ($joinEvent->team_id === $team->id) {
                        $userIds[] = $joinEvent->user->id;
                    }
                }
    
                $usernamesCountByTeam[$team->id] = count(array_unique($userIds));
            }

            return view('Participant.TeamList', compact('teamList', 'count', 'usernamesCountByTeam'));
        } else {
            session()->flash('errorMessage', 'You have 0 teams! Create a team first.');
            return view('Participant.CreateTeam');
        }
    }

    public function teamManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where('creator_id', $user_id)
            ->with(['members', 'awards'])->first();
        
        if ($selectTeam) {
            $joinEvents = JoinEvent::getJoinEventsForTeam($selectTeam->id)
                ->with(['eventDetails', 'results', 'roster' => function ($q) {
                    $q->where('status', 'accepted')->with('user');
                }])
                ->with('eventDetails.tier', 'eventDetails.game')
                ->get();

            foreach ($joinEvents as $joinEvent) {
                $joinEvent->status = $joinEvent->eventDetails->statusResolved();
                $joinEvent->tier = $joinEvent->eventDetails->tier;
                $joinEvent->game = $joinEvent->eventDetails->game;
            }

            $joinEventIds = $joinEvents->pluck('id')->toArray();
            $teamMembers = $selectTeam->members->where('status', 'accepted');

            return view('Participant.TeamManagement', 
                compact('selectTeam', 'joinEvents', 'teamMembers')
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
        $selectTeam = Team::where('id', $teamId)->where('creator_id', $user_id)->first();
        $joinEvent = JoinEvent::where('team_id', intval($teamId))->where('event_details_id', intval($id))->first();

        if ($selectTeam && $joinEvent) {
            $captain = RosterCaptain::where('join_events_id', $joinEvent->id)->first();
            $creator_id = $selectTeam->creator_id;
            $teamMembers = $selectTeam->members->where('status', 'accepted');
            $memberIds = $teamMembers->pluck('id')->toArray();
            
            $rosterMembers = RosterMember::whereIn('team_member_id', $memberIds)
                ->where('join_events_id', $joinEvent->id)->get();

            $rosterMembersProcessed = RosterMember::processStatus($rosterMembers);
            $rosterMembersKeyed = RosterMember::keyBy($rosterMembers);

            return view('Participant.RosterManagement', 
                compact('selectTeam', 'joinEvent', 'teamMembers', 'rosterMembersProcessed', 'creator_id', 'rosterMembersKeyed', 'id')
            );
        } else {
            return $this->show404Participant('This event is missing or you need to be a member to view events!');
        }
    }

    public function registrationManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where('creator_id', $user_id)
            ->with(['members', 'awards'])->first();

        if ($selectTeam) {
            $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($id) {
                $query->where('team_id', $id)->where('status', 'accepted');
            })
                ->with('eventDetails', 'user')
                ->with('eventDetails.tier', 'eventDetails.game')
                ->groupBy('event_details_id')
                ->get();

            foreach ($joinEvents as $joinEvent) {
                $joinEvent->status = $joinEvent->eventDetails->statusResolved();
                $joinEvent->tier = $joinEvent->eventDetails->tier;
                $joinEvent->game = $joinEvent->eventDetails->game;
            }

            $followCounts = Follow::select('organizer_id', DB::raw('count(user_id) as user_count'))->groupBy('organizer_id')->pluck('user_count', 'organizer_id')->toArray();

            return view('Participant.RegistrationManagement', compact('selectTeam', 'joinEvents', 'followCounts'));
        } else {
            return redirect()->back()->with('error', 'Team not found.');
        }
    }

    public function teamToRegister(Request $request)
    {
        $user_id = $request->attributes->get('user')->id;
        $teamId = $request->input('selectedTeamId');

        $isMember = TeamMember::where('team_id', $teamId)->where('user_id', $user_id)->exists();

        if (!$isMember) {
            $status = $user_id == Team::getTeamByCreatorId($teamId) ? 'accepted' : 'pending';
            $member = new TeamMember();
            $member->team_id = $teamId;
            $member->user_id = $user_id;
            $member->status = $status;
            $member->save();
        }

        $joinEventData = session('joinEventData');

        if ($joinEventData) {
            $joint = new JoinEvent();
            $joint->user_id = $joinEventData['user_id'];
            $joint->event_details_id = $joinEventData['event_details_id'];
            $joint->save();
        }

        return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);
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

            $organizerId = $event?->user?->organizer?->id ?? null;

            if ($organizerId) {
                $followersCount = Follow::where('organizer_id', $organizerId)->count();
            } else {
                $followersCount = null;
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

            $existingFollow = Follow::where('user_id', $userId)->where('organizer_id', $organizerId)->first();

            if (!$existingFollow) {
                Follow::create([
                    'user_id' => $userId,
                    'organizer_id' => $organizerId,
                ]);

                return response()->json(['message' => 'Successfully followed the organizer']);
            } else {
                return response()->json(['message' => 'Successfully followed the organizer']);
            }
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function unfollowOrganizer(Request $request)
    {
        $userId = $request->input('user_id');
        $organizerId = $request->input('organizer_id');

        Follow::where('user_id', $userId)->where('organizer_id', $organizerId)->delete();

        return response()->json(['message' => 'Successfully unfollowed the organizer']);
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
