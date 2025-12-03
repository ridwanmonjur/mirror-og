  <div class="text-center" id="step-0">
      <div class="welcome welcome--first" id="invisible-until-loaded">
          <div >
              <br>
              <u>
                  <h2>
                      @if (isset($error))
                          Sorry error has occurred in saving your event!
                      @else
                          Welcome to OW Gaming's Event Creator
                      @endif
                  </h2>
              </u>
              <br><br>
              <p class="create-online-esports">
                  @if (isset($error))
                      <span style="color: #EF4444;"> {{ $error }} </span>
                  @else
                      Create online esports events all on your own, right here on OW Gaming, in just 4 steps.
                  @endif
              </p>
              <br><br>
          </div>
          <input type="button" onclick="goToNextScreen('step-1', 'timeline-1')" value="Continue">
      </div>
  </div>
