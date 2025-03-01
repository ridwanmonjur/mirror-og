<?php

namespace App\Http\Controllers\Participant;

use App\Events\JoinEventSignuped;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LikeRequest;
use App\Jobs\HandleEventJoinConfirm;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\OrganizerFollow;
use App\Models\RosterMember;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Services\PaymentService;
use ErrorException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use App\Services\EventMatchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Match\ParticipantViewEventRequest;
use App\Models\Participant;

class ParticipantEventController extends Controller
{
    private $paymentService;
    private $eventMatchService;

    public function __construct(
        PaymentService $paymentService,
        EventMatchService $eventMatchService
    )
    {
        $this->paymentService = $paymentService;
        $this->eventMatchService = $eventMatchService;
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
        $currentDateTime = Carbon::now()->utc();
        $events = EventDetail::landingPageQuery($request, $currentDateTime)
            ->paginate($count);
        

        $output = [
            'events' => $events,
            'id' => $userId,
        ];

        if ($request->ajax()) {
            $view = view('__CommonPartials.LandingPageHomeScroll', $output)->render();

            return response()->json(['html' => $view]);
        }
        return view('LandingPage', $output);
    }

    public function viewEvent(ParticipantViewEventRequest $request, $id)
    {
        try {
            $event = $request->getEvent();
            $user = $request->getStoredUser();
            $existingJoint = $request->getJoinEvent();
            $viewData = $this->eventMatchService->getEventViewData(
                $event, $user, $existingJoint
            );
    
            $bracketData = $this->eventMatchService->generateBrackets(
                $event,
                false, 
                $existingJoint,
            );

            return view('Shared.ViewEvent', [
                    ...$viewData,
                    'livePreview' => 0,
                    ...$bracketData,
                ]
            );
        } catch (Exception $e) {
            Log::error($e);
            return $this->showErrorParticipant($e->getMessage());
        }
    }

  
    public function registrationManagement(Request $request, $id)
    {
        $logged_user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)->where(function ($q) use ($logged_user_id) {
            $q->where(function ($query) use ($logged_user_id) {
                $query->whereHas('members', function ($query) use ($logged_user_id) {
                    $query->where('user_id', $logged_user_id)->where('status', 'accepted');
                });
            });
        })
            ->with(
                [
                    'members' => function ($query) {
                        $query->where('status', 'accepted')->with(['user']);
                    },
                    'invitationList',
                ]
            )->first();
        $groupedPaymentsByEventAndTeamMember = [];
        $member = TeamMember::where('user_id', $logged_user_id)->select('id')->first();
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
            $isFollowing = OrganizerFollow::getIsFollowing($logged_user_id, $userIds);
            ['joinEvents' => $joinEvents, 'activeEvents' => $activeEvents, 'historyEvents' => $historyEvents]
                = JoinEvent::processEvents($joinEvents, $isFollowing);

            $maxRosterSize = config("constants.ROSTER_SIZE");  
            $signupStatusEnum = config("constants.SIGNUP_STATUS");
            $paymentLowerMin = config("constants.STRIPE.MINIMUM_RM");
            if ($request->has('scroll')) {
                session()->flash('scroll', $request->scroll);
            }

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
                    'eventId',
                    'maxRosterSize',
                    'paymentLowerMin',
                    'signupStatusEnum',
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

        $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $user_id);
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
            $userId = $user->id;
            $teamId = $request->input('selectedTeamId');
            if ($teamId === null || trim($teamId) === '') {
                throw new ErrorException('No team has been chosen');
            }
            $isAlreadyMember = TeamMember::isAlreadyMember($teamId, $userId);
            $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $userId);
            if ($hasJoinedOtherTeams) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $selectTeam = Team::getTeamAndMembersByTeamId($teamId);
            
            $event = EventDetail::with(['user' => function ($query) {
                    $query->select('id', 'name', 'email');
                }])
                ->select('id', 'user_id', 'eventName')
                ->find($id);

            if ($selectTeam && $isAlreadyMember) {
                Event::dispatch(new JoinEventSignuped(compact('user', 'event', 'selectTeam')));
                $selectTeam->processTeamRegistration( $user->id, $event->id);
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

    public function likeEvent(LikeRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->attributes->get('user');
        $existingLike = Like::where('user_id', $user->id)
            ->where('event_id', $validatedData['event_id'])
            ->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unliked the event',
                'isLiked' => false,
            ], 201);
        }
        Like::create([
            'user_id' => $user->id,
            'event_id' => $validatedData['event_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully liked the event',
            'isLiked' => true,
        ], 201);
    }


    public function createTeamToJoinEvent(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->attributes->get('user');
            $user_id = $user->id;
            [
                'teamList' => $selectTeam,
                'count' => $count,
            ] = Team::getUserTeamListAndCount($user_id);

            $hasJoinedOtherTeams = JoinEvent::hasJoinedByOtherTeamsForSameEvent($id, $user_id);
            if ($hasJoinedOtherTeams) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $event = EventDetail::select('id', 'user_id', 'eventName')->with(
                ['user' => function ($q) {
                    $q->select('id', 'name', 'email');
                }]
            )->find($id);

            if ($count < 5) {
                $selectTeam = new Team();
                $selectTeam = Team::validateAndSaveTeam($request, $selectTeam, $user_id);
                TeamMember::bulkCreateTeanMembers($selectTeam->id, [$user_id], 'accepted');
                TeamCaptain::insert([
                    'team_member_id' => $selectTeam->members[0]->id,
                    'teams_id' => $selectTeam->id,
                ]);
                $teamMembers = $selectTeam->members->load(['user' => function ($q) {
                        $q->select(['name', 'id', 'email', 'userBanner']);
                    },
                ]);
                Event::dispatch(new JoinEventSignuped(compact($user, $event, $selectTeam)));
                $selectTeam->processTeamRegistration( $user->id, $event->id);
                DB::commit();

                return view('Participant.EventNotify', compact('id', 'selectTeam'));
            }
            session()->flash('errorMessage', 'You already have 5 teams!');

            return view('Participant.CreateTeamToRegister', ['id' => $id]);
        } catch (Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            session()->flash('errorMessage', $errorMessage);
            return view('Participant.CreateTeamToRegister', ['id' => $id]);
        }
    }

    public function confirmOrCancel(Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            $isToBeConfirmed = $request->join_status === 'confirmed';
            $rosterMember = RosterMember::where([
                'join_events_id' => $request->join_event_id,
                'user_id' => $request->attributes->get('user')?->id
            ])->first();

            if (!$rosterMember) {
                return back()->with('errorMessage', 'Must be a member of the roster.');
            }
            
            $successMessage = $isToBeConfirmed ? 'Your registration is now successfully confirmed!'
                : 'You have started a vote to cancel registratin.';
            $joinEvent = JoinEvent::where('id', $request->join_event_id)
                ->firstOrFail();
            $team = Team::where( 'id', $joinEvent->team_id)
                ->firstOrFail();
            $event = EventDetail::findOrFail($joinEvent->event_details_id);
            $isPermitted = $joinEvent->payment_status === 'completed' &&
                ($request->join_status === 'confirmed' || $request->join_status === 'canceled');

            if ($isPermitted) {
                if ($isToBeConfirmed) {
                    $joinEvent->join_status = $request->join_status;
                    dispatch(new HandleEventJoinConfirm('Confirm', [
                        'selectTeam' => $team,
                        'user' => $user,
                        'event' => $event,
                    ]));
                    $joinEvent->save();
                } else {
                    $voteToQuit = true;
                    $rosterMember->vote_to_quit = $voteToQuit;
                    $rosterMember->save();
                    $joinEvent->vote_starter_id = $user->id;
                    $joinEvent->vote_ongoing = $user->id;
                    [$leaveRatio, $stayRatio] = $joinEvent->decideRosterLeaveVote();

                    if ($leaveRatio > 0.5 || $stayRatio > 0.5) {
                        if ($leaveRatio > 0.5) {
                            $team->load(['members' => function ($query) {
                                $query->where('status', 'accepted')->with('user');
                            }]);
                            $discountsByUserAndType = $this->paymentService->refundPaymentsForEvents([$event->id], 0.5);
                            $joinEvent->vote_ongoing = false;
                            $joinEvent->join_status = "canceled";
                            dispatch(new HandleEventJoinConfirm('VoteEnd', [
                                'selectTeam' => $team,
                                'user' => $user,
                                'event' => $event,
                                'discount' => $discountsByUserAndType,
                                'willQuit' => true
                            ]));
                        }

                        if ($stayRatio > 0.5) {
                            $joinEvent->vote_ongoing = false;
                            $joinEvent->join_status = $joinEvent->payment_status == "completed" ? 
                                "confirmed" : "pending";
                            
                            dispatch(new HandleEventJoinConfirm('VoteEnd', [
                                'selectTeam' => $team,
                                'user' => $user,
                                'event' => $event,
                                'willQuit' => false
                            ]));
                        }

                    } else {
                        dispatch(new HandleEventJoinConfirm('VoteStart', [
                            'selectTeam' => $team,
                            'user' => $user,
                            'event' => $event,
                        ]));
                    }
                    
                    $joinEvent->save();
                }
            } else {
                return back()->with('errorMessage', 'This cancel operation is not permitted at this stage.')
                    ->with('scroll', $request->join_event_id) ;
            }

            return back()->with('successMessage', $successMessage)
                ->with('scroll', $request->join_event_id) ;
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

  
}
