<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\EventDetail;
use App\Models\EventInvitation;
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

            if ($user && $userId) {
                $user->isFollowing = Follow::where('participant_user_id', $userId)
                    ->where('organizer_user_id', $event->user_id)
                    ->first();

                $followersCount = Follow::where('organizer_user_id', $event->user_id)->count();
                if ($event->sub_action_private == 'private') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $userId;
                    $checkIfUserIsInvited = EventInvitation::where('participant_user_id', $userId)
                        ->where('event_id', $event->id)->exists();
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
                $followersCount = null;
                if ($event->sub_action_private == 'private') {
                    throw new UnauthorizedException('Login to access this event.');
                } else {
                    $existingJoint = null;
                }
            }

            return view('Participant.ViewEvent', compact('event', 'followersCount', 'user', 'existingJoint'));
        } catch (Exception $e) {
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

    public function registrationManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where(function ($q) use ($user_id) {
            $q->where('creator_id', $user_id)
                ->orWhere(function ($query) use ($user_id) {
                    $query->whereHas('members', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id)->where('status', 'accepted');
                    });
                });
            })->with(['members', 'awards', 'invitationList'])->first();

        if ($selectTeam) {
            $invitationListIds = $selectTeam->invitationList->pluck('event_id');
            [$joinEventUserIds, $joinEvents] = JoinEvent::getJoinEventsAndIds($id, $invitationListIds, false);
            [$invitedEventUserIds, $invitedEvents] = JoinEvent::getJoinEventsAndIds($id, $invitationListIds, true);

            $userIds = array_unique(array_merge($joinEventUserIds, $invitedEventUserIds));
            $followCounts = DB::table('users')
                ->leftJoin('follows', function($q)  {
                    $q->on('users.id', '=', 'follows.organizer_user_id');
                })
                ->whereIn('users.id', $userIds)
                ->selectRaw('users.id as organizer_user_id, COALESCE(COUNT(follows.organizer_user_id), 0) as count')
                ->groupBy('users.id')
                ->pluck('count', 'organizer_user_id')
                ->toArray();

            return view('Participant.RegistrationManagement', compact('selectTeam', 'invitedEvents', 'followCounts', 'joinEvents'));
        } else {
            return redirect()->back()->with('error', "Team not found/ You're not authorized.");
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
            
            RosterCaptain::insert([
                'team_member_id' => $teamMembers[0]->id,
                'join_events_id' => $joinEvent->id,
                'teams_id' => $selectTeam->id
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

                if ($selectTeam->creator_id == $userId) {
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
            if ($e->getCode() == '23000' || 1062 == $e->getCode()) {
                session()->flash('errorMessage', 'Please choose a unique name!');
            } else {
                session()->flash('errorMessage', $e->getMessage());
            }

            return view('Participant.CreateTeamToRegister', ['id' => $id]);
        }
    }
}
