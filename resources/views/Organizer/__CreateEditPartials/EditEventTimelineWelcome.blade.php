  <div class="text-center" id="step-0">
      <div class="welcome mt-5" id="invisible-until-loaded">
          <div >
              <u>
                  <h2>
                      @if (isset($error))
                          Sorry error has occurred in saving your event!
                      @else
                          Edit your created event
                      @endif
                  </h2>
              </u>
              <br><br><br>
              <p class="create-online-esports">
                  @if (isset($error))
                      <span style="color: #EF4444;"> {{ $error }} </span>
                  @else
                      Edit the event you just created.
                  @endif
              </p>
              <br><br><br>
          </div>
          <input type="button" onclick="goToNextScreen('step-1', 'timeline-1')" value="Continue" class="me-3">
          <input type="button" class="bg-secondary" onclick="cancelEvent();" value="Cancel your event">

      </div>
  </div>

<script>
    function cancelEvent() {
       Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#43A4D7",
            cancelButtonColor: "#d33",
            cancelButtonText: "Yes, cancel this event!",
            confirmButtonText: "Oops, no..."
        })
        .then((result) => {
            if (result.isCanceled) {
                document.getElementById('cancelEvent')?.submit();
            }
        });

        }
</script>