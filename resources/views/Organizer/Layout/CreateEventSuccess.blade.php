<div class="text-center" id="step-13">
    <div class="welcome">
        <u>
            <h3 id="heading">Your event has been successfully created</h3>
        </u>
    </div>
    <div class="box-width">
        <p id="notification">UI To be implemented.....</p>
    </div>
    <a style="" href="{{route('event.show', $event->id) }}"> <u> Click to more details for event type: {{$event->sub_action_private}} id: {{$event->id}}  </u></a>
    <br><br>
    <input type="submit" onclick="goToNextScreen('', '')" value="Done">
</div>