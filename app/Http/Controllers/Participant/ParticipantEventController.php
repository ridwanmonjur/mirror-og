<?php

namespace App\Http\Controllers\Participant;

use App\Events\JoinEventConfirmed;
use App\Http\Controllers\Controller;
use App\Jobs\HandleFollows;
use App\Models\EventDetail;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\UnauthorizedException;

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {

        if (Session::has('intended')) {
            $intendedUrl = Session::get('intended');
            Session::forget('intended');
            return redirect($intendedUrl);        
        }

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

    public function viewEvent(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $userId = $user && $user->id ? $user->id : null;
            $event = EventDetail::with(['game', 'type', 'joinEvents' => function ($query) {
                $query->with(['members' => function ($query) {
                    $query->where('status', 'accepted');
                }]);
            },
            ], null
            )->find($id);

            $event->acceptedMembersCount = 0;
            foreach ($event->joinEvents as $joinEvent) {
                $event->acceptedMembersCount += $joinEvent->members->count();
            }
            if (! $event) {
                throw new ModelNotFoundException("Event not found by id: $id");
            }

            $status = $event->statusResolved();
            if (in_array($status, ['DRAFT', 'PREVEW', 'PENDING'])) {
                $lowerStatus = strtolower($status);
                throw new ModelNotFoundException("Can't display event: $id with status: $lowerStatus");
            }

            $followersCount = Follow::where('organizer_user_id', $event->user_id)->count();
            $likesCount = Like::where('event_id', $event->id)->count();
            if ($user && $userId) {
                $user->isFollowing = Follow::where('participant_user_id', $userId)
                    ->where('organizer_user_id', $event->user_id)
                    ->first();
                
                $user->isLiking = Like::where('user_id', $userId)
                    ->where('event_id', $event->id)
                    ->first();

                if ($event->sub_action_private == 'private') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $userId;
                    $checkIfUserIsInvited = true;
                    $checkIfShouldDisallow = ! ($checkIfUserIsOrganizerOfEvent || $checkIfUserIsInvited);
                    if ($checkIfShouldDisallow) {
                        throw new UnauthorizedException("This is a provate event and you're neither organizer nor a participant of event");
                    }
                }

                if ($status == 'SCHEDULED') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id == $userId;
                    if (! $checkIfUserIsOrganizerOfEvent) {
                        throw new UnauthorizedException('You cannot view a scheduled event');
                    }
                }

                $existingJoint = JoinEvent::getJoinedByTeamsForSameEvent($event->id, $userId);
            } else {
                if ($event->sub_action_private == 'private') {
                    throw new UnauthorizedException('Login to access this event.');
                } else {
                    $existingJoint = null;
                }
            }

            return view('Participant.ViewEvent', compact('event', 'likesCount', 'followersCount', 'user', 'existingJoint'));
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function followOrganizer(Request $request)
    {

        $user = $request->attributes->get('user');
        $userId = $user->id;
        $organizerId = $request->organizer_id;
        $existingFollow = Follow::where('participant_user_id', $userId)
            ->where('organizer_user_id', $organizerId)
            ->get()
            ->first();
        $organizer = User::findOrFail($organizerId);

        if ($existingFollow) {
            // dispatch(new HandleFollows('Unfollow', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Follow',
            // ]));

            $existingFollow->delete();

            return response()->json([
                'message' => 'Successfully Unfollowed the organizer',
                'isFollowing' => false,
            ], 201);
        } else {
            Follow::create([
                'participant_user_id' => $userId,
                'organizer_user_id' => $organizerId,
            ]);

            // dispatch(new HandleFollows('Unfollow', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Follow',
            //     'log' => '<span class="notification-gray"> User'
            //     . ' <span class="notification-black">' . $user->name . '</span> started following '
            //     . ' <span class="notification-black">' . $organizer->name . '.</span> '
            //     . '</span>'
            // ]));

            return response()->json([
                'message' => 'Successfully followed the organizer',
                'isFollowing' => true,
            ], 201);
        }
    }

    public function registrationManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where(function ($q) use ($user_id) {
            $q->where(function ($query) use ($user_id) {
                $query->whereHas('members', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 'accepted');
                });
            });
        })->with([
            'invitationList', 'members.payments' => function ($query) {
                $query
                    ->groupBy('team_members_id')
                    ->select('team_members_id', DB::raw('SUM(payment_amount) as total_payment'));
            }, 'members.user',
        ])->first();

        $paymentsByMemberId = [];
        foreach ($selectTeam->members as $member) {
            if ($member->payments->isNotEmpty()) {
                $firstPayment = $member->payments->first();
                $paymentsByMemberId[$member->id] = $firstPayment->total_payment;
            } else {
                $paymentsByMemberId[$member->id] = 0;
            }
        }

        $member = TeamMember::where('user_id', $user_id)->select('id')->get()->first();

        if ($selectTeam) {
            $invitationListIds = $selectTeam->invitationList->pluck('event_id');
            [$joinEventUserIds, $joinEvents] = JoinEvent::getJoinEventsAndIds($id, $invitationListIds, false);
            [$invitedEventUserIds, $invitedEvents] = JoinEvent::getJoinEventsAndIds($id, $invitationListIds, true);

            $userIds = array_unique(array_merge($joinEventUserIds, $invitedEventUserIds));
            $followCounts = Follow::getFollowCounts($userIds);
            $isFollowing = Follow::getIsFollowing($user_id, $userIds);
            ['joinEvents' => $joinEvents, 'activeEvents' => $activeEvents, 'historyEvents' => $historyEvents]
                = JoinEvent::processEvents($joinEvents, $isFollowing);

            return view('Participant.RegistrationManagement',
                compact('selectTeam', 'invitedEvents', 'followCounts', 'paymentsByMemberId', 'member', 'joinEvents', 'isFollowing')
            );
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
        ] = Team::getUserTeamListAndPluckIds($user_id);
        $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $user_id, 'accepted');
        if ($hasJoinedOtherTeams) {
            return $this->showErrorParticipant('One of your teams has joined this event already!');
        }

        if ($selectTeam) {
            $count = count($selectTeam);

            return view('Participant.SelectTeamToRegister', compact('selectTeam', 'count', 'id'));

        } else {
            $errorMessage = 'You have no team. Create a team.';

            return view('Participant.CreateTeamToRegister', ['id' => $id])->with('errorMessage', $errorMessage);
        }
    }

    public function redirectToCreateTeamToJoinEvent(Request $request, $id)
    {
        return view('Participant.CreateTeamToRegister', compact('id'));
    }

    public function selectTeamToJoinEvent(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->attributes->get('user');
            $userId = $request->attributes->get('user')->id;
            $teamId = $request->input('selectedTeamId');
            $isAlreadyMember = TeamMember::isAlreadyMember($teamId, $userId);
            $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $userId, 'accepted');
            if ($hasJoinedOtherTeams) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $selectTeam = Team::getTeamAndMembersByTeamId($teamId);
            // dd($selectTeam);
            $event = EventDetail::with(['user' => function ($query) {
                $query->select('id', 'name', 'email');
            }])
                ->select('id', 'user_id', 'eventName')
                ->find($id);

            if ($selectTeam && $isAlreadyMember) {
                [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs]
                    = $selectTeam->processTeamRegistration($user, $event, true);
                Event::dispatch(new JoinEventConfirmed(
                    compact(
                        'memberList', 'organizerList', 'memberNotification',
                        'organizerNotification', 'allEventLogs'
                    )
                ));

                DB::commit();

                return view('Participant.EventNotify', compact('id', 'selectTeam'));

            } else {
                if (is_null($selectTeam)) {
                    throw new ModelNotFoundException("Can't find team with the id!");
                } else {
                    throw new ModelNotFoundException("Can't join a team you're not part of!");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                $errorMessage = "Please choose a team that hasn't joined this event!";
            } else {
                $errorMessage = $e->getMessage();
            }

            return $this->showErrorParticipant($errorMessage);
        }
    }

    public function createTeamToJoinEvent(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->attributes->get('user');
            $user_id = $request->attributes->get('user')->id;
            [
                'teamList' => $selectTeam,
                'count' => $count,
            ] = Team::getUserTeamListAndCount($user_id);

            $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $user_id, 'accepted');
            if ($hasJoinedOtherTeams) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $event = EventDetail::select('id', 'user_id', 'eventName')->with(
                ['user' => function ($q) {
                    $q->select('id', 'name', 'email');
                }]
            )->find($id);

            if ($count < 5) {
                $request->validate([
                    'teamName' => 'required|string|max:25',
                    'teamDescription' => 'required',
                ]);

                $teamName = $request->input('teamName');
                $selectTeam = new Team(['teamName' => $teamName]);
                $selectTeam->teamDescription = $request->input('teamDescription');
                $selectTeam->creator_id = $user_id;
                $selectTeam->save();
                TeamMember::bulkCreateTeanMembers($selectTeam->id, [$user_id], 'accepted');
                TeamCaptain::insert([
                    'team_member_id' => $selectTeam->members[0]->id,
                    'teams_id' => $selectTeam->id,
                ]);
                $teamMembers = $selectTeam->members->load(['user' => function ($q) {
                    $q->select(['name', 'id', 'email']);
                }]);
                [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs]
                    = $selectTeam->processTeamRegistration($user, $event, true);
                event(new JoinEventConfirmed(
                    compact(
                        'memberList', 'organizerList', 'memberNotification',
                        'organizerNotification', 'allEventLogs'
                    )
                ));
                DB::commit();

                return view('Participant.EventNotify', compact('id', 'selectTeam'));
            } else {
                session()->flash('errorMessage', 'You already have 5 teams!');

                return view('Participant.CreateTeamToRegister', ['id' => $id]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                $errorMessage = 'Please choose a unique name!';
            } else {
                $errorMessage = $e->getMessage();
            }

            session()->flash('errorMessage', $errorMessage);

            return view('Participant.CreateTeamToRegister', ['id' => $id]);

        }
    }

    public function confirmOrCancel(Request $request) {
        try{
            // dd($request);
            $successMessage = $request->join_status == 'confirmed' ? 'Your registration is now successfully confirmed!' 
                : 'Your registration is now successfully canceled.';
            
            $joinEvent = JoinEvent::where('id', $request->join_event_id)->select(['id', 'join_status', 'payment_status'])->firstOrFail();
            
            $isPermitted = $joinEvent->payment_status == "completed" && 
                ($request->join_status == 'confirmed' || $request->join_status == 'canceled'); 

            if ($isPermitted) {
                $joinEvent->join_status = $request->join_status;
                $joinEvent->save();
                // dd($joinEvent, $request);
            } else {
                return back()->with('errorMessage', 'Error operation not permitted.');

            }

            return back()->with("successMessage", $successMessage);
        } catch (Exception $e) {
            return $this->showParticipantError($e->getMessage());
        }
    }
}
