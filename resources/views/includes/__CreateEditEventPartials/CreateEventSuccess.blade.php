@php

    $status = $event->statusResolved();
    $dateArray = bladeGenerateEventStartEndDateStr($event->sub_action_public_date, $event->sub_action_public_time);
    extract($dateArray);

@endphp
<div class="text-center" id="step-13">
    <div id="eventData" 
        data-manage-url="{{ route('event.index') }}" 
        data-id="{{$event->id}}"
        data-copy-url="{{ route('event.show', $event->id) }}"
        class="d-none">
    </div>
    
    @if (in_array($status, ['DRAFT', 'PENDING', 'PREVIEW']))
        <br><br>
        <h3> Draft Saved! </h3>
        <br><br>
        
        <h5 class="my-0 py-0"> Your event has been saved as a draft. </h5>
        @if ($status == 'PENDING')
            <p id="notification"> Your <span>{{ $event->sub_action_private ?? 'public / private' }}</span> event's payment
                status is pending
                or some details are missing!
            </p>
        @elseif ($status == 'DRAFT')
            <small> You can edit it any time and launch when you're ready.</small>
        @endif
        <br><br><br><br>
        <a href="{{ route('event.show', $event->id) }}">
            <button class="oceans-gaming-default-button" style="padding:10px 100px;">Done</button>
        </a>
    @else
        <div class="welcome mt-4">
                <h3 id="heading">All done!</h3>
        </div>
        <div class="box-width">
            @if ($status == 'ERROR')
                <h5 id="notification">Your <span class="text-primary">{{ strtolower($status) }}</span> event has no proper start date/ end date!</h5>
            @elseif ($status == 'SCHEDULED')
                <h5 id="notification">Your <span class="text-primary">{{ $event->sub_action_private }}</span> event has been scheduled to launch on
                    {{ $combinedStr }} at {{ $timePart }}!
                </h5>
            @elseif ($status == 'UPCOMING' || $status == 'ONGOING')
                <h5 id="notification">Your <span class="text-primary">{{ $event->sub_action_private }}</span> event is already live!</h5>
            @elseif ($status == 'ENDED')
                <h5 id="notification">Your <span class="text-primary">{{ $event->sub_action_private }}</span> event has ended.</h5>

            @endif
        </div>
        <br>
        <button class="btn btn-sm js-shareUrl btn-link btn-small text-primary border border-2 rounded-pill border-primary py-2 px-3"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#43a4d7" class="bi bi-copy" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
            </svg>
            &nbsp;
            <span class="text-primary">Copy Event Link</span>
        </button>
        <br><br>
        @if ($event->sub_action_private == 'private')
            <a href="{{ route('event.invitation.index', $event->id) }}">
                <button class="oceans-gaming-default-button"
                    style="padding: 10px 50px; background-color: transparent; color: #2e4b59; border: 1px solid black;">
                    <img src="{{ asset('/assets/images/events/user.png') }}" height="20" width="20"> &nbsp;
                    View Invite list
                </button>
            </a>
           
        @endif
        <div class="my-3">
            <input type="checkbox" class="form-check-input me-2" 
                id="notifyCheckbox" {{ $event->willNotify ? 'checked' : '' }}
            >
             <label> Notify me as players join my event. <span>
        </div>
        <button onclick="goToManageScreen();" class="oceans-gaming-default-button"
            style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black;">
            Go to Event Page
        </button>
        <br><br>
        <a href="{{ route('event.show', $event->id) }}">
            <button class="oceans-gaming-default-button" style="padding:10px 100px;">Done</button>
        </a>
    @endif
</div>
<script src="{{ asset('/assets/js/shared/CheckoutEventSuccess.js') }}"></script>
