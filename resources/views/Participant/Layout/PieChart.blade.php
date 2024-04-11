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
                <div class="text-center input-group w-75 mx-auto">
                    <span class="input-group-text bg-primary text-light" id="inputGroup-sizing-sm">RM </span>
                    <input class="form-control" type="text" value="00.00" oninput="moveCursorToEnd(this); updateInput(this);" onkeydown="moveCursorToEnd(this); keydown(this); ">
                    <span onclick="resetInput(this);" style="background-color: transparent;" class="input-group-text cursor-pointer border-0">
                        <svg 
                        onclick="resetInput(this);"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </span>
                </div>
                <br>
                <p class="text-center"><u>Selected Amount: RM <span class="putAmountClass"> </span> </u></p>
                <br>
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
    registrationPaymnentMap[{{$joinEvent->id}}] = 0;
    function updateInput(input) {
        let index = registrationPaymnentMap[{{$joinEvent->id}}];
        let totalLetters = 4;
        let newValue = input.value.replace(/[^\d]/g, '');
        let lettersToTake = index - totalLetters;
        let isMoreThanTotalLetters = lettersToTake >= 0;
        if (isMoreThanTotalLetters) {
            let length = newValue.length;
            newValue = newValue.substr(0, lettersToTake + 3) + '.' + newValue.substr(lettersToTake + 3, 2);
            console.log("yers")
        } else { 
                newValue = newValue.substr(1, 2) + '.' + newValue.substr(3, 2);
        }
        registrationPaymnentMap[{{$joinEvent->id}}] ++;
        
        input.value = newValue;
    }

    function keydown(input) {
        if (event.key === "Backspace" || event.key === "Delete" ||
            (event.key.length === 1 && !/\d/.test(event.key))) {
            event.preventDefault();
        }
    }

    function moveCursorToEnd(input) {
        input.focus(); 
        input.setSelectionRange(input.value.length, input.value.length);
    }

    function putAmount() {
        // tomorrow 2 hours
        // first fill the span simply by innerText
        // modify piechart label and wheel
        // change color and add color
    }
</script>
