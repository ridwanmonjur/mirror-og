<?php

namespace App\Http\Controllers\Organizer;

use App\Exceptions\DiscountNotFountException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventDetail;
use App\Models\EventCategory;
use App\Models\Discount;
use App\Models\EventTier;
use App\Models\EventType;
use Illuminate\View\View;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\EventChangeException;
use App\Exceptions\TimeGreaterException;
use Illuminate\Validation\UnauthorizedException;

class OrganizerEventController extends Controller
{
    public function home(): View
    {
        return view('Organizer.Home');
    }

    function combineParams($queryParams)
    {
        $combinedParams = [];

        foreach ($queryParams as $key => $value) {
            if (is_array($value) && count($value) > 1) {
                $combinedParams[$key] = $value;
            } else {
                $combinedParams[$key] = is_array($value) ? $value[0] : [$value];
            }
        }

        return $combinedParams;
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
            ->with('tier', 'type', 'game', 'joinEvents')
            ->withCount('joinEvents')
            ->where('user_id', $user->id)
            ->paginate($count);

        foreach ($eventList as $event) {
            $tierEntryFee = $event->tier->eventTier->tierEntryFee ?? null;
        }
        
        $outputArray = compact('eventList', 'count', 'user', 'organizer', 
            'mappingEventState', 'eventCategoryList', 'eventTierList', 'eventTypeList'
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
            ->where('user_id', $userId)
            ->with('tier', 'type', 'game', 'joinEvents')
            ->paginate($count);
        
        $mappingEventState = EventDetail::mappingEventStateResolve();
        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        $view = view('Organizer.ManageEvent.ManageEventScroll', $outputArray)->render();

        return response()->json(['html' => $view]);
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
        $fileNameInitial = 'eventBanner-' . time() . '.' . $file->getClientOriginalExtension();
        $fileNameFinal = "images/events/$fileNameInitial";
        $file->storeAs('images/events/', $fileNameInitial);
        return $fileNameFinal;
    }

    public function destroyEventBanner($file)
    {
        $fileNameInitial = str_replace('images/events/', '', $file);
        $fileNameFinal = "images/events/$fileNameInitial";
        
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
                $userId, $request->id, null , 'joinEvents'
            ); 
          
            $outputArray = compact( 'event',  'user', 
                'livePreview', 'mappingEventState'
            );
            
            return view('Organizer.ViewEvent', $outputArray);
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event not found with id: $request->id");
        }
    }

    public function showSuccess(Request $request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventAndThrowError(
                $id, $userId
            );
            $isUserSameAsAuth = true;
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event can't be retieved with id: $id");
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
                $userId, $id, null , 'joinEvents'
            );
        
            return view('Organizer.ViewEvent', [
                'event' => $event,
                'mappingEventState' => EventDetail::mappingEventStateResolve(),
                'isUser' => true,
                'livePreview' => 0,
            ]);

        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event not retrieved with id: $id");
        }
    }

    public function store(Request $request)
    {
        try {
            $eventDetail = new EventDetail();
            $eventDetail = EventDetail::storeLogic($eventDetail, $request);
            $eventDetail->user_id = $request->get('user')->id;
            $eventDetail->save();
            
            if ($request->livePreview == 'true') {
                return redirect('organizer/event/' . $eventDetail->id . '/live');
            } elseif ( $request->goToCheckoutPage == 'yes' ) {
                return redirect('organizer/event/' . $eventDetail->id . '/checkout');
            } else {
                return redirect('organizer/event/' . $eventDetail->id . '/success');
            }
        } catch (TimeGreaterException | EventChangeException $e ) {
            return back()->with('error', $e->getMessage());
        }  catch (Exception $e) {
            return back()->with('error', 'Something went wrong with saving data!');
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError(
                $userId, $id, null , null
            );  
            $status = $event->statusResolved();
                
            if ( $status == "ENDED" ) {
                return $this->show404("Event has already ended id: $id");
            } else if ( !in_array($status, ['UPCOMING', 'DRAFT', 'SCHEDULED', 'PENDING' ] ) ) {
                return $this->show404("Event has already gone live for id: $id");
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
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event not found for id: $id");
        }
    }

    public function updateForm($id, Request $request)
    {
        try {
            $eventId = $id;
            $user = $request->get('user');
            $userId = $user->id;
            $eventDetail =  EventDetail::findEventAndThrowError(
                $id, $userId
            );
            
            if ($eventId) {
                $eventDetail = EventDetail::storeLogic($eventDetail, $request);
                $eventDetail->user_id = $request->get('user')->id;
                $eventDetail->save();

                if ($request->livePreview == 'true') {
                    return redirect('organizer/event/' . $eventDetail->id . '/live');
                } else if ( $request->goToCheckoutPage == 'yes' ) {
                    return redirect('organizer/event/' . $eventDetail->id . '/checkout');
                } else {
                    return redirect('organizer/event/' . $eventDetail->id . '/success');
                }
            } else {
                return $this->show404("Event not found for id: $id");
            }
        } catch (TimeGreaterException | EventChangeException $e ) {
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong with saving data!');
        }
    }

    public function destroy($id)
    {
        try{
            $event = EventDetail::find($id);
            EventDetail::destroyEventBanner($event->fileBanner);
            $event->delete();
            return redirect('organizer/event');
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Failed to delete event!");
        }
    }
}
