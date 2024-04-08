@php
    $total = $joinEvent->tier->tierEntryFee * $joinEvent->tier->tierTeamSlot;
    $exisitngSum = $joinEvent->participant_payments_sum_payment_amount ?? 0;
    $pedning = $total - $exisitngSum;
    $percent = $exisitngSum * 100 / $total; 
    if ($percent == 0) {
        $styles = '--p:' . 100 . '; --c:'. '#f7f7f7';
    } else {
        $styles = '--p:' . $percent . '; --c:'. ($pedning == 0 ? 'orange' : 'purple');
    }
@endphp

<div class="ms-3 d-flex flex-column justify-content-between position-relative">
    <div class="mx-auto text-center">
        <div class="pie animate no-round" style="{{$styles}}">{{$percent}}%</div>

        <p> Total Entry Fee: <u>RM {{$total}} </u></p>
        <span>Paid: <u class="text-success">RM {{$exisitngSum}}</u> 
        <span>Pending: <u style="color: red;">RM {{($pedning)}} </u> <span></p>
    </div>
    <div class="mx-auto text-center">
        @if ($pedning < 0)
            <button class="btn oceans-gaming-default-button">Contribute </button>
        @else
            <button class="btn oceans-gaming-default-button oceans-gaming-gray-button">Contribution Full </button>
        @endif
        <br>
        @if ($pedning < 0 && true)
            <button class="mt-2 btn oceans-gaming-default-button oceans-gaming-green-button">Confirm Registration</button>
        @else
            <button class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button">Confirm Registration</button>
        @endif
    </div>
</div>
