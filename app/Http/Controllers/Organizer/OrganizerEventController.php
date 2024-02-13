<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventDetail;
use App\Models\EventCategory;
use App\Models\Discount;
use App\Models\EventTier;
use App\Models\EventType;
use Illuminate\View\View;
use App\Models\Organizer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\EventChangeException;
use App\Exceptions\TimeGreaterException;
use Illuminate\Validation\UnauthorizedException;

class OrganizerEventController extends Controller
{
    private function getEvent($userId, $id): EventDetail
    {
        $event = EventDetail::with('type','tier','game')
            ->find($id);

        if ($event->user_id != $userId) {
            throw new UnauthorizedException('You cannot view an event of another organizer!');
        }
        
        if (!$event) {
            throw new ModelNotFoundException("Event not found with id: $id");
        }

        return $event;
    }

    private function createNoDiscountFeeObject($fee, $entryFee) { 
        $fee['discountFee'] = 0;
        $fee['entryFee'] = $entryFee * 1000;
        $fee['totalFee'] = $fee['finalFee'] = $fee['entryFee'] + $fee['entryFee'] * 0.2;
        return $fee;
    }

    public function show404($error): View
    {
        return view('Organizer.EventNotFound', compact('error'));
    }

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

    public function showLive($request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = $this->getEvent($userId, $id);
            $isUserSameAsAuth = true;            
            $count = 8;
            $eventListQuery = EventDetail::query();
            $eventListQuery->withCount('joinEvents');
            $eventList = $eventListQuery->where('user_id', $user->id)->paginate($count);
            $mappingEventState = EventDetail::mappingEventStateResolve();
            
            foreach ($eventList as $eventItem) {
                $tierEntryFee = $eventItem->tier?->tierEntryFee ?? null;
            }
            
            $livePreview = true;

            $outputArray = compact('eventList', 'event', 'count', 'user', 
                'livePreview', 'mappingEventState'
            );
            
            return view('Organizer.ViewEvent', $outputArray);
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event not found with id: $id");
        }
    }

    public function showSuccess(Request $request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = EventDetail::with('type','tier','game')
                ->where('user_id', $userId)
                ->find($id);
    
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

    public function showCheckout(Request $request, $id): View
    {
        session()->forget(['successMessageCoupon',  'errorMessageCoupon']);

        try {
            $user = $request->get('user');
            $userId = $user->id;
            $event = $this->getEvent($userId, $id);     
            $isUserSameAsAuth = true;

            if (!is_null($event->payment_transaction_id)) {
                return $this->show404("Event with id: $id has already been checked out");
            } else if (is_null($event->tier)) {
                return $this->show404("Event with id: $id has no event tier chosen");
            }

            if ($request->has('coupon')) {
                $discount = Discount::whereRaw('coupon = ?', [$request->coupon])
                    ->first();
            } else {
                $discount = null;
            }

            $fee = [];

            if (!is_null($discount)) {
                $currentDateTime = Carbon::now()->utc();
                $startTime = generateCarbonDateTime($discount->startDate, $discount->startTime);
                $endTime = generateCarbonDateTime($discount->endDate, $discount->endTime);
                $fee['discountId'] = $discount->id;
                $fee['discountName'] = $discount->name;
                $fee['discountType'] = $discount->type;
                $fee['discountAmount'] = $discount->amount;
                
                if ($startTime < $currentDateTime && $endTime > $currentDateTime && $discount->isEnforced) {
                    $fee['entryFee'] = $event->tier->tierEntryFee * 1000 ;
                    $fee['totalFee'] = $fee['entryFee'] + $fee['entryFee'] * 0.2;
                    $fee['discountFee'] = $discount->type == "percent" ? 
                        ( $discount->amount/ 100 ) * $fee['totalFee'] : $discount->amount;
                    $fee['finalFee'] = $fee['totalFee'] - $fee['discountFee'];
                    session()->flash('successMessageCoupon', "Applying your coupon named: $request->coupon!");
                } else {
                    $fee = $this->createNoDiscountFeeObject($fee, $event->tier->tierEntryFee);
                    session()->flash('errorMessageCoupon', "Your coupon named: $request->coupon! is expired or not available now!");
                }
            } else {
                if ($request->has('coupon')) {
                    session()->flash('errorMessageCoupon', "Sorry, your coupon named $request->coupon can't be found!");
                }

                $fee['discountId'] = $fee['discountName'] = $fee['discountType'] 
                    = $fee['discountAmount'] = null;

                $fee = $this->createNoDiscountFeeObject($fee, $event->tier->tierEntryFee);
            }

            return view('Organizer.CheckoutEvent', [
                'event' => $event,
                'mappingEventState' => EventDetail::mappingEventStateResolve(),
                'isUser' => $isUserSameAsAuth,
                'livePreview' => 1,
                'fee' => $fee
            ]);
        }  catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event not found with id: $request->id");
        }     
    }

    public function show(Request $request, $id): View
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            
            $event = EventDetail::with('tier')
                ->where('user_id', $userId)
                ->withCount('joinEvents')
                ->find($id);
            
            if (is_null($event)) {
                throw new ModelNotFoundException("Event not found with id: $id");
            } else if ($event->user_id != $userId) {
                throw new UnauthorizedException('You cannot view an event of another organizer!');
            }

        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Event not found with id: $id");
        }

        return view('Organizer.ViewEvent', [
            'event' => $event,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'isUser' => true,
            'livePreview' => 0,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $fileNameFinal = null;
            
            if ($request->hasFile('eventBanner')) {
                $fileNameFinal = $this->storeEventBanner($request->file('eventBanner'));
            }
            
            $eventDetail = new EventDetail();
            $eventDetail = EventDetail::storeLogic($eventDetail, $request);
            $eventDetail->user_id = $request->get('user')->id;
            $eventDetail->eventBanner = $fileNameFinal;
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
            $event = $this->getEvent($userId, $id);     
            $status = $event->statusResolved();
                
            if ( $status == "ENDED" ) {
                return $this->show404("Event has already ended id: $id");
            }
            
            if ( !in_array($status, ['UPCOMING', 'DRAFT', 'SCHEDULED', 'PENDING' ] ) ) {
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
            $eventDetail = EventDetail::where('user_id', $userId)
                ->find($id);
            
            if ($eventId) {
                $fileNameFinal = null;
                
                if ($request->hasFile('eventBanner')) {
                    $fileNameFinal = $this->storeEventBanner($request->file('eventBanner'));
                
                    if ($eventDetail->eventBanner) {
                        $this->destroyEventBanner($eventDetail->eventBanner);
                    }
                } else {
                    $fileNameFinal = $eventDetail->eventBanner;
                }

                $eventDetail = EventDetail::storeLogic($eventDetail, $request);
                $eventDetail->user_id = $request->get('user')->id;
                $eventDetail->eventBanner = $fileNameFinal;
                $eventDetail->save();

                if ($request->livePreview == 'true') {
                    return redirect('organizer/event/' . $eventDetail->id . '/live');
                } elseif ( $request->goToCheckoutPage == 'yes' ) {
                    return redirect('organizer/event/' . $eventDetail->id . '/checkout');
                }

                return redirect('organizer/event/' . $eventDetail->id . '/success');
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
            $event->delete();
            return redirect('organizer/event');
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404($e->getMessage());
        } catch (Exception $e) {
            return $this->show404("Failed to delete event!");
        }
    }
}
