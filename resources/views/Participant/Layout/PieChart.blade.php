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
            <div class="modal-body">
                <div class="mx-auto text-center">
                    <div class="pie animate no-round" style="{{$styles}}">{{$percent}}%</div>
                    <p> Total Entry Fee: <u>RM {{$total}} </u></p>
                    <span>Paid: <u class="text-success">RM {{$exisitngSum}}</u> 
                    <span>Pending: <u style="color: red;">RM {{($pedning)}} </u> <span></p>
                </div>
                <div class="text-center"> 
                    <input type="text" value="00.00" oninput="updateInput(this)" onkeydown="keydown(this)" style="direction: ltr;">
                </div>
                <div class="mx-auto text-center">
                    <button class="mt-2 btn oceans-gaming-default-button oceans-gaming-gray-button">Proceed to payment</button>
                    <br>
                    <button  data-bs-dismiss="modal" class="mt-2 btn oceans-gaming-default-button oceans-gaming-transparent-button">Cancel</button>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>
<script>
    function updateInput(input) {
        let newValue = input.value.replace(/[^\d.]/g, '');
        console.log({newValue, letters: newValue.substr(0, 4), zletters: newValue.substr(1, 5)});
        if (newValue.substr(0, 4) == '00.00') {
            newValue = newValue.substr(1, 5);
        } else if (newValue.charAt(0) === '0') {
            
        } 
        
        newValue = parseFloat(newValue).toFixed(2);
        input.value = newValue;
    }

    function keydown(input) {
        if (event.key === "Backspace" || event.key === "Delete" ||
            (event.key.length === 1 && !/\d/.test(event.key))) {
            event.preventDefault();
        }
    }
</script>
