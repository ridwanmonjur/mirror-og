<?php

namespace App\Http\Controllers\Participant;

use App\Events\JoinEventSignuped;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LikeRequest;
use App\Jobs\HandleFollows;
use App\Models\Achievements;
use App\Models\AwardResults;
use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\EventJoinResults;
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
use App\Services\EventMatchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Match\ParticipantViewEventRequest;

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

  
}
