<?php

namespace App\Http\Controllers\Organizer;

use App\Exceptions\EventChangeException;
use App\Exceptions\TimeGreaterException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\OrganizerViewEventRequest;
use App\Jobs\HandleEventJoinConfirm;
use App\Models\EventCategory;
use App\Models\EventDetail;
use App\Models\EventTier;
use App\Models\EventType;
use App\Models\JoinEvent;
use App\Models\Organizer;
use App\Models\Team;
use App\Models\User;
use App\Services\EventMatchService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;
use App\Jobs\HandleEventUpdate;
use App\Models\OrganizerFollow;
use App\Jobs\CreateUpdateEventTask;

class OrganizerEventController extends Controller
{
    private $paymentService;

    private $eventMatchService;

    public function __construct(PaymentService $paymentService, EventMatchService $eventMatchService)
    {
        $this->paymentService = $paymentService;
        $this->eventMatchService = $eventMatchService;
    }

    public function home()
    {
        if (Session::has('intended')) {
            $intendedUrl = Session::get('intended');

            Session::forget('intended');

            return redirect($intendedUrl);
        }

        return view('Organizer.Home');
    }

    public function index(Request $request)
    {
        $eventCategoryList = EventCategory::all();
        $user = $request->attributes->get('user');
        $eventTierList = EventTier::byUserOrNullUser($user->id)->get();
        $eventTypeList = EventType::all();
        $user = $request->attributes->get('user');
        $userId = $user->id;
        $count = 8;
        $organizer = Organizer::where('user_id', $userId)->first();
        $eventListQuery = EventDetail::filterEvents($request);
        $eventList = $eventListQuery
            ->with(['tier', 'type', 'game', 'user', 'signup'])
            ->where('user_id', $user->id)
            ->withCount([
                'joinEvents' => function ($q) {
                    $q->where('join_status', 'confirmed');
                },
            ])
            ->simplePaginate();

        $joinTeamIds = [];

        $results = DB::table('join_events')->select('join_events.event_details_id', DB::raw('COUNT(team_members.id) as accepted_members_count'))->join('team_members', 'join_events.team_id', '=', 'team_members.team_id')->where('team_members.status', '=', 'accepted')->groupBy('join_events.event_details_id')->get();

        $followersCount = OrganizerFollow::where('organizer_user_id', $user->id)->count();

        $joinEventDetailsMap = $results->pluck('accepted_members_count', 'event_details_id');

        $outputArray = compact('eventList', 'count', 'user', 'eventCategoryList', 'eventTierList', 'eventTypeList', 'followersCount');

        return view('Organizer.ManageEvent', $outputArray);
    }

    public function search(Request $request)
    {
        $userId = $request->userId;
        $user = User::find($userId);
        $organizer = Organizer::where('user_id', $user->id)->first();
        $count = 8;
        $eventListQuery = EventDetail::filterEventsFull($request);
        $followersCount = OrganizerFollow::where('organizer_user_id', $user->id)->count();

        $eventList = $eventListQuery
            ->where('event_details.user_id', $userId)
            ->with(['tier', 'type', 'game'])
            ->withCount([
                'joinEvents' => function ($q) {
                    $q->where('join_status', 'confirmed');
                },
            ])
            ->simplePaginate();

        $results = DB::table('join_events')->select('join_events.event_details_id', DB::raw('COUNT(team_members.id) as accepted_members_count'))->join('team_members', 'join_events.team_id', '=', 'team_members.team_id')->where('team_members.status', '=', 'accepted')->groupBy('join_events.event_details_id')->get();

        $joinEventDetailsMap = $results->pluck('accepted_members_count', 'event_details_id');

        $outputArray = compact('eventList', 'count', 'followersCount', 'user', 'organizer');
        $view = view('includes.ManageEvent.ManageEventScroll', $outputArray)->render();

        return response()->json(['html' => $view], 200);
    }

    public function create(Request $request): View
    {
        $user = $request->get('user');
        $eventCategory = EventCategory::all();
        $eventTierList = EventTier::byUserOrNullUser($user->id)->get();
        $eventTypeList = EventType::all();

        return view('Organizer.CreateEvent', [
            'eventCategory' => $eventCategory,
            'event' => null,
            'editMode' => 0,
            'tier' => null,
            'game' => null,
            'type' => null,
            'eventTierList' => $eventTierList,
            'eventTypeList' => $eventTypeList,
        ]);
    }

    public function showSuccess(Request $request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError($userId, $id);

            $isUserSameAsAuth = true;
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer("Event can't be retieved with id: {$id}");
        }

        return view('Organizer.CreateEventSuccess', [
            'event' => $event,
            'isUser' => $isUserSameAsAuth,
            'livePreview' => 0,
        ]);
    }

    public function show(OrganizerViewEventRequest $request, $id): View
    {
        try {
            $event = $request->getEvent();
            $user = $request->getStoredUser();
            $existingJoint = $request->getJoinEvent();

            $viewData = $this->eventMatchService->getEventViewData($event, $user, $existingJoint);

            $bracketData = $this->eventMatchService->generateBrackets($event, false, $existingJoint);

            return view('Public.ViewEvent', [...$viewData, 'livePreview' => $request->query('live') === 'true' ? 1 : 0, ...$bracketData]);
        } catch (Exception $e) {
            Log::error($e);

            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $eventDetail = new EventDetail;
            [$eventDetail] = EventDetail::storeLogic($eventDetail, $request);
            $eventDetail->user_id = $request->get('user')->id;
            $eventDetail->save();
            try {
            } catch (Exception $e) {
                throw new Exception('Failed to create signup tables: '.$e->getMessage());
            }

            try {
                CreateUpdateEventTask::dispatch($eventDetail);
            } catch (Exception $e) {
                throw new Exception('Failed to queue event task creation: '.$e->getMessage());
            }

            DB::commit();
            if ($request->livePreview === 'true') {
                return redirect('organizer/event/'.$eventDetail->id.'?live=true');
            }

            if ($request->goToCheckoutPage === 'yes') {
                return redirect('organizer/event/'.$eventDetail->id.'/checkout');
            }

            return redirect('organizer/event/'.$eventDetail->id.'/success');
        } catch (TimeGreaterException|EventChangeException $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError($userId, $id);

            $status = $event->statusResolved();

            if ($status === 'ENDED') {
                return $this->showErrorOrganizer("Event has already ended id: {$id}");
            }

            $eventCategory = EventCategory::all();
            $eventTierList = EventTier::byUserOrNullUser($userId)->get();
            $eventTypeList = EventType::all();

            return view('Organizer.EditEvent', [
                'eventCategory' => $eventCategory,
                'event' => $event,
                'tier' => $event->tier,
                'game' => $event->game,
                'type' => $event->type,
                'eventTierList' => $eventTierList,
                'eventTypeList' => $eventTypeList,
                'editMode' => 1,
            ]);
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer("Event not found for id: {$id}");
        }
    }

    public function updateForm($id, Request $request)
    {
        try {
            $eventId = $id;
            $user = $request->get('user');
            $userId = $user->id;

            $eventDetail = EventDetail::findEventWithRelationsAndThrowError($userId, $id, null, ['type', 'tier', 'game', 'matches', 'deadlines']);

            DB::beginTransaction();

            if ($eventId) {
                [$eventDetail, $isTimeSame] = EventDetail::storeLogic($eventDetail, $request);
                $eventDetail->user_id = $request->get('user')->id;
                $eventDetail->save();
                if (! $isTimeSame) {
                    dispatch(new HandleEventUpdate($eventDetail));
                }
                try {
                    CreateUpdateEventTask::dispatch($eventDetail);
                } catch (Exception $e) {
                    throw new Exception('Failed to queue event task creation: '.$e->getMessage());
                }

                DB::commit();
                if ($request->livePreview === 'true') {
                    return redirect('organizer/event/'.$eventId.'/live');
                }
                if ($request->goToCheckoutPage === 'yes') {
                    return redirect('organizer/event/'.$eventId.'/checkout');
                }

                return redirect('organizer/event/'.$eventId.'/success');
            }

            return $this->showErrorOrganizer("Event not found for id: {$id}");
        } catch (TimeGreaterException|EventChangeException $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage().' '.$e->getTraceAsString());
        }
    }

    public function storeNotify(Request $request, $id)
    {
        $event = EventDetail::findOrFail($id);
        $event->update(['willNotify' => $request->notify]);

        return response()->json(['success' => true, 'message' => 'Notification settings updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->attributes->get('user');

            $event = EventDetail::where('id', $id)->with('user')->firstOrFail();

            $joinList = JoinEvent::where('event_details_id', $id)
                ->whereNot('join_status', 'canceled')
                ->with(['roster', 'roster.user', 'eventDetails.user:id,name,userBanner', 'eventDetails.tier:id,eventTier', 'team:id,teamName,teamBanner'])
                ->get();

            $joinList->each(function (JoinEvent $join) use ($event, $user) {
                $discountsByUserAndType = $this->paymentService->refundPaymentsForEvents($join->id, 0);
                dispatch(
                    new HandleEventJoinConfirm('OrgCancel', [
                        'selectTeam' => $join->team,
                        'user' => $user,
                        'event' => $event,
                        'discount' => $discountsByUserAndType,
                        'join_id' => $join->id,
                        'joinEvent' => $join,
                    ]),
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully',
            ]);
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            Log::error('Failed to delete event: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            Log::error('General exception in event deletion: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
