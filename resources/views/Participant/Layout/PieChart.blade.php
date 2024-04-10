@php
    $random_int = rand(0, 999);
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

<div class="ms-3 d-flex flex-column justify-content-between position-relative popover__wrapper">
    <div class="mx-auto text-center popover__title">
        <div class="pie animate no-round" style="{{$styles}}">{{$percent}}%</div>

        <p> Total Entry Fee: <u>RM {{$total}} </u></p>
        <span>Paid: <u class="text-success">RM {{$exisitngSum}}</u> 
        <span>Pending: <u style="color: red;">RM {{($pedning)}} </u> <span></p>
    </div>
     <div class="popover__content">
        <p class="popover__message">Joseph Francis "Joey" Tribbiani, Jr.</p>
        <img alt="Joseph Francis Joey Tribbiani, Jr." src="https://media.giphy.com/media/11SIBu3s72Co8w/giphy.gif">
    </div>
    <div class="mx-auto text-center">
        @if ($pedning != 0)
            <button class="btn oceans-gaming-default-button" data-bs-toggle="modal" data-bs-target="{{'#payModal' . $random_int}}">Contribute </button>
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
   <div class="modal fade" id={{'payModal' . $random_int}} tabindex="-1" aria-labelledby={{'#payModal' . $random_int. 'Label'}} aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary text-light">Save changes</button>
            </div>
            </div>
        </div>
    </div>
</div>
