<?php

namespace App\Http\Controllers\Participant;

use App\Events\JoinEventSignuped;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\ValidateBracketUpdateRequest;
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

    public function __construct(PaymentService $paymentService, EventMatchService $eventMatchService)
    {
        $this->paymentService = $paymentService;
        $this->eventMatchService = $eventMatchService;
    }

    

    public function viewEvent(ParticipantViewEventRequest $request, $id)
    {
        try {
            $event = $request->getEvent();
            $user = $request->getStoredUser();
            $existingJoint = $request->getJoinEvent();
            $viewData = $this->eventMatchService->getEventViewData($event, $user, $existingJoint);

            $bracketData = $this->eventMatchService->generateBrackets($event, false, $existingJoint);

            return view('Public.ViewEvent', [...$viewData, 'livePreview' => 0, ...$bracketData]);
        } catch (Exception $e) {
            Log::error($e);
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function registrationManagement(Request $request, $id)
    {
        $logged_user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $id)
            ->where(function ($q) use ($logged_user_id) {
                $q->where(function ($query) use ($logged_user_id) {
                    $query->whereHas('members', function ($query) use ($logged_user_id) {
                        $query->where('user_id', $logged_user_id)->where('status', 'accepted');
                    });
                });
            })
            ->with([
                'members' => function ($query) {
                    $query->where('status', 'accepted')->with(['user']);
                },
                'invitationList',
            ])
            ->first();
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
            [$joinEventOrganizerIds, $joinEvents, $invitedEventOrganizerIds, $invitedEvents, $groupedPaymentsByEvent, $groupedPaymentsByEventAndTeamMember] = JoinEvent::fetchJoinEvents($id, $invitationListIds);
            // dd($joinEvents, $invitedEvents);
            $userIds = array_unique(array_merge($joinEventOrganizerIds, $invitedEventOrganizerIds));
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            $isFollowing = OrganizerFollow::getIsFollowing($logged_user_id, $userIds);
            $joinEvents = JoinEvent::processJoins($joinEvents, $isFollowing);
            $invitedEvents = JoinEvent::processJoins($invitedEvents, $isFollowing);

            $maxRosterSize = config('constants.ROSTER_SIZE');
            $signupStatusEnum = config('constants.SIGNUP_STATUS');
            $paymentLowerMin = config('constants.STRIPE.MINIMUM_RM');
            if ($request->has('scroll')) {
                session()->flash('scroll', $request->scroll);
            }

            return view('Participant.RegistrationManagement', compact('selectTeam', 'invitedEvents', 'followCounts', 'groupedPaymentsByEvent', 'groupedPaymentsByEventAndTeamMember', 'member', 'joinEvents', 'isFollowing', 'isRedirect', 'eventId', 'maxRosterSize', 'paymentLowerMin', 'signupStatusEnum'));
        }

        return redirect()->back()->with('error', "Team not found/ You're not authorized.");
    }

    public function redirectToSelectOrCreateTeamToJoinEvent(Request $request, $id)
    {
        $user_id = $request->attributes->get('user')->id;
        [
            'teamList' => $selectTeam,
            'count' => $count,
        ] = Team::getUserTeamListAndCount($user_id);

        if ($selectTeam) {
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
            $isPartOfRoster = JoinEvent::isPartOfRoster($id, $userId);
            $joinTeamButNotRoster = JoinEvent::where('event_details_id', $id)->where('team_id', $teamId)->first();

            if ($joinTeamButNotRoster) {
                $errorMessage = 'You have already joined this event with your team before!';
                return redirect()
                    ->route('participant.register.manage', ['id' => $teamId])
                    ->with('errorMessage', $errorMessage)
                    ->with('scroll', $joinTeamButNotRoster->id);
            }

            if ($isPartOfRoster) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $selectTeam = Team::getTeamAndMembersByTeamId($teamId);

            $event = EventDetail::with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email');
                },
            ])
                ->select('id', 'eventName', 'eventBanner', 'event_tier_id', 'user_id')
                ->with(['tier:id,eventTier', 'user:id,name,userBanner', 'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'])
                ->find($id);

            $status = $event->getRegistrationStatus();
            if ($status == config('constants.SIGNUP_STATUS.CLOSED')) {
                return $this->showErrorParticipant('Signup is closed right now!');
            }

            if ($selectTeam && $isAlreadyMember) {
                $join_id = $selectTeam->processTeamRegistration($user->id, $event->id);
                Event::dispatch(new JoinEventSignuped(compact('user', 'join_id', 'event', 'selectTeam')));
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
                $errorMessage = $e->getMessage();
                return $this->showErrorParticipant($errorMessage);
            }
        }
    }

    public function likeEvent(LikeRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->attributes->get('user');
        $existingLike = Like::where('user_id', $user->id)->where('event_id', $validatedData['event_id'])->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully unliked the event',
                    'isLiked' => false,
                ],
                201,
            );
        }
        Like::create([
            'user_id' => $user->id,
            'event_id' => $validatedData['event_id'],
        ]);

        return response()->json(
            [
                'success' => true,
                'message' => 'Successfully liked the event',
                'isLiked' => true,
            ],
            201,
        );
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

            $isPartOfRoster = JoinEvent::isPartOfRoster($id, $user_id);
            if ($isPartOfRoster) {
                throw new Exception('One of your teams has joined this event already!');
            }

            $event = EventDetail::select('id', 'eventName', 'eventBanner', 'event_tier_id', 'user_id')
                ->with(['tier:id,eventTier', 'user:id,name,userBanner', 'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'])
                ->find($id);

            $status = $event->getRegistrationStatus();

            if ($status == config('constants.SIGNUP_STATUS.CLOSED')) {
                return $this->showErrorParticipant('Signup is closed right now!');
            }

            if ($count < 5) {
                $selectTeam = new Team();
                $selectTeam = Team::validateAndSaveTeam($request, $selectTeam, $user_id);
                TeamMember::bulkCreateTeanMembers($selectTeam->id, [$user_id], 'accepted');
                TeamCaptain::insert([
                    'team_member_id' => $selectTeam->members[0]->id,
                    'teams_id' => $selectTeam->id,
                ]);
                $teamMembers = $selectTeam->members->load([
                    'user' => function ($q) {
                        $q->select(['name', 'id', 'email', 'userBanner']);
                    },
                ]);
                $join_id = $selectTeam->processTeamRegistration($user->id, $event->id);
                Event::dispatch(new JoinEventSignuped(compact('user', 'join_id', 'event', 'selectTeam')));
                DB::commit();

                return view('Participant.EventNotify', compact('id', 'selectTeam'));
            }
            session()->flash('errorMessage', 'You already have 5 teams!');

            return view('Participant.CreateTeamToRegister', ['id' => $id]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $errorMessage = 'This team name was taken. Please change to another name.';
            } else {
                $errorMessage = 'Error updating team: ' . $e->getMessage();
            }

            DB::rollBack();
            session()->flash('errorMessage', $errorMessage);
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
        $routeBack = null;
        try {
            $user = $request->attributes->get('user');
            $errorMessage = null;
            $isToBeConfirmed = $request->join_status === 'confirmed';
            $rosterMember = RosterMember::where([
                'join_events_id' => $request->join_event_id,
                'user_id' => $request->attributes->get('user')?->id,
            ])->first();


            if (!($rosterMember && $rosterMember->team_id)) {
                return $this->showErrorParticipant("Doesn't belong to this roster!");
            }

            $routeBack = route('participant.register.manage', ['id' => $rosterMember->team_id, 'scroll' => $request->join_event_id]);

            $successMessage = $isToBeConfirmed ? 'Your registration is now successfully confirmed!' : 'You have started a vote to cancel registratin.';

            $joinEvent = JoinEvent::where('id', $request->join_event_id)->firstOrFail();

            $team = Team::where('id', $joinEvent->team_id)->firstOrFail();

            $event = EventDetail::where('id', $joinEvent->event_details_id)
                ->select('id', 'eventName', 'eventBanner', 'event_tier_id', 'user_id')
                ->with(['tier:id,eventTier', 'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'])
                ->firstOrFail();

            $status = $event->getRegistrationStatus();

            if ($status == config('constants.SIGNUP_STATUS.CLOSED')) {
                return $this->showErrorParticipant('Signup is closed right now!');
            }

            if ($isToBeConfirmed) {
                $isPermitted = $joinEvent->payment_status === 'completed';
                if (!$isPermitted) {
                    $errorMessage = 'Unformtunately, your payment is not yet cleared!';
                }

                $isPermitted = $joinEvent->vote_ongoing == null && $isPermitted;
                if (!$isPermitted) {
                    $errorMessage = 'Unformtunately, the vote is ongoing';
                }

                $isPermitted = $joinEvent->join_status == 'pending';
                if (!$isPermitted) {
                    $errorMessage = $joinEvent->join_stauts == 'confirmed' ? 'You have already confirmed your registration!' : 'Your registration is already canceled.';
                }
            } else {
                $isPermitted = $joinEvent->vote_ongoing == null;
                if (!$isPermitted) {
                    $errorMessage = 'Unformtunately, the vote is ongoing';
                }

                $isPermitted = $joinEvent->join_status == 'pending' || $joinEvent->join_status == 'confirmed';
                if (!$isPermitted) {
                    $errorMessage = $joinEvent->join_stauts == 'canceled' ? 'You have already canceled your registration!' : 'Your registration is in a weird state.';
                }
            }

            if ($isPermitted) {
                $joinEvent->load(['roster', 'roster.user']);
                if ($isToBeConfirmed) {
                    $joinEvent->join_status = $request->join_status;

                    dispatch(
                        new HandleEventJoinConfirm('Confirm', [
                            'selectTeam' => $team,
                            'user' => $user,
                            'event' => $event,
                            'joinEvent' => $joinEvent,
                            'join_id' => $joinEvent->id,
                        ]),
                    );
                    $joinEvent->save();
                } else {
                    $rosterCount = $joinEvent->roster->count();
                    if ($rosterCount == 1) {
                        $errorMessage = "You're just one member. Please leave the roster instead!";
                        return redirect($routeBack)->with('errorMessage', $errorMessage)->with('scroll', $request->join_event_id);
                    } else {
                        $joinEvent->vote_starter_id = $user->id;
                        $joinEvent->vote_ongoing = true;
                        $joinEvent->save();

                        dispatch(
                            new HandleEventJoinConfirm('VoteStart', [
                                'selectTeam' => $team,
                                'user' => $user,
                                'joinEvent' => $joinEvent,
                                'event' => $event,
                                'join_id' => $joinEvent->id,
                            ]),
                        );
                    }
                }
            } else {
                return redirect($routeBack)->with('errorMessage', $errorMessage)->with('scroll', $request->join_event_id);
            }

            return redirect($routeBack)->with('successMessage', $successMessage)->with('scroll', $request->join_event_id);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function validateBracket(ValidateBracketUpdateRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Successfully verified!',
        ]);
    }
}
