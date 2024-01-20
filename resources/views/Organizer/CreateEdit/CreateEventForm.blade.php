@php
    $isEventNotNull = isset($event);
    $status = $isEventNotNull ? $event->statusResolved() : null;
    $dateStartArray = null;
    if ($isEventNotNull) {
        $dateStartArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
        extract($dateStartArray);
    }
@endphp

<div id="step-2" class="d-none">

    <div class="welcome text-center">
        <u>
            <h3>
                STEP 1: Choose your Event Categories
            </h3>
        </u>
        <p>Then, select what kind of event you want to run.</p>
        <div class="box-width">
            <div class="grid-2-columns box-width" style="margin-top: -20px !important;">
                @foreach ($eventTypeList as $gameCategory)
                    @if ($gameCategory->eventDefinitions)
                        <div onclick="setFormValues( {'eventType': {{ Js::from($gameCategory->eventType) }} } );
                            let eventTypeId = {{ Js::from($gameCategory->id) }} ;
                            setFormValues( {'eventTypeId': eventTypeId} );
                            goToNextScreen('step-3', 'timeline-1');
                            document.querySelectorAll('.box_2nd').forEach((el) => {
                                el.classList.remove('color-border-success');
                            });
                            this.querySelector('.box_2nd').classList.add('color-border-success');
                            let eventTypeTitle = this.querySelector('.inputEventTypeTitle u').innerHTML;
                            let eventTypeDefinition = this.querySelector('.inputEventTypeDefinition').innerHTML;
                            localStorage.setItem('eventTypeTitle', eventTypeTitle);
                            localStorage.setItem('eventTypeTitle', eventTypeTitle);
                            localStorage.setItem('eventTypeDefinition', eventTypeDefinition);
                            localStorage.setItem('eventTypeId', eventTypeId);
                            "
                            @class(['container-border'])>
                            <a href="#" @class([
                                'box_2nd selectable-box',
                                'color-border-success' =>
                                    $event && $gameCategory->id == $event->event_type_id,
                            ])>
                                <h2 class="{{ 'inputEventTypeTitle box-title' }}">
                                    <u>{{ $gameCategory->eventType }}</u>
                                </h2>
                                <span class="inputEventTypeDefinition" class="box-text"
                                    style="text-align: left;">{{ $gameCategory->eventDefinitions }}</span>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-1', 'none')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-3', 'timeline-1')" type="button"
                class="oceans-gaming-default-button"> Next > </button>
        </div>
    </div>
</div>



<div id="step-3" class="d-none">
    <div class="welcome text-center">
        <u>
            <h3>
                STEP 1: Choose your Event Categories
            </h3>
        </u>
        <br>
        <p>Finally, choose a tier for your event.</p>
        <div class="grid-3-columns box-width">
            @foreach ($eventTierList as $tierCategory)
                <section
                    onclick="setFormValues( {'eventTier': {{ Js::from($tierCategory->eventTier) }} } );
                        let eventTierId = {{ Js::from($tierCategory->id) }} ;
                        let eventTierImg = this.querySelector('.inputEventTierImg').src;
                        let eventTierPerson = this.querySelector('.inputEventTierPerson').innerHTML;
                        let eventTierPrize = this.querySelector('.inputEventTierPrize').innerHTML;
                        let eventTierEntry = this.querySelector('.inputEventTierEntry').innerHTML;
                        let eventTierTitle = this.querySelector('.inputEventTierTitle').innerHTML;
                        setFormValues( {'eventTierId': eventTierId } );
                        localStorage.setItem('eventTierPerson', eventTierPerson);
                        localStorage.setItem('eventTierPrize', eventTierPrize);
                        localStorage.setItem('eventTierImg', eventTierImg);
                        localStorage.setItem('eventTierEntry', eventTierEntry);
                        localStorage.setItem('eventTierTitle', eventTierTitle);
                        localStorage.setItem('eventTierId', eventTierId);
                        fillStepGameDetailsValues();
                        document.querySelectorAll('.box-tier').forEach(element => {
                            element.classList.remove('color-border-success-dotted', 'rounded-box-turtle',
                                'rounded-box-dolphin', 'rounded-box-starfish', 'rounded-box-mermaid',
                            );
                        });
                        this.querySelector('.box-tier').classList.add('color-border-success-dotted');
                        goToNextScreen('step-4', 'timeline-1');
                        ;"
                    class="featured-events">
                    <a href="#" @class([
                        'event selectable-box box-tier',
                        'rounded-box-' . strtolower($tierCategory->eventTier),
                        'color-border-success-dotted' =>
                            $event && $tierCategory->id == $event->event_tier_id,
                    ])>
                        <div class="event_head_container ">
                            <img id='starfish' src="{{ asset('storage/' . $tierCategory->tierIcon) }}"
                                class="inputEventTierImg event_head">
                        </div>
                        <br>
                        <h4 class="inputEventTierTitle text-center">{{ $tierCategory->eventTier }}</h4>
                        <div style="display: flex; justify-content: start; align-items: center">
                            <img style="width: 25px; height: 25px; margin-right: 20px;"
                                src="{{ asset('/assets/images/createEvent/user.png') }}">

                            <div>
                                <span class="inputEventTierPerson">{{ $tierCategory->tierTeamSlot }}</span>
                                <span>team slots</span>
                            </div>
                        </div>
                        <br>
                        <div style="display: flex; justify-content: start; align-items: center">
                            <img style="width: 25px; height: 25px; margin-right: 20px;"
                                src="{{ asset('/assets/images/createEvent/trophy.png') }}">
                            <div>
                                <span class="inputEventTierPrize">RM {{ $tierCategory->tierPrizePool }}</span>
                                <span>prize pool</span>
                            </div>
                        </div>
                        <br>
                        <div style="display: flex; justify-content: start; align-items: center">
                            <img style="width: 25px; height: 25px; margin-right: 20px;"
                                src="{{ asset('/assets/images/createEvent/dollar.png') }}">
                            <div>
                                <span class="inputEventTierEntry">RM {{ $tierCategory->tierEntryFee }}</span>
                                <span>team entry fee</span>
                            </div>
                        </div>
                        <br>
                    </a>
                </section>
            @endforeach
        </div>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-4', 'timeline-1'); fillStepGameDetailsValues();" type="button"
                class="oceans-gaming-default-button"> Next > </button>
        </div>
    </div>
</div>

<div id="step-4" class="d-none">
    <div class="welcome text-center" style="margin-bottom: -10px !important;">
        <u>
            <h3>
                STEP 1: Choose your Event Categories
            </h3>
        </u>
        <p>Here are the categories you've chosen for your event.</p>
    </div>
    <section class="container-border-2 grid-2" style="justify-content: center !important;">
        <img id="outputGameTitleImg" {!! trustedBladeHandleImageFailure() !!} width=225 height="100%"
            src="{{ asset('assets/images/createEvent/exclamation.png') }}" alt=""
            style="margin: auto; border-radius: 20px; width: 225px; border: 1px dotted black; object-fit: cover; ">
        <div class="box_3rd box_3rd_max_width event_extra mx-auto">
            <h4 id="outputEventTypeTitle">League/ Tier</h4>
            <p id="outputEventTypeDefinition" style="text-align: left;">Choose your event type...</p>
        </div>
        <div class="event_extra rounded-box" id="event-tier-display">
            <div class="event_head_container">
                <img id="outputEventTierImg" src="{{ asset('assets/images/createEvent/question.png') }}"
                    class="event_head" width="60" height="60">
            </div>
            <br>
            <h4 id="outputEventTierTitle" class="text-center mt-1">Choose a tier</h4>
            <div class="event_row">
                <div class="icon_container mr-4 ml-3">
                    <img width="25" height=25 id="outputEventTierImg" src="{{ asset('assets/images/user.png') }}"
                        class="event_icon">
                </div>
                <div class="info_container">
                    <p id="outputEventTierPerson">X</p>
                    <small>team slots</small>
                </div>
            </div>
            <div class="event_row">
                <div class="icon_container mr-4 ml-3">
                    <img width="25" height=25 src="{{ asset('/assets/images/createEvent/trophy.png') }}"
                        class="event_icon">
                </div>
                <div class="info_container">
                    <p id="outputEventTierPrize">RM Y</p>
                    <small>prize pool</small>
                </div>
            </div>
            <div class="event_row">
                <div class="icon_container mr-4 ml-3">
                    <img width="25" height=25 src="{{ asset('assets/images/dollar.png') }}" class="event_icon">
                </div>
                <div class="info_container">
                    <p id="outputEventTierEntry">RM Z</p>
                    <small>team entry fee</small>
                </div>
            </div>
        </div>
    </section>
    <div class="flexbox box-width back-next">
        <button onclick="goToNextScreen('step-3', 'timeline-1');" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-5', 'timeline-2');" type="button"
            class="oceans-gaming-default-button"> Step 2 > </button>
    </div>
</div>

<div class="text-center create d-none" id="step-5">
    <div class="welcome text-center" style="margin-bottom: 0px !important;">
        <u>
            <h5>
                STEP 2: Fill in your Event Details
            </h5>
        </u>
        <p>
            First, when is your event happening?
        </p>
        <br><br>
        <div class="event-details-form">
            <div class="form-group form-group-width mx-auto">
                <label for="startDate">Date of Event</label>
                <div class="small-detail">Tell your players when to mark their calendars</div>
                <div class="grid-2-columns box-date">
                    <div class="box">
                        <div class="small-detail" style="font-weight: bold;"><b>Start</b></div>
                        <input type="date" id="startDate" onchange="checkValidTime();" name="startDate"
                            value="{{ $isEventNotNull ? $event->startDate : '' }}" placeholder=" Select a start date"
                            required>
                    </div>
                    <div class="box">
                        <div class="small-detail" style="font-weight: bold;"><b>End</b></div>
                        <input type="date" id="endDate" onchange="checkValidTime();" name="endDate"
                            value="{{ $isEventNotNull ? $event->endDate : '' }}" placeholder=" Select an end date"
                            required>
                    </div>
                </div>
            </div>
            <div class="form-group form-group-width mx-auto">
                <label for="startTime">Time of Event</label>
                <div class="small-detail">So that your players can set their alarms</div>
                <div class="grid-2-columns box-date">
                    <div class="box">
                        <div class="small-detail" style="font-weight: bold;"><b>Start</b></div>
                        <input type="time" id="startTime" onchange="checkValidTime();" name="startTime"
                            value="{{ $isEventNotNull ? $event->startTime : '' }}" placeholder=" Select a start time"
                            required>
                    </div>
                    <div class="box">
                        <div class="small-detail" style="font-weight: bold;"><b>End</b></div>
                        <input type="time" id="endTime" name="endTime" onchange="checkValidTime();"
                            value="{{ $isEventNotNull ? $event->endTime : '' }}" placeholder=" Select an end time"
                            required>
                    </div>
                </div>
            </div>
        </div>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-4', 'timeline-1'); fillStepGameDetailsValues();" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button"
                class="oceans-gaming-default-button">
                Next > </button>
        </div>
    </div>
</div>

<div class="text-center d-none create" id="step-6">
    <div class="welcome text-center">
        <u>
            <h5>
                STEP 2: Fill in your Event Details
            </h5>
        </u>
        <p>
            Don't forget to name your event!
        </p>
    </div>
    <br><br>
    <div class="event-details-form box-width">
        <div class="form-group mx-auto">
            <label for="eventName">Name of Event</label>
            <p class="description">Pick a good name for your event (max. 60 characters)</p>
            <input value="{{ $isEventNotNull ? $event->eventName : '' }}" type="text" id="eventName"
                name="eventName" placeholder=" Name of Event" required class="@error('title') is-invalid @enderror">
        </div>
    </div>
    <div class="flexbox box-width back-next">
        <button onclick="goToNextScreen('step-5', 'timeline-2')" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-7', 'timeline-2')" type="button" class="oceans-gaming-default-button">
            Next > </button>
    </div>
</div>

<div class="text-center d-none create" id="step-7">
    <div class="welcome text-center">
        <u>
            <h5>
                STEP 2: Fill in your Event Details
            </h5>
        </u>
        <p>
            Next, what's your event about?
        </p>
    </div>
    <br><br>
    <div class="event-details-form box-width">
        <div class="form-group">
            <label for="eventDescription">Event Description</label>
            <p class="description">So, tell us a little bit about your event (max. 3,000 characters)</p>
            @if ($isEventNotNull)
                <textarea id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event"
                    required>{{ $event->eventDescription }}</textarea>
            @else
                <textarea id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event"
                    required></textarea>
            @endif
        </div>
    </div>
    <div class="flexbox box-width back-next">
        <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-8', 'timeline-2'); fillEventTags();" type="button"
            class="oceans-gaming-default-button">
            Next > </button>
    </div>
</div>

<div class="text-center d-none create" id="step-8">
    <div class="welcome text-center">
        <u>
            <h5>
                STEP 2: Fill in your Event Details
            </h5>
        </u>
        <p>
            Keywords wil help players find your event!
        </p>
    </div>
    <br><br>
    <div class="event-details-form box-width">
        <div class="form-group">
            <label for="eventTags">Event Tags</label>
            <p class="description">Add some relevant keywords to help players find your event more easily</p>
            <div class="box">
                <input type="text" id="eventTags" name="eventTags" placeholder="Add tags" required>
            </div>
        </div>
    </div>
    <div class="flexbox box-width back-next">
        <button onclick="goToNextScreen('step-7', 'timeline-2'); fillEventTags();" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button" class="oceans-gaming-default-button">
            Next > </button>
    </div>
</div>

<div class="text-center create d-none" id="step-9">
    <div class="welcome text-center"
        style="margin-bottom: 0px !important; padding-top: 10px !important; padding-bottom: 0px !important;">
        <u>
            <h5>
                STEP 2: Fill in your Event Details
            </h5>
        </u>
        <p>
            Finally, some visual aid!
        </p>
        <br>
        <div class="event-details-form box-width">
            <div class="form-group">
                <label for="eventBanner">Event Banner</label>
                <p class="description">A distinctive banner will help your event stand out (minimum resolution: 1400px
                    x 600px).</p>
                <div class="banner-upload">
                    <input onchange="handleFile('eventBanner', 'previewImage');" type="file" id="eventBanner"
                        name="eventBanner" accept="image/*" required>
                    <div class="banner-preview">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-image">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                    Supported files: JPG and PNG<br>
                    <label class="upload-button" for="eventBanner">Upload Image</label>
                    <br>
                </div>
                @if ($event)
                    @if ($event->eventBanner)
                    @else
                        <div style="color: #EF4444;" id="preview-image-warning">Please enter an image</div>
                    @endif
                    <img @class([
                        'd-none' => is_null($event->eventBanner),
                        'banner-preview-img',
                    ]) src="{{ bladeImageNull($event->eventBanner) }}"
                        {!! trustedBladeHandleImageFailure() !!} id="previewImage" alt="Preview" width="350px" height="auto">
                @else
                    <img class="d-none banner-preview-img" id="previewImage" alt="Preview" width="350px"
                        height="auto">
                @endif
            </div>
        </div>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-8', 'timeline-2')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-launch-1', 'timeline-launch');" type="button"
                class="oceans-gaming-default-button"> Step 3 > </button>
        </div>
    </div>
</div>

<div class="text-center d-none" id="step-launch-1">
    <div class="welcome" style="margin-bottom: 0px;">
        <u>
            <h5>
                STEP 3: Launch your event
            </h5>
        </u>
    </div>
    <br>
    <div class="payment-summary">

        @if ($isEventNotNull)
            @if ($status == 'DRAFT')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success">Your event is currently saved as draft.</p>
                </div>
            @elseif ($status == 'SCHEDULED')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success">Your {{ $event->sub_action_private }} event is scheduled to launch on:
                        {{ $combinedStr }} at
                        {{ $timePart }}. </p>
                </div>
            @elseif ($status == 'UPCOMING' || $status == 'ONGOING')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success">Your {{ $event->sub_action_private }} event is live now
                    </p>
                </div>
            @elseif ($status == 'ENDED')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success">Your {{ $event->sub_action_private }} event has already ended
                    </p>
                </div>
            @elseif ($status == 'PENDING')
            <div>
                <h5>Your payment status is pending!</h5>
                    <p class="text-success">Your {{ $event->sub_action_private ?? 'public / private' }} event's payment status is pending.
                    </p>
                    <p class="text-success"> 
                        @if ($event->status == "DRAFT")
                        You chose a draft event.
                        @else
                        Launch type: {{ $event->sub_action_private }}
                        <br>
                        Launch time chosen: {{ $event->sub_action_public_time }} {{ $event->sub_action_public_date ?? 'N/A' }}
                        @endif
                    </p>
                </div>
            @endif

        @endif
        <input checked onchange="toggleRadio(this, 'draft'); updateLaunchButton('draft');" type="radio"
            id="draft" name="launch_visible" value="DRAFT">
        <label for="draft"><u>Save as draft</u></label>
        <div class="radio-indent draft">
            <p>Save your event and edit it later</p>
        </div>

        <!-- public? -->
        <input
            onchange="toggleRadio(this, 'public' ); updateLaunchButton('launch'); launchScheduleDefaultSelected('launch_schedule_default_1');"
            required type="radio" id="public" name="launch_visible" value="public">
        <label for="public"><u>Public</u></label><br>
        <div class="radio-indent public">
            <p>Everyone can see and join your event</p>
        </div>

        <div class="radio-indent-hidden public d-none">
            <input onchange="updateLaunchButton('launch');" type="radio" class="launch_schedule_default_1"
                name="launch_schedule" value="now">
            <label for="sub_action_public"><u>Launch now</u></label><br>
            <input onchange="updateLaunchButton('schedule');" type="radio" id="launch_schedule"
                name="launch_schedule" value="schedule">
            <label for="sub_action_public"><u>Schedule launch</u></label><br>
            <div class="container">
                <div class="box">
                    <input onchange="updateLaunchButton('schedule');" type="date" id="sub_action_public_date"
                        name="launch_date_public"
                        value="{{ $isEventNotNull ? $event->sub_action_public_date : '' }}">
                </div>
                <div class="box">
                    <input onchange="updateLaunchButton('schedule');" type="time" id="sub_action_public_time"
                        name="launch_time_public"
                        value="{{ $isEventNotNull ? $event->sub_action_public_time : '' }}">
                </div>
            </div>
        </div>
        <input
            onchange="toggleRadio(this, 'private'); updateLaunchButton('launch'); launchScheduleDefaultSelected('launch_schedule_default_2');"
            required type="radio" id="private" name="launch_visible" value="private">
        <label for="private"><u>Private</u></label><br>
        <div class="radio-indent private">
            <p>Only players you invite can see and join your event</p>
        </div>

        <div class="radio-indent-hidden private d-none">
            <!-- private launch now? -->
            <input onchange="updateLaunchButton('launch');" type="radio" class="launch_schedule_default_2"
                name="launch_schedule" value="now">
            <label for="sub_action_public"><u>Launch now</u></label><br>

            <!-- private launch schedule? -->
            <input onclick="updateLaunchButton('schedule');" type="radio" id="launch_schedule"
                name="launch_schedule" value="schedule">
            <label for="sub_action_public"><u>Schedule launch</u></label><br>
            <!-- private launch date? -->
            <div class="container">
                <div class="box">
                    <input onclick="updateLaunchButton('schedule');" type="date" id="sub_action_public_date"
                        name="launch_date_private"
                        value="{{ $isEventNotNull ? $event->sub_action_public_date : '' }}">
                </div>
                <div class="box">
                    <input onclick="updateLaunchButton('schedule');" type="time" id="sub_action_public_time"
                        name="launch_time_private"
                        value="{{ $isEventNotNull ? $event->sub_action_public_time : '' }}">
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="button" class="oceans-gaming-default-button" onclick="saveForLivePreview();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="feather feather-eye">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                &nbsp;&nbsp;
                <u id="preview-button-text">Preview your event page</u>
            </button>
        </div>

    </div>
    <br>

    <br>
    <div class="flexbox box-width back-next">
        <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToPaymentPage();" type="button"
            class="oceans-gaming-default-button" id="launch-button"> Step 4 > </button>
    </div>
    <br>
</div>

<script> 
    function goToPaymentPage() {
        goToNextScreen('step-payment', 'timeline-payment');
        fillStepPaymentValues();
    }

    function goToLaunch2ndPage() {
        goToNextScreen('step-launch-2', 'timeline-launch');
    }
</script>

<div class="text-center d-none" id="step-launch-2">
    <div class="welcome">
        <u>
            <h5>
                STEP 3: Launch your event
            </h5>
        </u>
    </div>
    <br>
    <div class="payment-summary" style="text-align: center">
        <br>
        <h5>Launch Event Now?</h5>
        <p>You are about to launch your your event to the world.</p>
        <p>Once your event is live, you will no longer be able to make any changes to it, and it will appear to players
            as it is.</p>
        <p>Are your sure you want to launch your event now?</p>
        <br>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-launch-1', 'timeline-launch'); " type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button"> Cancel </button>
            <button onclick="goToPaymentPage()" type="button" class="oceans-gaming-default-button"> Yes, I'm sure
            </button>
        </div>
        <br>
    </div>
    <br>
</div>


<div class="text-center d-none" id="step-payment">
    <div class="welcome" style="margin-bottom: -20px;">
        <u>
            <h5>
                STEP 4: Complete the Payment
            </h5>
        </u>
        <br>
        <div class="payment-summary">
            <h5>Payment Summary </h5>
            <div>Event Categories</div>
            <div class="ml-3">Type: <span id="paymentType"> </span></div>
            <div class="ml-3">Tier: <span id="paymentTier"> </span></div>
            <div class="flexbox">
                <span>Subtotal</span>
                <span id="paymentSubtotal" id="subtotal"></span>
            </div>
            <div class="flexbox">
                <span>Event Creation Fee Rate</span>
                <span id="paymentRate"></span>
            </div>
            <div class="flexbox">
                <span>Event Creation Fee total</span>
                <span id="paymentFee"></span>
            </div>
            <br>
            <div class="flexbox">
                <h5> TOTAL </h5>
                <h5 id="paymentTotal"></h5>
            </div>
            <br>
            <div class="text-center">
                @if ($event && $event->payment_transaction_id != null)
                    <button class="choose-payment-method"
                        style="background-color: #8CCD39 !important;" type="button">
                        Paid successfully!
                    </button>
                @else
                    <button onclick="setFormValues( {'goToCheckoutPage': 'yes'} ); saveEvent(false);" type="button" class="choose-payment-method" data-toggle="modal"
                        data-target="#payment-modal">
                        Go to checkout page
                    </button>
                @endif
                <button onclick="goToNextScreen('step-1', 'timeline-1');" type="button"
                    class="choose-payment-method-condition-fulfilled" style="background-color: #EF4444;">
                    Choose event tier and title first
                </button>
            </div>
        </div>
        <br>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-launch-1', 'timeline-launch');" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="saveEvent(false)" type="button" type="button" 
                class="oceans-gaming-default-button"> Save, pay later </button>
        </div>
    </div>
</div>
