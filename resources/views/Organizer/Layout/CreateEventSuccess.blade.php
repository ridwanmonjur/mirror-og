@php

$dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);

extract($dateArray);

@endphp
<div class="text-center" id="step-13">
    <div class="welcome">
        <u>
            <h3 id="heading">All done!</h3>
        </u>
    </div>
    <div class="box-width">
        <p id="notification">Your <u>{{$event->sub_action_private}}</u> event has been scheduled to launch on {{$combinedStr}} at {{$timePart}}!</p>
    </div>
    <button class="js-shareUrl oceans-gaming-default-button" style="padding: 10px 50px; background-color: transparent; color: black; border: 1px solid black;">
        <img src="{{ asset('/assets/images/events/copy-icon.png') }}" class="js-shareUrl" height="20" width="20"> &nbsp;
        Copy event url
    </button>

    <!-- <a style="" href="{{route('event.show', $event->id) }}"> <u> Click to more details for event type: {{$event->sub_action_private}} id: {{$event->id}} </u></a> -->
    <br><br><br><br>
    @if($event->sub_action_private == 'private')
    <button class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: transparent; color: black; border: 1px solid black;">
        <img src="{{ asset('/assets/images/events/copy-icon.png') }}" class="js-shareUrl" height="20" width="20"> &nbsp;
        <p id="notification">View Invite list</p>
    </button>
    @endif
    <button
        onclick="window.location.href='{{route('event.index') }}'"
        class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: black; border: 1px solid black;">
        Go to event page
    </button>
    <br><br>
    <button class="oceans-gaming-default-button" style="padding:10px 60px;">Done</button>
</div>
<script>
    let copyUrl = "{{ route('event.show', $event->id) }}";
    const copyUtil = () => {
        navigator.clipboard.writeText(copyUrl).then(function() {
            console.log('Copying to clipboard was successful!');
        }, function(err) {
            console.error('Could not copy text to clipboard: ', err);
        });
    }
    let shareUrl = document.querySelectorAll('.js-shareUrl');
    console.log({
        copyUrl
    });
    console.log({
        copyUrl
    });
    console.log({
        copyUrl
    });
    console.log({
        copyUrl
    });
    for (let i = 0; i < shareUrl.length; i++) {
        shareUrl[i].addEventListener('click', copyUtil, false);
    }
</script>