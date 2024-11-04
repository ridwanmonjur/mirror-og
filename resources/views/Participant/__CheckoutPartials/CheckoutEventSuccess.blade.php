@php

    $status = $event->statusResolved();
    $dateArray = bladeGenerateEventStartEndDateStr($event->sub_action_public_date, $event->sub_action_public_time);
    extract($dateArray);

@endphp
<div class="text-center" id="step-13">
    <div id="eventData" 
        data-manage-url="{{ route('event.index') }}"
        data-copy-url="{{ route('event.show', $event->id) }}"
        class="d-none"
    >
    </div>
    <div class="welcome">
        <u>
            <h3 id="heading">Payment Successful</h3>
        </u>
    </div>
    <div class="box-width my-4">
        You have successfully paid for this event!
    </div>
    <div class="text-success box-width"> 
        @if ($status == 'ERROR')
            <p id="notification">Your <u>{{ strtolower($status) }}</u> event has no proper start date/ end date!</p>
        @elseif ($status == 'DRAFT')
            <p id="notification">Your <u>{{ strtolower($status) }}</u> event has been checked out!</p>
        @elseif ($status == 'SCHEDULED')
            <p id="notification">Your <u>{{ $event->sub_action_private }}</u> event has been scheduled to launch on
                {{ $combinedStr }} at {{ $timePart }}!</p>
        @elseif ($status == 'UPCOMING' || $status == 'ONGOING')
            <p id="notification">Your <u>{{ $event->sub_action_private }}</u> event is already live!</p>
        @elseif ($status == 'ENDED')
            <p id="notification">Your <u>{{ $event->sub_action_private }}</u> event has ended</p>
        @elseif ($status == 'PENDING')
            <p id="notification"> Your {{ $event->sub_action_private ?? 'public / private' }} event's payment status is pending 
                or some details are missing!
            </p>
        @endif
    </div>
    <button class="mt-5 js-shareUrl oceans-gaming-default-button"
        style="padding: 10px 50px; background-color: transparent; color: black; border: 1px solid black;">
        <img src="{{ asset('/assets/images/events/copy-icon.png') }}" class="js-shareUrl" height="20" width="20">
        &nbsp;
        Copy event url
    </button>
    <br><br>
    <!-- <a style="" href="{{ route('event.show', $event->id) }}"> <u> Click to more details for event type: {{ $event->sub_action_private }} id: {{ $event->id }} </u></a> -->
    
    <button onclick="goToManageScreen();" class="oceans-gaming-default-button"
        style="padding: 10px 50px; background-color: white; color: black; border: 1px solid black;">
        Go to event page
    </button>
    <br><br>
    <a href="{{ route('event.show', $event->id) }}">
        <button class="oceans-gaming-default-button" style="padding:10px 100px;">Done</button>
    </a>
</div>
<script src="{{ asset('/assets/js/shared/CheckoutEventSuccess.js') }}"></script>

   
