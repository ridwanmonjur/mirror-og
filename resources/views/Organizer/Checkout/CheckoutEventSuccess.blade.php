@php

    $status = $event->statusResolved();
    $dateArray = bladeGenerateEventStartEndDateStr($event->sub_action_public_date, $event->sub_action_public_time);
    extract($dateArray);

@endphp
<div class="text-center" id="step-13">
    <div class="welcome">
        <u>
            <h3 id="heading">Payment Successful</h3>
        </u>
    </div>
    <div class="box-width my-4">
        You have successfully paid for this event!
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
<script>
    const goToManageScreen = () => {
        window.location.href = "{{ route('event.index') }}";
    }
    let copyUrl = "{{ route('event.show', $event->id) }}";
    const copyUtil = () => {
        navigator.clipboard.writeText(copyUrl).then(function() {
            console.log('Copying to clipboard was successful!');
            Swal.fire({
                toast: true,
                position: 'top-right',
                icon: 'success',
                title: 'Copied!',
                text: 'Event url copied to clipboard',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'colored-toast'
                },
                timerProgressBar: true
            })

        }, function(err) {
            console.error('Could not copy text to clipboard: ', err);
        });
    }
    let shareUrl = document.querySelectorAll('.js-shareUrl');
    for (let i = 0; i < shareUrl.length; i++) {
        shareUrl[i].addEventListener('click', copyUtil, false);
    }
</script>
