@php
    $random_int = rand(0, 999);
    $total = $joinEvent->tier?->tierEntryFee;
    $exisitngSum = $groupedPaymentsByEvent[$joinEvent->id] ?? 0;
    $individualContributionTotal = 0;
    $pedning = $total - $exisitngSum;
    $percentReal = round(($exisitngSum * 100) / $total, 2);
    $percent = floor($percentReal);
    $myMemberId = null;
    if (!$percent) {
        $styles = '--p:' . 100 . '; --c:' . 'lightgray';
    } else {
        $styles = '--p:' . $percent . ';';
        $color = 'red';
        if ($percent > 50 && $percent < 100 ) {
            $color = 'orange';
        } else if ($percent >= 100 ) {
            $color = '#179317';
        }

        $styles.= '--c:' . $color;
    }
@endphp


<div style="margin-top: -20px;" class="col-12 col-lg-6 d-flex user-select-none flex-column justify-content-between mb-5 position-relative  popover-parent rounded">
    <div class="mx-auto text-center cursor-pointer popover-button">
        <div class="pie animate no-round" style="{{ $styles }}">{{ $percent }}%</div>
        <p> Total Entry Fee: <u>RM {{ $total }} </u></p>
        <span>Paid: <u class="text-success">RM {{ $exisitngSum }}</u>
            <span>Pending: <u style="color: red;">RM {{ $pedning }} </u> <span></p>
    </div>
    <div class="popover-content border-dark border-2 d-none cursor-pointer z-999" style="background: white;">
        <div class="border border-2 border-secondary py-4 px-3" style="background: white; width: min-content;">
            <h5 class="ps-3 text-primary pb-2">Contribution of each member</h5>
            <table class="responsive table table-borderless table-sm align-start px-3 mx-3 popover-display2"    >
                <thead>
                    <tr>
                        <th class="pb-2"></th>
                        <th class="pe-3 pb-2">Participant</th>
                        <th class="pe-3 pb-2">Payment</th>
                        <th class="pe-3 pb-2">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($selectTeam->members as $member2)
                        @php
                            $myMemberId = $myMemberId === null && $member2->user_id === $user->id ?  $member2->id : $myMemberId;
                            $memberContribution = $groupedPaymentsByEventAndTeamMember[$joinEvent->id][$member2->id] ?? 0;
                            $individualContributionTotal += $memberContribution;
                        @endphp
                        <tr style="border-collapse: seperate !important; border-spacing: 0 1em !important;">
                            <td class="ps-3 pe-0 " style="width: 55px; ">
                                <a href="{{route('public.participant.view', ['id' => $member2->user->id])}}" class="text-right">
                                   <svg class="mt-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                    </svg>
                                </a>
                            </td> 
                            <td class="pe-3 " style="width: 200px; ">
                                <img
                                    class="object-fit-cover rounded-circle me-2 border border-primary random-color-circle" 
                                    src="{{'/storage' . '/' . $member2->user->userBanner}}" width="25" height="25"
                                    {!! trustedBladeHandleImageFailureBanner() !!}
                                >
                                <span>{{$member2->user->name}}</span>
                            </td>
                            <td class="pe-3 ">
                                RM {{$memberContribution ?? 0}}
                            </td>
                            <td class="pe-2 ">
                                {{round(($memberContribution?? 0) *100 / $total, 2) ?? 0}}%
                            </td>
                           
                        </tr>
                    @endforeach
                   <tr class="mt-3  border-secondary border-top " style="border-collapse: seperate !important; border-spacing: 0 1em !important;">
                            <td class="pe-3"></td>
                            <td class="pe-3 pb-2">
                               Total paid
                            </td>
                            <td class="pe-3 pb-2">
                                RM {{$exisitngSum}}
                            </td>
                            <td class="pe-2 pb-2">
                               {{ $percentReal }}%
                            </td>
                           
                        </tr>
                </tbody>
            </table>
             @if ($exisitngSum !== $individualContributionTotal) 
                <p class="mx-4">Another member made the remaining RM {{$exisitngSum- $individualContributionTotal}} .</p>
                <p class="mx-4">Existing members paid RM {{$individualContributionTotal}} .</p>
            @endif
        </div>
       
    </div>
    <div class="mx-auto text-center ">
        @if ($joinEvent->isUserPartOfRoster)
            @if ($signupStatusEnum['TOO_EARLY'] == $joinEvent->regStatus )
                <p class="text-center"> 
                    Event start time: {{$joinEvent->eventDetails->getFormattedStartDate()}} 
                </p>
            @else
                @if ($joinEvent->payment_status != "completed")
                    <button class="oceans-gaming-default-button  d-inline-block btn " 
                        data-bs-toggle="modal"
                        data-bs-target="{{ '#payModal' . $random_int }}"
                        data-join-event-id="{{ $joinEvent->id }}"
                    >
                        {{ $signupStatusEnum['EARLY'] == $joinEvent->regStatus ? 'Early Registration' : 'Normal Registration' }} 

                    </button> <br>

                @endif
                @if ($joinEvent->totalRosterCount >= $maxRosterSize 
                    && $joinEvent->payment_status == "completed"
                    && $joinEvent->roster_captain_id
                )
                    @if ($joinEvent->join_status == "pending")
                        <form  class="{{'confirmform' . $random_int}}" action="{{route('participant.confirmOrCancel.action')}}" method="POST">
                            @csrf
                            <input type="hidden" name="join_event_id" value="{{$joinEvent->id}}">
                            <input type="hidden" name="join_status" value="confirmed">
                            <button 
                                data-form="{{'confirmform' . $random_int}}" 
                                type="button" 
                                data-cancel="0"
                                data-join-event-id="{{$joinEvent->id}}"
                                data-join-status="{{$joinEvent->join_status}}"
                                data-registration-status="{{$joinEvent->regStatus}}"
                                onclick="submitConfirmCancelForm(event)" 
                                class="mt-2 btn bg-success py-2 rounded-pill"
                            >
                                Confirm Registration
                            </button>
                        </form>
                    @elseif ($joinEvent->join_status == "confirmed") 
                        @if(!isset($joinEvent->vote_ongoing))
                            <form class="{{'cancelform' . $random_int}}" action="{{route('participant.confirmOrCancel.action')}}" id="cancelRegistration" method="POST">
                                @csrf
                                <input type="hidden" name="join_event_id" value="{{$joinEvent->id}}">
                                <input type="hidden" name="join_status" value="canceled">
                                <button 
                                    data-join-event-id="{{$joinEvent->id}}"
                                    data-form="{{'cancelform' . $random_int}}" 
                                    data-cancel="1"
                                    type="button" 
                                    data-join-status="{{$joinEvent->join_status}}"
                                    data-registration-status="{{$joinEvent->regStatus}}"
                                    onclick="submitConfirmCancelForm(event)" 
                                    class="mt-2 btn py-2 bg-red text-light rounded-pill"
                                >
                                    Cancel Registration
                                </button> 
                                <br>
                                <p class="text-success mt-2">Your registration is confirmed!</p>
                            </form>
                        @else
                           <button
                                style="cursor: not-allowed; pointer-events: none;"
                                class="mt-2 btn oceans-gaming-default-button bg-success  py-2 rounded-pill px-3">
                                Registration Confirmed
                            </button>
                        </form>
                            <p class="text-red mt-2 py-0 mb-0">A vote to quit is in progress!</p>
                        @endif

                    @elseif ($joinEvent->join_status == "canceled")
                        <button
                                style="cursor: not-allowed; pointer-events: none;"
                                class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button px-2">
                                Registration Canceled
                            </button>
                        </form>
                    @endif
                @endif
                @if ($joinEvent->totalRosterCount == $maxRosterSize)
                    <div class="mt-2 text-start text-success">
                        <span class="me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                            </svg>
                        </span>
                        <span class="me-1">Roster is filled.</span>
                       
                    </div>
                @else
                    <div class="mt-2 text-start text-red  z-99 cursor-pointer">
                        <span class="me-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>       
                        </span>
                        <span class="me-0">Roster is still empty.</span>
                        
                    </div>
                @endif
            
                @if ($joinEvent->payment_status == "completed")
                    <div class="mt-0 text-start text-success">
                        <span class="me-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                            </svg>
                        </span>
                        <span class="me-1">Entry fee is fully paid.</span>
                    </div>
                @else
                    <div class="mt-0 text-start text-red">
                        <span class="me-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>       
                        </span>
                        <span>Entry fee is not fully paid!</span>
                    </div>
                @endif
                @if ($joinEvent->roster_captain_id)
                    <div class="mt-0 text-start text-success">
                        <span class="me-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                            </svg>
                        </span>
                        <span>Roster captain is appointed.</span>
                    </div>
                @else
                    <div class="mt-0 text-start text-red">
                        <span class="me-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>       
                        </span>
                        <span class="me-1">Roster captain is not appointed.</span>
                        
                    </div>
                @endif
            @endif
        @else
            <p class="text-center text-primary"> 
                Join the roster to contribute to the entry fee
            </p>
        @endif
    </div>
    <div class="modal fade" id={{ 'payModal' . $random_int }} tabindex="-1"
        aria-labelledby={{ '#payModal' . $random_int . 'Label' }} aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST"
                onsubmit="validateAmount(event)"
                action="{{ route('participant.checkout.action') }}"
            >
                @csrf
                @if ($myMemberId === null) 
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="mx-auto text-center">
                                <p> Error occurred! </p> 
                            </div>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="teamId" value="{{$selectTeam->id}}">
                    <input type="hidden" name="teamName" value="{{$selectTeam->teamName}}">
                    <input type="hidden" name="joinEventId" value="{{$joinEvent->id}}">
                    <input type="hidden" name="id" value="{{$joinEvent->event_details_id}}">
                    <input type="hidden" name="memberId" value="{{$myMemberId}}">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div>
                                <h5 class="my-2"> Contribute to the entry fee </h5>
                                <div class="py-2 px-2 border border-2 border-success">
                                    <div>
                                        <img {!! trustedBladeHandleImageFailureBanner() !!}
                                            src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game?->gameIcon : null) }}"
                                            class="object-fit-cover rounded-2 me-1" width="30" height="30"
                                        >
                                        <p class=" d-inline my-0 ms-2"> {{ $joinEvent->eventDetails->eventName }} </p>
                                    </div>
                                    <div class="d-flex pt-2 justify-content-start">
                                        <img {!! trustedBladeHandleImageFailureBanner() !!} 
                                            src="{{ bladeImageNull($joinEvent->eventDetails->user->userBanner) }}" width="30"
                                            height="30" class="me-1 object-fit-cover random-color-circle"
                                        >
                                        <div class="ms-2">
                                            <small class="d-block py-0 my-0">
                                                {{ $joinEvent->eventDetails->user->name }}
                                            </small>
                                            <small
                                                data-count="{{ array_key_exists($joinEvent->eventDetails->user_id, $followCounts) ? $followCounts[$joinEvent->eventDetails->user_id] : 0 }} "
                                                class="p-0 my-0 {{ 'followCounts' . $joinEvent->eventDetails?->user_id }}">
                                                {{ $followCounts[$joinEvent->eventDetails->user_id] }}
                                                follower{{ bladePluralPrefix($followCounts[$joinEvent->eventDetails->user_id]) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mx-auto mt-3 mb-2">
                                <div class="row mx-2">
                                    <div class="col-12 col-lg-5 px-0 mx-0">
                                        <div class="pie text-center animate no-round me-2" style="{{ $styles }}">{{ $percent }}%</div>
                                    </div>
                                    <div class="text-start col-12 col-lg-7 px-0 mx-0">
                                        <p class="mt-2 mb-1 py-0"> Total Entry Fee: <u>RM {{ $total }} </u></p>
                                        <small class="my-0 py-0">Paid: <u class="text-success">RM {{ $exisitngSum }}</u> </small>
                                        <br>
                                        <small class="my-0 py-0">Pending: <u style="color: red;">RM {{ $pedning }} </u></small>
                                        <div class="input-group mt-3">
                                            <span class="input-group-text bg-primary text-light" id="inputGroup-sizing-sm">RM </span>
                                            <input data-joinEventId="{{ $joinEvent->id }}" 
                                                data-pending-amount="{{ $pedning }}"
                                                data-total-amount="{{ $total }}"
                                                data-existing-amount="{{ $exisitngSum }}" data-modal-id="{{ $random_int }}"
                                                name="amount" class="form-control" type="text" default="00.00" value="00.00"
                                                oninput="moveCursorToEnd(this); addPrice(this);"
                                                onkeydown="moveCursorToEnd(this); keydownPrice(event, this); "
                                            >
                                            <span data-joinEventId="{{ $joinEvent->id }}" 
                                                dota-pending-amount="{{ $pedning }}"
                                                data-total-amount="{{ $total }}"
                                                id="currencyResetInput"
                                                style="width: 10px; visibility: hidden;"
                                                data-existing-amount="{{ $exisitngSum }}" data-modal-id="{{ $random_int }}"
                                                onclick="resetInput(this);" style="background-color: transparent;"
                                                class="input-group-text cursor-pointer border-0 button-close">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                </svg>
                                            </span>
                                    </div>
                                    <p class="mt-4 mb-3 py-0"><u>Selected Amount: RM <span class="putAmountClass">00.00 </span> </u></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mx-auto text-center">
                                <button 
                                    type="submit"
                                    id="paymentProceedButton"
                                    data-joinEventId="{{ $joinEvent->id }}" 
                                    class="mt-2 btn rounded-pill text-white py-2 px-3 btn-secondary">Proceed to payment
                                    </button>
                                <br>
                                <button type="button" data-bs-dismiss="modal"
                                    data-joinEventId="{{ $joinEvent->id }}" 
                                    onclick="triggerResetClick(event)"
                                    class="mt-2 btn oceans-gaming-default-button oceans-gaming-transparent-button">Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
            <br>
        </div>
    </div>
</div>

