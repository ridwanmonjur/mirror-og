<?php

namespace App\Http\Controllers\Organizer;

use App\Exceptions\EventChangeException;
use App\Exceptions\TimeGreaterException;
use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Models\EventDetail;
use App\Models\EventTier;
use App\Models\EventType;
use App\Models\Organizer;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;

class OrganizerEventController extends Controller
{
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
        $eventTierList = EventTier::all();
        $eventTypeList = EventType::all();
        $user = Auth::user();
        $userId = $user->id;
        $count = 8;
        $organizer = Organizer::where('user_id', $userId)->first();
        $eventListQuery = EventDetail::generateOrganizerPartialQueryForFilter($request);
        $mappingEventState = EventDetail::mappingEventStateResolve();
        $eventList = $eventListQuery
            ->with(['tier', 'type', 'game'])
            ->where('user_id', $user->id)
            ->paginate($count);

        $joinTeamIds = [];

        $acceptedMembersCount = [];
        $results = DB::table('join_events')
            ->select('join_events.event_details_id', DB::raw('COUNT(team_members.id) as accepted_members_count'))
            ->join('team_members', 'join_events.team_id', '=', 'team_members.team_id')
            ->where('team_members.status', '=', 'accepted')
            ->groupBy('join_events.event_details_id')
            ->get();

        $joinEventDetailsMap = $results->pluck('accepted_members_count', 'event_details_id');
        foreach ($eventList as $event) {
            if ($joinEventDetailsMap->has($event->id)) {
                $event->acceptedMembersCount = $joinEventDetailsMap[$event->id];
            } else {
                $event->acceptedMembersCount = 0;
            }
        }

        $outputArray = compact(
            'eventList',
            'count',
            'user',
            'organizer',
            'mappingEventState',
            'eventCategoryList',
            'eventTierList',
            'eventTypeList'
        );

        return view('Organizer.ManageEvent', $outputArray);
    }

    public function search(Request $request)
    {
        $userId = $request->userId;
        $user = User::find($userId);
        $organizer = Organizer::where('user_id', $user->id)->first();
        $count = 8;
        $eventListQuery = EventDetail::generateOrganizerFullQueryForFilter($request);

        $eventList = $eventListQuery
            ->where('event_details.user_id', $userId)
            ->with(['tier', 'type', 'game'])
            ->paginate($count);

        $acceptedMembersCount = [];
        $results = DB::table('join_events')
            ->select('join_events.event_details_id', DB::raw('COUNT(team_members.id) as accepted_members_count'))
            ->join('team_members', 'join_events.team_id', '=', 'team_members.team_id')
            ->where('team_members.status', '=', 'accepted')
            ->groupBy('join_events.event_details_id')
            ->get();

        $joinEventDetailsMap = $results->pluck('accepted_members_count', 'event_details_id');
        foreach ($eventList as $event) {
            if ($joinEventDetailsMap->has($event->id)) {
                $event->acceptedMembersCount = $joinEventDetailsMap[$event->id];
            } else {
                $event->acceptedMembersCount = 0;
            }
        }

        $mappingEventState = EventDetail::mappingEventStateResolve();
        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        $view = view('Organizer.__ManageEventPartials.ManageEventScroll', $outputArray)->render();

        return response()->json(['html' => $view], 200);
    }

    public function create(): View
    {
        $eventCategory = EventCategory::all();
        $eventTierList = EventTier::all();
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

    public function storeEventBanner($file)
    {
        $fileNameInitial = 'eventBanner-'.time().'.'.$file->getClientOriginalExtension();
        $fileNameFinal = "images/events/{$fileNameInitial}";
        $file->storeAs('images/events/', $fileNameInitial);

        return $fileNameFinal;
    }

    public function destroyEventBanner($file)
    {
        $fileNameInitial = str_replace('images/events/', '', $file);
        $fileNameFinal = "images/events/{$fileNameInitial}";

        if (file_exists($fileNameFinal)) {
            unlink($fileNameFinal);
        }
    }

    public function showLive(Request $request): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $isUserSameAsAuth = true;
            $mappingEventState = EventDetail::mappingEventStateResolve();
            $livePreview = true;

            $event = EventDetail::findEventWithRelationsAndThrowError(
                $userId,
                $request->id,
                ['joinEvents' => function ($query) {
                    $query->with(['members' => function ($query) {
                        $query->where('status', 'accepted');
                    },
                    ]);
                },
                ],
                null
            );

            $event->acceptedMembersCount = 0;
            foreach ($event->joinEvents as $joinEvent) {
                $event->acceptedMembersCount += $joinEvent->members->count();
            }

            $outputArray = compact(
                'event',
                'user',
                'livePreview',
                'mappingEventState'
            );

            return view('Organizer.ViewEvent', $outputArray);
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer("Event not found with id: {$request->id}");
        }
    }

    public function showSuccess(Request $request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError(
                $userId,
                $id,
                ['joinEvents' => function ($query) {
                    $query->with(['members' => function ($query) {
                        $query->where('status', 'accepted');
                    },
                    ]);
                },
                ],
                null
            );

            $event->acceptedMembersCount = 0;
            foreach ($event->joinEvents as $joinEvent) {
                $event->acceptedMembersCount += $joinEvent->members->count();
            }

            $isUserSameAsAuth = true;
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer("Event can't be retieved with id: {$id}");
        }

        return view('Organizer.CreateEventSuccess', [
            'event' => $event,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'isUser' => $isUserSameAsAuth,
            'livePreview' => 1,
        ]);
    }

    public function show(Request $request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError(
                $userId,
                $id,
                ['joinEvents' => function ($query) {
                    $query->with(['members' => function ($query) {
                        $query->where('status', 'accepted');
                    },
                    ]);
                },
                ],
                null
            );

            // dd($event);

            $event->acceptedMembersCount = 0;
            foreach ($event->joinEvents as $joinEvent) {
                $event->acceptedMembersCount += $joinEvent->members->count();
            }

            // dd($event);

            return view('Organizer.ViewEvent', [
                'event' => $event,
                'mappingEventState' => EventDetail::mappingEventStateResolve(),
                'isUser' => true,
                'livePreview' => 0,
            ]);
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer("Event not retrieved with id: {$id}");
        }
    }

    public function store(Request $request)
    {
        try {
            $eventDetail = new EventDetail();
            $eventDetail = EventDetail::storeLogic($eventDetail, $request);
            $eventDetail->user_id = $request->get('user')->id;
            $eventDetail->save();

            if ($request->livePreview === 'true') {
                return redirect('organizer/event/'.$eventDetail->id.'/live');
            }
            if ($request->goToCheckoutPage === 'yes') {
                return redirect('organizer/event/'.$eventDetail->id.'/checkout');
            }
            return redirect('organizer/event/'.$eventDetail->id.'/success');
        } catch (TimeGreaterException|EventChangeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong with saving data!');
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError(
                $userId,
                $id,
                null,
                null
            );
            $status = $event->statusResolved();

            if ($status === 'ENDED') {
                return $this->showErrorOrganizer("Event has already ended id: {$id}");
            }
            if (! in_array($status, ['UPCOMING', 'DRAFT', 'SCHEDULED', 'PENDING'])) {
                return $this->showErrorOrganizer("Event has already gone live for id: {$id}");
            }

            $eventCategory = EventCategory::all();
            $eventTierList = EventTier::all();
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
            $eventDetail = EventDetail::findEventAndThrowError(
                $id,
                $userId
            );

            if ($eventId) {
                $eventDetail = EventDetail::storeLogic($eventDetail, $request);
                $eventDetail->user_id = $request->get('user')->id;
                $eventDetail->save();

                if ($request->livePreview === 'true') {
                    return redirect('organizer/event/'.$eventDetail->id.'/live');
                }
                if ($request->goToCheckoutPage === 'yes') {
                    return redirect('organizer/event/'.$eventDetail->id.'/checkout');
                }
                return redirect('organizer/event/'.$eventDetail->id.'/success');
            }
            return $this->showErrorOrganizer("Event not found for id: {$id}");
        } catch (TimeGreaterException|EventChangeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong with saving data!');
        }
    }

    public function destroy($id)
    {
        try {
            $event = EventDetail::find($id);
            EventDetail::destroyEventBanner($event->eventBanner);
            $event->delete();

            return redirect('organizer/event');
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer('Failed to delete event!');
        }
    }
}
