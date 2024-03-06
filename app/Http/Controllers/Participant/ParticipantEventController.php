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

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        $userId = Auth::id();
        $count = 6;
        $events = EventDetail::generateParticipantFullQueryForFilter($request)
            ->with('tier', 'type', 'game', 'joinEvents')
            ->paginate($count);

        $output = [
            'events' => $events,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'id' => $userId,
        ];

        if ($request->ajax()) {
            $view = view("Participant.HomeScroll", $output)->render();
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
            'teamIdList' => $teamIdList
        ] = Team::getUserTeamListAndPluckIds($user_id);
    
        $joinEvents = JoinEvent::getJoinEventsByTeamIdList($teamIdList);

        foreach ($teamList as $team) {
            $userIds = [];

            foreach ($joinEvents as $joinEvent) {
                if ($joinEvent->team_id === $team->id) {
                    $userIds[] = $joinEvent->user->id;
                }
            }

            $usernamesCountByTeam[$team->id] = count(array_unique($userIds));
        }
        return view('Participant.TeamList', compact('teamList', 'usernamesCountByTeam'));
    }

    public function teamManagement($id)
    {
        $teamManage = Team::where('id', $id)->get();

        if ($teamManage) {
            $userStatus = $this->getUserStatusForTeam(auth()->user()->id, $id);
            if ($userStatus == 'accepted' || $userStatus === null) {
                $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($id) {
                    $query->where('team_id', $id)->where('status', 'accepted');
                })
                    ->with('eventDetails', 'user')
                    ->get();

                $eventsByTeam = [];

                foreach ($joinEvents as $event) {
                    
                    $userId = $event->user_id;
                    $teamId = $event->user->teams->first(function ($team) use ($id) {
                        return $team->id == $id;
                    })->id;

                    if (!isset($eventsByTeam[$teamId][$userId])) {
                        $eventsByTeam[$teamId][$userId]['user'] = $event->user;
                        $eventsByTeam[$teamId][$userId]['events'] = [];
                    }

                    $eventsByTeam[$teamId][$userId]['events'][] = $event;
                }

                $pendingMembers = TeamMember::where('team_id', $id)
                    ->where('status', 'pending')
                    ->with('user')
                    ->get();
                
                $pendingMembersCount = count($pendingMembers);
                
                return view('Participant.TeamManagement', compact('teamManage', 'joinEvents', 'eventsByTeam', 'pendingMembers', 'pendingMembersCount'));
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'You need to be an accepted member to view events.');
            }
        } else {
            return redirect()
                ->back()
                ->with('error', 'Team not found.');
        }
    }

    private function getUserStatusForTeam($userId, $teamId)
    {
        return TeamMember::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->value('status');
    }

    public function approveMember(Request $request, $id)
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

    public function registrationManagement($id)
    {
        $teamManage = Team::where('id', $id)->get();

        if ($teamManage) {
            $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($id) {
                $query->where('team_id', $id)->where('status', 'accepted');
            })
                ->with('eventDetails', 'user')
                ->groupBy('event_details_id') 
                ->get();

            $eventsByTeam = [];

            foreach ($joinEvents as $event) {
                
                $userId = $event->user_id;
                
                $teamId = $event->user->teams->first(function ($team) use ($id) {
                    return $team->id == $id;
                })->id;

                if (!isset($eventsByTeam[$teamId][$userId])) {
                    $eventsByTeam[$teamId][$userId]['user'] = $event->user;
                    $eventsByTeam[$teamId][$userId]['events'] = [];
                }

                $eventsByTeam[$teamId][$userId]['events'][] = $event;
            }

            $followCounts = Follow::select('organizer_id', DB::raw('count(user_id) as user_count'))
                ->groupBy('organizer_id')
                ->pluck('user_count', 'organizer_id')
                ->toArray();

            return view('Participant.RegistrationManagement', compact('teamManage', 'joinEvents', 
                'eventsByTeam', 'followCounts'
            ));
        } else {
            return redirect()
                ->back()
                ->with('error', 'Team not found.');
        }
    }

    public function makeCaptain(Request $request)
    {
        $userId = $request->input('userId');
        $eventId = $request->input('eventId');

        $existingCaptain = Captain::where('eventID', $eventId)->where('userID', $userId)->first();
        
        if ($existingCaptain) {
            $existingCaptain->delete();
            return response()->json(['message' => 'You are no longer a captain for this event.'], 200);
        }

        $existingCaptainForEvent = Captain::where('eventID', $eventId)->first();
        
        if ($existingCaptainForEvent) {
            return response()->json(['error' => 'This event already has a captain.'], 400);
        }

        Captain::create([
            'userID' => $userId,
            'eventID' => $eventId,
            'isCaptain' => true,
        ]);

        return response()->json(['message' => 'You are now a captain for this event.'], 200);
    }

    public function createTeamView()
    {
        return view('Participant.CreateTeam');
    }

    public function teamStore(Request $request)
    {
        $user_id = $request->attributes->get('user')->id;

        $validatedData = $request->validate([
            'teamName' => 'required|string|max:25',
        ]);

        $team = new Team();
        $team->teamName = $request->input('teamName');
        $existingTeam = Team::where('teamName', $team->teamName)->first();

        if ($existingTeam) {
            return redirect()
                ->back()
                ->with('error', 'Team name already exists. Please choose a different name.');
        }
        
        $team->user_id = $user_id;
        $team->save();
        return redirect()->route('participant.team.view', ['id' => $user_id]);
    }

   

    public function teamToRegister(Request $request)
    {
        $user_id = $request->attributes->get('user')->id;
        $teamId = $request->input('selectedTeamId');
      
        $isMember = TeamMember::where('team_id', $teamId)
            ->where('user_id', $user_id)
            ->exists();

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

        session()->forget('joinEventData');

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

            $event = EventDetail::with('game', 'type')
                ->withCount('joinEvents')
                ->find($id);


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

            $existingFollow = Follow::where('user_id', $userId)
                ->where('organizer_id', $organizerId)
                ->first();

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

        Follow::where('user_id', $userId)
            ->where('organizer_id', $organizerId)
            ->delete();

        return response()->json(['message' => 'Successfully unfollowed the organizer']);
    }

    public function redirectToSelectOrCreateTeamToJoinEvent(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        [
            'teamList' => $selectTeam,
            'teamIdList' => $teamIdList
        ] = Team::getUserTeamListAndPluckIds($user_id);
    
        if ($selectTeam) {
            $joinEvents = JoinEvent::getJoinEventsByTeamIdList($teamIdList);
            
            if ($joinEvents->isEmpty()) {
                $count = $selectTeam->count();    
                return view('Participant.SelectTeamToRegister', compact('selectTeam', 'count', 'id'));
            } else {
                $errorMessage = 'You have already joined this event.';
                session()->flash('errorMessage', $errorMessage);
                return redirect()->back()->withInput();
            }
        } else {
            $errorMessage = 'You have no team. Create a team';
            return view('Participant.CreateTeamToRegister', ['id' => $id] )
                ->with('errorMessage', $errorMessage);
        }
    }

    public function redirectToCreateTeamToJoinEvent(Request $request, $id) {
        return view('Participant.CreateTeamToRegister', compact('id'));
    }

    private function processTeamRegistration($request, $id, $selectTeam)
    {
        try{
            $userId = $request->attributes->get('user')->id;
            $participant = Participant::where('user_id', $userId)->firstOrFail();
            dd($userId, $participant);

            $joinEvent = JoinEvent::saveJoinEvent([
                'team_id' => $selectTeam->id,
                'joiner_id' => $userId,
                'joiner_participant_id' => $participant->id,
                'event_details_id' => $id
            ]);
        
            $teamMembers = $selectTeam->members();
            $rosterList = RosterMember::bulkCreateRosterMembers($joinEvent->id, $teamMembers);
            return view('Participant.ManageTeamToRegister', compact('selectTeam', 'joinEvent', 'teamMembers'));
        } catch(Exception $exception) {
            return $this->show404Participant($exception->getMessage());
        }
    }
    
    public function selectTeamToJoinEvent(Request $request, $id)
    {
        try{
            $teamId = $request->input('selectedTeamId');
            $selectTeam = Team::find($teamId);
            if ($selectTeam) {
                return $this->processTeamRegistration($request, $id, $selectTeam->id);
            } else {
                throw new ModelNotFoundException("Can't find team with the id!");
            }
        } catch (Exception $e) {
            return $this->show404Participant($e->getMessage());
        }
    }
    
    public function createTeamToJoinEvent(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        [
            'teamList' => $selectTeam,
            'count' => $count
        ] = Team::getUserTeamListAndCount($user_id);

        if ($count < 5) {
            $teamName = $request->input('teamName');
            $selectTeam = new Team(['teamName' => $teamName]);
            $selectTeam->creator_id = $user_id;
            $selectTeam->save();
            return $this->processTeamRegistration($request, $id, $selectTeam);
        } else {
            return redirect()
                    ->back()
                    ->with('error', "You have $count teams, so cannot join.");
        }
    }
}
