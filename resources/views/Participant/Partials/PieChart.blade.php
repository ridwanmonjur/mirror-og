@php
    $random_int = rand(0, 999);
    $total = $joinEvent->tier->tierEntryFee * $joinEvent->tier->tierTeamSlot;
    $exisitngSum = $joinEvent->participant_payments_sum_payment_amount ?? 0;
    $pedning = $total - $exisitngSum;
    $percent = ($exisitngSum * 100) / $total;
    $styles = '--p:' . $percent . '; --c:' . ($isInvited ? 'purple' : 'orange');
@endphp

<div class="ms-3 d-flex flex-column justify-content-between position-relative popover__wrapper">
    <div class="mx-auto text-center popover__title">
        <div class="pie animate no-round" style="{{ $styles }}">{{ $percent }}%</div>
        <p> Total Entry Fee: <u>RM {{ $total }} </u></p>
        <span>Paid: <u class="text-success">RM {{ $exisitngSum }}</u>
            <span>Pending: <u style="color: red;">RM {{ $pedning }} </u> <span></p>
    </div>
    <div class="popover__content">
        <p class="popover__message">
            @foreach($selectTeam->members as $member)
                <img
                    class="object-cover rounded-circle me-2 border border-primary" 
                    src="{{'/storage' . '/' . $member->user->userBanner}}" width="45" height="45">
                <span class="me-2"> {{$member->user->name}} </span>
                <span class="me-2"> RM {{$paymentsByMemberId[128] ?? 0}} </span>
            @endforeach
        </p>
       
    </div>
    <div class="mx-auto text-center">
        @if ($pedning != 0)
            <button class="btn oceans-gaming-default-button" data-bs-toggle="modal"
                data-bs-target="{{ '#payModal' . $random_int }}">Contribute </button>
        @else
            <button class="btn oceans-gaming-default-button oceans-gaming-gray-button">Contribution Full </button>
        @endif
        <br>
        @if ($pedning < 0 && true)
            <button class="mt-2 btn btn-success py-2 rounded-pill">Confirm
                Registration</button>
        @else
            <button class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button">Confirm
                Registration</button>
        @endif
    </div>
    <div class="modal fade" id={{ 'payModal' . $random_int }} tabindex="-1"
        aria-labelledby={{ '#payModal' . $random_int . 'Label' }} aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST"
                action="{{ route('participant.checkout.action') }}"
            >
                @csrf
                <input type="hidden" name="teamId" value="{{$selectTeam->id}}">
                <input type="hidden" name="teamName" value="{{$selectTeam->teamName}}">
                <input type="hidden" name="joinEventId" value="{{$joinEvent->id}}">
                <input type="hidden" name="id" value="{{$joinEvent->event_details_id}}">
                <input type="hidden" name="memberId" value="{{$member->id}}">
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
                            <input data-joinEventId="{{ $joinEvent->id }}" data-total-amount="{{ $total }}"
                                data-existing-amount="{{ $exisitngSum }}" data-modal-id="{{ $random_int }}"
                                name="amount" class="form-control" type="text" default="00.00" value="00.00"
                                oninput="moveCursorToEnd(this); updateInput(this);"
                                onkeydown="moveCursorToEnd(this); keydown(this); ">
                            <span data-joinEventId="{{ $joinEvent->id }}" data-total-amount="{{ $total }}"
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
            </form>
            <br>
        </div>
    </div>
</div>
