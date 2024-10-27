@php
    $random_int = rand(0, 999);
    $total = $joinEvent->tier?->tierEntryFee;
    $exisitngSum = $groupedPaymentsByEvent[$joinEvent->id] ?? 0;
    $individualContributionTotal = 0;
    $pedning = $total - $exisitngSum;
    $percentReal = round(($exisitngSum * 100) / $total, 2);
    $percent = floor($percentReal);
    $myMemberId = null;

    $styles = '--p:' . $percent . '; --c:' . ($isInvited ? 'purple' : 'orange');
@endphp


<div class="ms-3 d-flex flex-column justify-content-between position-relative popover__wrapper rounded">
    <div class="mx-auto text-center popover__title">
        <div class="pie animate no-round" style="{{ $styles }}">{{ $percent }}%</div>
        <p> Total Entry Fee: <u>RM {{ $total }} </u></p>
        <span>Paid: <u class="text-success">RM {{ $exisitngSum }}</u>
            <span>Pending: <u style="color: red;">RM {{ $pedning }} </u> <span></p>
    </div>
    <div @class(["popover__content" => !$isRedirect, "d-none" => $isRedirect, "pt-0"])>
        <p class="popover__message  pt-0">
            <table class="align-start px-3 mx-3 ">
                <thead>
                    <tr>
                        <th class="pe-3 pb-2">Participant</th>
                        <th class="pe-3 pb-2">Payment</th>
                        <th class="pe-3 pb-2">%</th>
                        <th class="pb-2">View</th>
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
                            <td class="pe-3 pb-2">
                                <img
                                    class="object-fit-cover rounded-circle me-2 border border-primary random-color-circle" 
                                    src="{{'/storage' . '/' . $member2->user->userBanner}}" width="35" height="35"
                                    {!! trustedBladeHandleImageFailureBanner() !!}
                                >
                                {{$member2->user->name}}
                            </td>
                            <td class="pe-3 pb-2">
                                RM {{$memberContribution ?? 0}}
                            </td>
                            <td class="pe-2 pb-2">
                                {{round(($memberContribution?? 0) *100 / $total, 2) ?? 0}}%
                            </td>
                            <td class="ps-3 pe-2 pb-2">
                                <a href="{{route('public.participant.view', ['id' => $member2->user->id])}}" class="text-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                   <tr style="border-collapse: seperate !important; border-spacing: 0 1em !important;">
                            <td class="pe-3 pb-2">
                               Total paid
                            </td>
                            <td class="pe-3 pb-2">
                                RM {{$exisitngSum}}
                            </td>
                            <td class="pe-2 pb-2">
                               {{ $percentReal }}%
                            </td>
                            <td class="ps-3 pe-2 pb-2">
                                
                            </td>
                        </tr>
                </tbody>
            </table>
             @if ($exisitngSum !== $individualContributionTotal) 
                <p class="mx-4">Another member made the remaining RM {{$exisitngSum- $individualContributionTotal}} .</p>
                <p class="mx-4">Existing members paid RM {{$individualContributionTotal}} .</p>
            @endif
        </p>
       
    </div>
    <div class="mx-auto text-center">
        @if ($joinEvent->payment_status != "completed")
            <button class="btn oceans-gaming-default-button" data-bs-toggle="modal"
                data-bs-target="{{ '#payModal' . $random_int }}">Contribute </button>
        @else
            <button style="pointer-events: none;" class="btn oceans-gaming-default-button oceans-gaming-gray-button px-3">Contribution Full </button>
        @endif
        <br>
        @if ($joinEvent->payment_status == "completed" && $joinEvent->join_status == "pending")
            <form  class="{{'form' . $random_int}}" action="{{route('participant.confirmOrCancel.action')}}" id="confirmRegistration" method="POST">
                @csrf
                <input type="hidden" name="join_event_id" value="{{$joinEvent->id}}">
                <input type="hidden" name="join_status" value="confirmed">
                <button data-form="{{'form' . $random_int}}" type="button" 
                    onclick="submitConfirmCancelForm(event, 'Confirm registering this event?', 'confirmRegistration')" 
                    class="mt-2 btn bg-success py-2 rounded-pill">
                    Confirm Registration
                </button>
            </form>
        @elseif ($joinEvent->payment_status == "completed" && $joinEvent->join_status == "confirmed")
            <form class="{{'form' . $random_int}}" action="{{route('participant.confirmOrCancel.action')}}" id="cancelRegistration" method="POST">
                @csrf
                <input type="hidden" name="join_event_id" value="{{$joinEvent->id}}">
                <input type="hidden" name="join_status" value="canceled">
                <button data-form="{{'form' . $random_int}}" type="button" style="background-color: red;" onclick="submitConfirmCancelForm(event, 'Confirm canceling this event?', 'cancelRegistration' )" class="mt-2 btn py-2 text-light rounded-pill">
                    Cancel Registration
                </button> 
                <br>
                <p class="text-success mt-2">Your registration is confirmed!</p>
            </form>
        @elseif ($joinEvent->payment_status == "completed" && $joinEvent->join_status == "canceled")
               <button
                    style="cursor: not-allowed; pointer-events: none;"
                    class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button px-2">
                    Registration Canceled
                </button>
            </form>
        @else
            <button class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button px-2">Confirm
                Registration
            </button>
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
                            <div class="mx-auto text-center">
                                <div class="pie animate no-round" style="{{ $styles }}">{{ $percent }}%</div>
                                <p> Total Entry Fee: <u>RM {{ $total }} </u></p>
                                <span>Paid: <u class="text-success">RM {{ $exisitngSum }}</u>
                                    <span>Pending: <u style="color: red;">RM {{ $pedning }} </u> <span></p>
                            </div>
                            <div class="text-center input-group w-75 mx-auto">
                                <span class="input-group-text bg-primary text-light" id="inputGroup-sizing-sm">RM </span>
                                <input data-joinEventId="{{ $joinEvent->id }}" 
                                    data-pending-amount="{{ $pedning }}"
                                    data-total-amount="{{ $total }}"
                                    data-existing-amount="{{ $exisitngSum }}" data-modal-id="{{ $random_int }}"
                                    name="amount" class="form-control" type="text" default="00.00" value="00.00"
                                    oninput="moveCursorToEnd(this); updateInput(this);"
                                    onkeydown="moveCursorToEnd(this); keydown(this); ">
                                <span data-joinEventId="{{ $joinEvent->id }}" 
                                    dota-pending-amount="{{ $pedning }}"
                                    data-total-amount="{{ $total }}"
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
                            <br>
                            <p class="text-center"><u>Selected Amount: RM <span class="putAmountClass">00.00 </span> </u>
                            </p>
                            <br>
                            <div class="mx-auto text-center">
                                <button 
                                    type="submit"
                                    
                                    class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button">Proceed to
                                    payment</button>
                                <br>
                                <button type="button" data-bs-dismiss="modal"
                                    class="mt-2 btn oceans-gaming-default-button oceans-gaming-transparent-button">Cancel</button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
            <br>
        </div>
    </div>
</div>

<script src="{{ asset('/assets/js/participant/PieChart.js') }}"></script>
