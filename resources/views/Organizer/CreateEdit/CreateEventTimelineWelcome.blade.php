  <div class="text-center" id="step-0">
      <div id="loader-until-loaded" class="mt-5">
          <img src="{{ asset('/assets/images/animation/Spin.gif') }}">
      </div>
      <div class="welcome mt-5 d-none" id="invisible-until-loaded">
          <div >
              <u>
                  <h2>
                      @if (isset($error))
                          Sorry error has occurred in saving your event!
                      @else
                          Welcome to Splash's Event Creator
                      @endif
                  </h2>
              </u>
              <br><br><br>
              <p class="create-online-esports">
                  @if (isset($error))
                      <span style="color: #EF4444;"> {{ $error }} </span>
                  @else
                      Create online esports events all on your own, right here on Splash, in just 4 steps.
                  @endif
              </p>
              <br><br><br>
          </div>
          <input type="button" onclick="goToNextScreen('step-1', 'timeline-1')" value="Continue">
      </div>
  </div>
