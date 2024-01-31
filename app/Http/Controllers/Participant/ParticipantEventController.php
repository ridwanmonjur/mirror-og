<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Team;
use App\Models\EventDetail;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\Member;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        $userId = Auth::id();

        $currentDateTime = Carbon::now()->utc();
        $count = 6;

        $events = EventDetail::query()
            ->where('status', '<>', 'DRAFT')
            ->whereNotNull('payment_transaction_id')
            ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime])
            ->where('sub_action_private', '<>', 'private')
            ->where(function ($query) use ($currentDateTime) {
                $query
                    ->whereRaw('CONCAT(sub_action_public_time, " ", sub_action_public_date) < ?', [$currentDateTime])
                    ->orWhereNull('sub_action_public_time')
                    ->orWhereNull('sub_action_public_date');
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                if (empty($search)) {
                    return $query;
                }
                return $query->where('eventName', 'LIKE', "%{$search}%")->orWhere('eventDefinitions', 'LIKE', "%{$search}%");
            })
            ->with('tier', 'type', 'game', 'joinEvents')
            ->paginate($count);

        $output = [
            'events' => $events,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'id' => $userId,
        ];

        if ($request->ajax()) {
            $view = view('Participant.HomeScroll', $output)->render();
            return response()->json(['html' => $view]);
        }

        return view('Participant.Home', $output);
    }

    public function teamList($user_id)
    {
        $teamList = Team::leftJoin('members', 'teams.id', '=', 'members.team_id')
            ->where(function ($query) use ($user_id) {
                $query->where('teams.user_id', $user_id)->orWhere('members.user_id', $user_id);
            })
            ->groupBy('teams.id')
            ->select('teams.*')
            ->get();

        if ($teamList->isNotEmpty()) {
            $usernamesCountByTeam = [];
            foreach ($teamList as $team) {
                $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($team) {
                    $query->where('team_id', $team->id);
                })
                    ->with('user')
                    ->get();

                $usernames = $joinEvents
                    ->unique('user_id')
                    ->pluck('user')
                    ->count();

                $usernamesCountByTeam[$team->id] = $usernames;
            }

            return view('Participant.TeamList', compact('teamList', 'usernamesCountByTeam'));
        } else {
            return redirect()
                ->back()
                ->with('error', 'No teams found for the user.');
        }
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

                $pendingMembers = Member::where('team_id', $id)
                    ->where('status', 'pending')
                    ->with('user')
                    ->get();

                return view('Participant.TeamManagement', compact('teamManage', 'joinEvents', 'eventsByTeam', 'pendingMembers'));
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
        return Member::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->value('status');
    }

    public function approveMember(Request $request, $id)
    {
        $member = Member::find($id);
        if ($member && $member->status === 'pending') {
            $member->status = 'accepted';
            $member->save();

            return response()->json(['success' => true, 'message' => 'Member status updated to accepted']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid operation or member not found'], 400);
    }

    public function registrationManagement($id)
    {
        $teamManage = Team::where('id', $id)->get();

        if ($teamManage) {
            $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($id) {
                $query->where('team_id', $id)->where('status', 'accepted');
            })
                ->with('eventDetails', 'user')
                ->groupBy('event_details_id') // Group by event_details_id to ensure uniqueness
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

            return view('Participant.RegistrationManagement', compact('teamManage', 'joinEvents', 'eventsByTeam', 'followCounts'));
        } else {
            return redirect()
                ->back()
                ->with('error', 'Team not found.');
        }
    }

    public function createTeamView(Request $request, $user_id)
    {
        $teamm = Team::find($user_id);
        return view('Participant.CreateTeam', compact('teamm'));
    }

    public function TeamStore(Request $request)
    {
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

        $team->user_id = auth()->user()->id;
        $team->save();
        return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);
    }

    public function selectTeamToRegister(Request $request)
    {
        $selectTeam = Team::all();
        return view('Participant.SelectTeamToRegister', compact('selectTeam'));
    }

    // public function TeamtoRegister(Request $request)
    // {
    //     $selectedTeamNames = $request->input('selectedTeamName');

    //     if (is_array($selectedTeamNames)) {
    //         foreach ($selectedTeamNames as $teamId) {
    //             $member = new Member();
    //             $member->team_id = $teamId;
    //             $member->user_id = auth()->user()->id;
    //             $member->save();
    //             return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);
    //         }
    //     } else {
    //         $member = new Member();
    //         $member->team_id = $selectedTeamNames;
    //         $member->user_id = auth()->user()->id;
    //         $member->save();
    //         return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);
    //     }
    // }

    public function TeamtoRegister(Request $request)
    {
        $selectedTeamNames = $request->input('selectedTeamName');

        if (is_array($selectedTeamNames)) {
            
            foreach ($selectedTeamNames as $teamId) {
                
                if (!$this->userAlreadyMember($teamId)) {
                    $status = auth()->user()->id == $this->getTeamCreatorId($teamId) ? 'accepted' : 'pending';
                    $this->registerUserToTeam($teamId, $status);
                }
            }
        } else {
            
            if (!$this->userAlreadyMember($selectedTeamNames)) {
                $status = auth()->user()->id == $this->getTeamCreatorId($selectedTeamNames) ? 'accepted' : 'pending';
                $this->registerUserToTeam($selectedTeamNames, $status);
            }
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

    private function userAlreadyMember($teamId)
    {
        $userId = auth()->user()->id;

        return Member::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->exists();
    }

    private function getTeamCreatorId($teamId)
    {
        return Team::where('id', $teamId)->value('user_id');
    }

    private function registerUserToTeam($teamId, $status)
    {
        $member = new Member();
        $member->team_id = $teamId;
        $member->user_id = auth()->user()->id;
        $member->status = $status;
        $member->save();
    }

    public function ConfirmUpdate(Request $request)
    {
        return view('Participant.Notify');
    }

    public function ViewEvent(Request $request, $id)
    {
        try {
            $event = EventDetail::find($id);

            if (!$event) {
                throw new ModelNotFoundException("Event not found by id: $id");
            }

            $status = $event->statusResolved();

            if (in_array($status, ['DRAFT', 'PREVEW', 'PENDING'])) {
                $lowerStatus = strtolower($status);
                throw new ModelNotFoundException("Can't display event: $id with status: $lowerStatus");
            }

            $count = 8;
            $user = Auth::user();
            $eventListQuery = EventDetail::query();
            $eventListQuery->with('tier');

            if ($user) {
                if ($event->sub_action_private == 'private') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $user->id;
                    // change this line
                    $checkIfUserIsInvited = true;
                    $checkIfShouldDisallow = !($checkIfUserIsOrganizerOfEvent || $checkIfUserIsInvited);

                    if ($checkIfShouldDisallow) {
                        throw new UnauthorizedException("You're neither organizer nor a participant of event");
                    }
                }

                if ($status == 'SCHEDULED') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $user->id;

                    if (!$checkIfUserIsOrganizerOfEvent) {
                        throw new UnauthorizedException('You cannot view a scheduled event');
                    }
                }

                $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);
                $userId = auth()->user()->id;

                $existingJoint = JoinEvent::where('user_id', $userId)
                    ->where('event_details_id', $event->id)
                    ->first();

                foreach ($eventList as $_event) {
                    $tierEntryFee = $_event->eventTier?->tierEntryFee ?? null;
                }
            } else {
                if ($event->sub_action_private == 'private') {
                    throw new UnauthorizedException('Login to access this event.');
                }

                $eventList = [];
                $userId = null;
                $existingJoint = null;
            }

            $organizerId = $event?->user?->organizer?->id ?? null;

            if ($organizerId) {
                $followersCount = Follow::where('organizer_id', $organizerId)->count();
            } else {
                $followersCount = null;
            }

            return view('Participant.ViewEvent', compact('event', 'eventList', 'followersCount', 'user', 'existingJoint'));
        } catch (Exception $e) {
            return $this->show404($e->getMessage());
        }
    }

    public function show404($error)
    {
        return view('Organizer.EventNotFound', compact('error'));
    }

    public function FollowOrganizer(Request $request)
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

    public function JoinEvent(Request $request, $id)
    {
        $userId = auth()->user()->id;
        $existingJoint = JoinEvent::where('user_id', $userId)
            ->where('event_details_id', $id)
            ->first();

        if ($existingJoint) {
            $errorMessage = 'You have already joined this event.';
            session()->flash('errorMessage', $errorMessage);
        } else {
            session([
                'joinEventData' => [
                    'user_id' => $userId,
                    'event_details_id' => $id,
                ],
            ]);

            return redirect()->route('participant.selectTeam.view', ['id' => auth()->user()->id]);
        }

        return redirect()
            ->back()
            ->withInput();
    }
}
