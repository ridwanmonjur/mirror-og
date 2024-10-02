<?php

namespace App\Http\Controllers\Participant;

use App\Events\JoinEventSignuped;
use App\Http\Controllers\Controller;
use App\Jobs\HandleFollows;
use App\Models\BracketData;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\OrganizerFollow;
use App\Models\PaymentTransaction;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\PaymentService;
use ErrorException;
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

    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

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
        }
        return view('Participant.Home', $output);
    }

    public function viewEvent(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $userId = $user && $user->id ? $user->id : null;
            $event = EventDetail::with(
                ['game', 'type',  'joinEvents' => function ($q) {
                    $q->where('join_status', 'confirmed')->with('team');
                }]
                ,
                null
            )
                ->withCount(['joinEvents' => function ($q) {
                    $q->where('join_status', 'confirmed');
                }])
                ->find($id);
           
            if (! $event) {
                throw new ModelNotFoundException("Event not found by id: {$id}");
            }

            $status = $event->statusResolved();
            if (in_array($status, ['DRAFT', 'PREVEW', 'PENDING'])) {
                $lowerStatus = strtolower($status);
                throw new ModelNotFoundException("Can't display event: {$id} with status: {$lowerStatus}");
            }

            $followersCount = OrganizerFollow::where('organizer_user_id', $event->user_id)->count();
            $likesCount = Like::where('event_id', $event->id)->count();
            if ($user && $userId) {
                // @phpstan-ignore-next-line
                $user->isFollowing = OrganizerFollow::where('participant_user_id', $userId)
                    ->where('organizer_user_id', $event->user_id)
                    ->first();

                // @phpstan-ignore-next-line
                $user->isLiking = Like::where('user_id', $userId)
                    ->where('event_id', $event->id)
                    ->first();

                if ($event->sub_action_private === 'private') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id === $userId;
                    $checkIfUserIsInvited = true;
                    // phstan correct actually
                    // @phpstan-ignore-next-line
                    $checkIfShouldDisallow = ! ($checkIfUserIsOrganizerOfEvent || $checkIfUserIsInvited);
                    // phstan correct actually
                    // @phpstan-ignore-next-line
                    if ($checkIfShouldDisallow) {
                        throw new UnauthorizedException("This is a provate event and you're neither organizer nor a participant of event");
                    }
                }

                if ($status === 'SCHEDULED') {
                    $checkIfUserIsOrganizerOfEvent = $event->user_id === $userId;
                    if (! $checkIfUserIsOrganizerOfEvent) {
                        throw new UnauthorizedException('You cannot view a scheduled event');
                    }
                }

                $existingJoint = JoinEvent::getJoinedByTeamsForSameEvent($event->id, $userId);
            } else {
                if ($event->sub_action_private === 'private') {
                    throw new UnauthorizedException('Login to access this event.');
                }
                $existingJoint = null;
            }

            $joinEventIds = $event->joinEvents->pluck('id');

            $event->load([
                'joinEvents.team.roster' => function ($query) use ($joinEventIds) {
                    $query->select('id', 'team_id', 'join_events_id', 'user_id')
                          ->whereIn('join_events_id', $joinEventIds)
                          ->with(['user' => function ($query) {
                                $query->select('id', 'name', 'userBanner');
                            }]);
                },
                'matches',
            ]);
    
            $teamMap = collect();
            $teamList = collect();
            $event->joinEvents->each(function ($joinEvent) use (&$teamList, &$teamMap) {
                $teamMap[$joinEvent->team->id] = $joinEvent->team;
                $teamList->push($joinEvent->team);
            });
    
            $matchTeamIds = collect();
            $event->matches->each(function ($match) use ($teamMap, &$matchTeamIds) {
                $match->team1 = $teamMap->get($match->team1_id);
                $match->team2 = $teamMap->get($match->team2_id);
                $matchTeamIds->push($match->team1_id, $match->team2_id);
            });
    
            $defaultValues = BracketData::DEFAULT_VALUES;
            $matchesUpperCount = intval($event->tier->tierTeamSlot); 
            $valuesMap = ['Tournament' => 'tournament', 'League' => 'tournament'];
            $tournamentType = $event->type->eventType;
            $tournamentTypeFinal = $valuesMap[$tournamentType];
            $previousValues = BracketData::PREV_VALUES[(int)($matchesUpperCount)];
            $bracketList = BracketData::BRACKET_DATA[$matchesUpperCount][$tournamentTypeFinal];
            $bracketList = $event->matches->reduce(function ($bracketList, $match) {
                $path = "{$match->stage_name}.{$match->inner_stage_name}.{$match->order}";
    
                return data_set($bracketList, $path, [
                    'id' => $match->id,
                    'event_details_id' => $match->event_details_id,
                    'match_type' => $match->match_type,
                    'stage_name' => $match->stage_name,
                    'inner_stage_name' => $match->inner_stage_name,
                    'order' => $match->order,
                    'team1_id' => $match->team1_id,
                    'team2_id' => $match->team2_id,
                    'team1_teamBanner' => $match->team1->teamBanner,
                    'team2_teamBanner' => $match->team2->teamBanner,
                    'team1_teamName' => $match->team1->teamName,
                    'team2_teamName' => $match->team2->teamName,
                    'team1_roster' => $match->team1->roster,
                    'team2_roster' => $match->team2->roster,
                    'team1_score' => $match->team1_score,
                    'team2_score' => $match->team2_score,
                    'team1_position' => $match->team1_position,
                    'team2_position' => $match->team2_position,
                    'winner_id' => $match->winner_id,
                    'status' => $match->status,
                    'result' => $match->result,
                    'winner_next_position' => $match->winner_next_position,
                    'loser_next_position' => $match->loser_next_position,
                    'team1_name' => $match->team1->name ?? null,
                    'team2_name' => $match->team2->name ?? null,
                    'winner_name' => $match->winner->name ?? null,
                ]);
            }, $bracketList);
            
            if (empty($bracketList['tournament']['finals']['finals'])) {
                $bracketList['tournament']['finals']['finals'][] = [
                    'team1_position' => 'G1',
                    'team2_position' => 'G2',
                    'order' => 1,
                    'winner_next_position' => null,
                    'loser_next_position' => null,
                ];
            }
            
            if (empty($bracketList['tournament']['upperBracket']['eliminator1'])) {
                $bracketList['tournament']['upperBracket']['eliminator1'][] = [
                    'team1_position' => '',
                    'team2_position' => '',
                    'order' => 1,
                    'winner_next_position' => 'U1',
                    'loser_next_position' => 'L1',
                ];
            }


            return view('Participant.ViewEvent', [
                'event' => $event,
                'teamList' => $teamList,
                'matchesUpperCount' => $matchesUpperCount,
                'bracketList' => $bracketList,
                'defaultValues' => $defaultValues,
                'likesCount' => $likesCount, 
                'followersCount' => $followersCount, 
                'user' => $user, 
                'existingJoint' => $existingJoint,
                'previousValues' => $previousValues
                ]
            );
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function followOrganizer(Request $request)
    {
        $user = $request->attributes->get('user');
        $userId = $user->id;
        $organizerId = $request->organizer_id;
        $existingFollow = OrganizerFollow::where('participant_user_id', $userId)
            ->where('organizer_user_id', $organizerId)
            ->first();
        $organizer = User::findOrFail($organizerId);

        if ($existingFollow) {
            dispatch(new HandleFollows('Unfollow', [
                'subject_type' => User::class,
                'object_type' => User::class,
                'subject_id' => $userId,
                'object_id' => $organizerId,
                'action' => 'Follow',
            ]));

            $existingFollow->delete();

            return response()->json([
                'message' => 'Successfully Unfollowed the organizer',
                'isFollowing' => false,
            ], 201);
        }
        OrganizerFollow::create([
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

    public function registrationManagement(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where(function ($q) use ($user_id) {
            $q->where(function ($query) use ($user_id) {
                $query->whereHas('members', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 'accepted');
                });
            });
        })->with(
            $request->eventId ? [] : [
                'members' => function ($query) {
                    $query->where('status', 'accepted')->with('user');
                },
                'invitationList',
            ]
        )->first();
        $groupedPaymentsByEventAndTeamMember = [];
        $member = TeamMember::where('user_id', $user_id)->select('id')->first();
        if ($selectTeam) {
            if ($request->eventId) {
                $invitationListIds = [];
                $isRedirect = true;
                $eventId = $request->eventId;
            } else {
                $invitationListIds = $selectTeam->invitationList->pluck('event_id');
                $isRedirect = false;
                $eventId = null;
            }
            [
                $joinEventOrganizerIds, $joinEvents, $invitedEventOrganizerIds,
                $invitedEvents, $groupedPaymentsByEvent, $groupedPaymentsByEventAndTeamMember,
            ] = JoinEvent::fetchJoinEvents($id, $invitationListIds, $request->eventId);

            $userIds = array_unique(array_merge($joinEventOrganizerIds, $invitedEventOrganizerIds));
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            $isFollowing = OrganizerFollow::getIsFollowing($user_id, $userIds);
            ['joinEvents' => $joinEvents, 'activeEvents' => $activeEvents, 'historyEvents' => $historyEvents]
                = JoinEvent::processEvents($joinEvents, $isFollowing);

            // dd($joinEvents);

            return view(
                'Participant.RegistrationManagement',
                compact(
                    'selectTeam',
                    'invitedEvents',
                    'followCounts',
                    'groupedPaymentsByEvent',
                    'groupedPaymentsByEventAndTeamMember',
                    'member',
                    'joinEvents',
                    'isFollowing',
                    'isRedirect',
                    'eventId'
                )
            );
        }
        return redirect()->back()->with('error', "Team not found/ You're not authorized.");
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
        }
        $errorMessage = 'You have no team. Create a team.';

        return view('Participant.CreateTeamToRegister', ['id' => $id])->with('errorMessage', $errorMessage);
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
            if ($teamId === null || trim($teamId) === '') {
                throw new ErrorException('No team has been chosen');
            }
            $isAlreadyMember = TeamMember::isAlreadyMember($teamId, $userId);
            $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $userId, 'accepted');
            if ($hasJoinedOtherTeams) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $selectTeam = Team::getTeamAndMembersByTeamId($teamId);
            
            $event = EventDetail::with(['user' => function ($query) {
                $query->select('id', 'name', 'email');
            },
            ])
                ->select('id', 'user_id', 'eventName')
                ->find($id);

            if ($selectTeam && $isAlreadyMember) {
                [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs]
                    = $selectTeam->processTeamRegistration($user, $event);
                Event::dispatch(new JoinEventSignuped(
                    compact(
                        'memberList', 'organizerList', 'memberNotification',
                        'organizerNotification', 'allEventLogs'
                    )
                ));

                DB::commit();

                return view('Participant.EventNotify', compact('id', 'selectTeam'));
            }
            if (is_null($selectTeam)) {
                throw new ModelNotFoundException("Can't find team with the id!");
            }
            throw new ModelNotFoundException("Can't join a team you're not part of!");
        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
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
                },
                ]
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
                },
                ]);
                [$memberList, $organizerList, $memberNotification, $organizerNotification, $allEventLogs]
                    = $selectTeam->processTeamRegistration($user, $event);
                
                    event(new JoinEventSignuped(
                    compact(
                        'memberList', 'organizerList', 'memberNotification',
                        'organizerNotification', 'allEventLogs'
                    )
                ));
                DB::commit();

                return view('Participant.EventNotify', compact('id', 'selectTeam'));
            }
            session()->flash('errorMessage', 'You already have 5 teams!');

            return view('Participant.CreateTeamToRegister', ['id' => $id]);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                $errorMessage = 'Please choose a unique name!';
            } else {
                $errorMessage = $e->getMessage();
            }

            session()->flash('errorMessage', $errorMessage);

            return view('Participant.CreateTeamToRegister', ['id' => $id]);
        }
    }

    public function confirmOrCancel(Request $request)
    {
        try {
            $isToBeConfirmed = $request->join_status === 'confirmed';
            
            $successMessage = $isToBeConfirmed ? 'Your registration is now successfully confirmed!'
                : 'Your registration is now successfully canceled.';

            $joinEvent = JoinEvent::findOrFail( $request->join_event_id);
            $team = Team::findOrFail( $joinEvent->team_id);
            $event = EventDetail::findOrFail($joinEvent->event_details_id);
            // $isPermitted = true;
            $isPermitted = $joinEvent->payment_status === 'completed' &&
                ($request->join_status === 'confirmed' || $request->join_status === 'canceled');

            if ($isPermitted) {
                $joinEvent->join_status = $request->join_status;
                $joinEvent->save();

                if ($isToBeConfirmed) {
                    $team->confirmTeamRegistration($event);
                } else {
                    $discountsByUserAndType = 
                        $this->paymentService->refundPaymentsForEvents([$event->id], 0.5);

                    $team->cancelTeamRegistration($event, $discountsByUserAndType );
                }
                // dd($joinEvent, $request);
            } else {
                return back()->with('errorMessage', 'Error operation not permitted.');
            }

            return back()->with('successMessage', $successMessage);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function showSuccess(Request $request)
    {
        // try {
        //     $user = $request->get('user');
        //     $userId = $user->id;

        // } catch (ModelNotFoundException|UnauthorizedException $e) {
        //     return $this->showErrorOrganizer($e->getMessage());
        // } catch (Exception $e) {
        //     return $this->showErrorOrganizer("Event can't be retieved with id: $id");
        // }

        // return view('Participant.RegistrationSuccess', [
        //     'event' => $event,
        //     'mappingEventState' => EventDetail::mappingEventStateResolve(),
        //     'isUser' => $isUserSameAsAuth,
        //     'livePreview' => 1,
        // ]);
    }
}
