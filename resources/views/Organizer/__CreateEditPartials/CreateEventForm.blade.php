@php
    $isEventNotNull = isset($event);
    $status = $isEventNotNull ? $event->statusResolved() : null;
    $dateStartArray = null;
    if ($isEventNotNull) {
        $dateStartArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
        extract($dateStartArray);
    }
@endphp
<div id="eventContainer" 
    data-event="{{ json_encode($event ?? null) }}"
    data-tier="{{ json_encode($tier) }}"
    data-type="{{ json_encode($type) }}"
    data-game="{{ json_encode($game) }}"
    data-asset-key-word="{{ asset('') }}"
    class="d-none"    
>
</div>
<div id="step-2" class="d-none">

    <div class="welcome text-center">
        <h3>
            STEP 1: Choose your <span class="text-primary">event categories</span>
        </h3>
        <br>
        <p>Then, select what kind of event you want to run.</p>
        <div class="box-width">
            <div class="grid-2-columns box-width" >
                @foreach ($eventTypeList as $gameCategory)
                    @if ($gameCategory->eventDefinitions)
                        <div  
                            data-event-type="{{ $gameCategory->eventType }}"
                            data-event-type-id="{{ $gameCategory->id }}"
                            data-event-definitions="{{ $gameCategory->eventDefinitions }}"
                            onclick="setFormValuesAndNavigate(this)"
                            @class(['container-border'])
                        >
                            <a href="#" @class([
                                'box_2nd selectable-box ',
                                ' container-border-dotted ' => $gameCategory->eventType == "League",
                                'color-border-success' =>
                                    $event && $gameCategory->id == $event->event_type_id,
                            ])>
                                <h2 class="{{ 'inputEventTypeTitle box-title' }}">
                                    <u>{{ $gameCategory->eventType }}</u>
                                </h2>
                                <span class="inputEventTypeDefinition" class="box-text"
                                    style="text-align: left;">{!! $gameCategory->eventDefinitions !!}</span>
                                @if ($gameCategory->eventType == "League")
                                    <h5 class="text-primary mt-2 text-center fw-bold"> COMING SOON</h5>
                                @endif 
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-1', 'none')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-3', 'timeline-1')" type="button"
                class="oceans-gaming-default-button"> Next&nbsp;&nbsp;  &gt; </button>
        </div>
    </div>
</div>



<div id="step-3" class="d-none">
    <div class="welcome text-center">
         <h3>
            STEP 1: Choose your <span class="text-primary">event categories</span>
        </h3>
        <p class="my-3">Finally, choose a tier for your event.</p>
        <div class="row  box-width">
            @foreach ($eventTierList as $tierCategory)
                <section
                    data-event-tier="{{ $tierCategory->eventTier }}"
                    data-event-tier-id="{{ $tierCategory->id }}"
                    class="featured-events col-12 col-xl-4 py-0"
                    onclick="handleTierSelection(this)"
                >
                    <a href="#" 
                        @class([
                        'event pt-2 selectable-box box-tier ps-5 pe-5 mx-auto',
                        'rounded-box-' . strtolower($tierCategory->eventTier),
                        'color-border-success-dotted' =>
                            $event && $tierCategory->id == $event->event_tier_id,
                    ])>
                        <div class="text-center pt-2 pb-2">
                            <img id='starfish' height="40" width="40" src="{{ asset('storage/' . $tierCategory->tierIcon) }}"
                                class="inputEventTierImg object-fit-cover"
                            > 
                            <h5 class="ms-2 d-inline inputEventTierTitle"> {{ $tierCategory->eventTier }}</h5>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-3">
                            <img style="width: 25px; height: 25px; margin-right: 20px;"
                                src="{{ asset('/assets/images/createEvent/user.png') }}">

                            <div>
                                <span class="d-block inputEventTierPerson text-start m-0">{{ $tierCategory->tierTeamSlot }}</span>
                                <span class="m-0 d-block">team slots</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-3">
                            <img style="width: 25px; height: 25px; margin-right: 20px;"
                                src="{{ asset('/assets/images/createEvent/trophy.png') }}">
                            <div>
                                <span class="inputEventTierPrize text-start d-block m-0">RM {{ $tierCategory->tierPrizePool }}</span>
                                <span class="m-0 d-block">prize pool</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-3">
                            <img style="width: 25px; height: 25px; margin-right: 20px;"
                                src="{{ asset('/assets/images/createEvent/dollar.png') }}">
                            <div>
                                <span class="inputEventTierEntry d-block text-start m-0">RM {{ $tierCategory->tierEntryFee }}</span>
                                <span class="m-0 d-block">team entry fee</span>
                            </div>
                        </div>
                    </a>
                </section>
            @endforeach
        </div>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-4', 'timeline-1'); fillStepGameDetailsValues();" type="button"
                class="oceans-gaming-default-button"> Next&nbsp;&nbsp;  &gt; </button>
        </div>
    </div>
</div>

<div id="step-4" class="d-none">
    <div class="welcome text-center">
        <h3>
            STEP 1: Choose your <span class="text-primary">event categories</span>
        </h3>
        <p class="m-3">Here are the categories you've chosen for your event.</p>
    </div>
    <section class="container-border-2 grid-2 justify-content-center py-0">
        <img id="outputGameTitleImg" {!! trustedBladeHandleImageFailure() !!} width=225 height="100%"
            src="{{ asset('assets/images/createEvent/exclamation.png') }}" alt=""
            style="margin: auto; border-radius: 20px; width: 180px; border: 1px dotted black; object-fit: cover; ">
        <div class="box_3rd box_3rd_max_width event_extra mx-auto">
            <h4 id="outputEventTypeTitle">League/ Tier</h4>
            <p class="mt-4 text-center" id="outputEventTypeDefinition">Choose your event type...</p>
        </div>
        <div class="event_extra rounded-box" id="event-tier-display">
            <div class="event_head_container">
                <img id="outputEventTierImg" src="{{ asset('assets/images/createEvent/question.png') }}"
                    class="event_head" width="40" height="40"
                >
            </div>
            <h4 id="outputEventTierTitle" class="text-center mt-2 mb-2">Choose a tier</h4>
            <div class="event_row">
                <div class="icon_container me-4 ms-3">
                    <img width="25" height=25 id="outputEventTierImg" src="{{ asset('assets/images/user.png') }}"
                        class="event_icon">
                </div>
                <div class="info_container">
                    <p class="my-1" id="outputEventTierPerson">X</p>
                    <small>team slots</small>
                </div>
            </div>
            <div class="event_row">
                <div class="icon_container me-4 ms-3">
                    <img width="25" height=25 src="{{ asset('/assets/images/createEvent/trophy.png') }}"
                        class="event_icon">
                </div>
                <div class="info_container">
                    <p class="my-1" id="outputEventTierPrize">RM Y</p>
                    <small>prize pool</small>
                </div>
            </div>
            <div class="event_row">
                <div class="icon_container me-4 ms-3">
                    <img width="25" height=25 src="{{ asset('assets/images/dollar.png') }}" class="event_icon">
                </div>
                <div class="info_container">
                    <p class="my-1" id="outputEventTierEntry">RM Z</p>
                    <small>team entry fee</small>
                </div>
            </div>
        </div>
    </section>
    <div class=" d-flex justify-content-between box-width back-next">
        <button onclick="goToNextScreen('step-3', 'timeline-1');" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-5', 'timeline-2');" type="button"
            class="oceans-gaming-default-button"> Step 2 > </button>
    </div>
</div>

<div class="text-center create d-none" id="step-5">
    <div class="welcome text-center" >
            <h3>
                STEP 2: Fill in your <span class="text-primary">event details</span>
            </h3>
        <p>
            First, when is your event happening?
        </p>
        <br><br>
        <div class="event-details-form row mx-auto">
            <div class="form-group col-12 col-lg-6 mx-auto">
                <label for="mt-3 startDate">Date of Event</label>
                <div class="my-3">Tell your players when to mark their calendars</div>
                 <div class="mx-auto d-flex justify-content-center  ">
                    <input type="text" id="daterange-display" class="ps-3 rounded-pill" readonly>
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi mt-2 ms-3 me-3 bi-calendar-day" viewBox="0 0 16 16">
                    <path d="M4.684 11.523v-2.3h2.261v-.61H4.684V6.801h2.464v-.61H4v5.332zm3.296 0h.676V8.98c0-.554.227-1.007.953-1.007.125 0 .258.004.329.015v-.613a2 2 0 0 0-.254-.02c-.582 0-.891.32-1.012.567h-.02v-.504H7.98zm2.805-5.093c0 .238.192.425.43.425a.428.428 0 1 0 0-.855.426.426 0 0 0-.43.43m.094 5.093h.672V7.418h-.672z"/>
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                    </svg>
                </div>
                <div class="box-date d-none">
                    <div class="box">
                        <div class="small-detail" style="font-weight: bold;"><b>Start</b></div>
                        <input type="date" id="startDate" onchange="checkValidTime();" name="startDate"
                            value="{{ $isEventNotNull ? $event->startDate : '' }}" placeholder=" Select a start date"
                            required
                        >
                    </div>
                    <div class="box">
                        <div class="small-detail" style="font-weight: bold;"><b>End</b></div>
                        <input type="date" id="endDate" onchange="checkValidTime();" name="endDate"
                            value="{{ $isEventNotNull ? $event->endDate : '' }}" placeholder=" Select an end date"
                            required>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-lg-6 mx-auto">
                <label for="mt-3 startTime">Time of Event</label>
                <div class="my-3">So that your players can set their alarms</div>
                <div class="mx-auto d-flex justify-content-center  ">
                    <input type="text" id="timerange-display" class="ps-3 rounded-pill dropdown" readonly
                         data-bs-toggle="dropdown" data-bs-auto-close="false"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-alarm mt-2 ms-3 me-3" viewBox="0 0 16 16">
                    <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z"/>
                    <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1"/>
                    </svg>
                    <div class="dropdown-menu p-3 border border-2 border-primary" style="background: #f9f7ef !important; width: min(400px, 90vw);">
                        <div class="box">
                            <div class="small-detail" style="font-weight: bold;"><b>Start</b></div>
                            <div class="flatpickr">
                                <input type="time" id="startTime" onchange="checkValidTime();setTimeRangeDisplay();" name="startTime"
                                    value="{{ $isEventNotNull ? $event->startTime : '' }}" 
                                    class="form-control w-75 border-dark text-dark" required data-input
                                >
                                
                            </div>
                        </div>
                        <div class="box mt-3">
                            <div class="small-detail" style="font-weight: bold;"><b>End</b></div>
                            <div class="flatpickr">
                                <input type="time" id="endTime" name="endTime" onchange="checkValidTime();setTimeRangeDisplay();"
                                    value="{{ $isEventNotNull ? $event->endTime : '' }}" 
                                    class="form-control w-75 border-dark text-dark" required data-input
                                >
                                
                            </div>
                        </div>
                        <div class="mt-3 ms-5">
                            <button class="btn rounded-pill bg-primary text-light" type="button" onclick="closeDropDown()">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-4', 'timeline-1'); fillStepGameDetailsValues();" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button"
                class="oceans-gaming-default-button">
                Next&nbsp;&nbsp;  &gt; </button>
        </div>
    </div>
</div>

<div class="text-center d-none create" id="step-6">
    <div class="welcome text-center">
            <h3>
                STEP 2: Fill in your <span class="text-primary">event details</span>
            </h3>
        <p>
            Don't forget to name your event!
        </p>
    </div>
    <div class="event-details-form box-width">
        <div class="form-group mx-auto">
            <label for="eventName">Name of Event</label>
            <p class="my-3">Pick a good name for your event.</p>
            <input value="{{ $isEventNotNull ? $event->eventName : '' }}" type="text" id="eventName"
                name="eventName" placeholder=" Name of Event" required class="@error('title') is-invalid @enderror">
            <p class="description text-end mt-2"><i><span class="character-count-eventName">60</span> characters remaining</i></p>

        </div>
    </div>
    <div class=" d-flex justify-content-between box-width back-next">
        <button onclick="goToNextScreen('step-5', 'timeline-2')" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-7', 'timeline-2')" type="button" class="oceans-gaming-default-button">
            Next&nbsp;&nbsp;  &gt; </button>
    </div>
</div>

<div class="text-center d-none create" id="step-7">
    <div class="welcome text-center">
            <h3>
                STEP 2: Fill in your <span class="text-primary">event details</span>
            </h3>
        <p>
            Next, what's your event about?
        </p>
    </div>
    <div class="event-details-form box-width">
        <div class="form-group">
            <label for="eventDescription">Event Description</label>
            <p class="my-3">Tell the players a little about your event</p>
            @if ($isEventNotNull)
                <textarea class="textarea-size" id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event"
                    required>{{ $event->eventDescription }}</textarea>
            @else
                <textarea class="textarea-size" id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event"
                    required></textarea>
            @endif
            <p class="description text-end mt-2"><i><span class="character-count-eventDescription">3000</span> characters remaining</i></p>

        </div>
    </div>
    <div class=" d-flex justify-content-between box-width back-next">
        <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-8', 'timeline-2'); fillEventTags();" type="button"
            class="oceans-gaming-default-button">
            Next&nbsp;&nbsp;  &gt; </button>
    </div>
</div>

<div class="text-center d-none create" id="step-8">
    <div class="welcome text-center">
            <h3>
                STEP 2: Fill in your <span class="text-primary">event details</span>
            </h3>
        <p>
            Keywords wil help players find your event!
        </p>
    </div>
    <div class="event-details-form box-width">
        <div class="form-group">
            <label for="eventTags">Event Tags</label>
            <p class="my-3">Add some relevant keywords to help players find your event more easily</p>
            <div class="box">
                <input type="text" id="eventTags" name="eventTags" placeholder="Add tags" required class="w-100 rounded-pil">
            </div>
        </div>
    </div>
    <div class=" d-flex justify-content-between box-width back-next">
        <button onclick="goToNextScreen('step-7', 'timeline-2'); fillEventTags();" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button" class="oceans-gaming-default-button">
            Next&nbsp;&nbsp;  &gt; </button>
    </div>
</div>

<div class="text-center create d-none" id="step-9">
    <div class="welcome text-center"
        style="padding-top: 10px !important; padding-bottom: 0px !important;">
            <h3>
                STEP 2: Fill in your <span class="text-primary">event details</span>
            </h3>
        <p>
            Finally, some visual aid!
        </p>
        <br>
        <div class="event-details-form box-width">
            <div class="form-group ">
                <label for="eventBanner">Event Banner</label>
                <p class="mt-3" style="font-size: 16px;">A distinctive banner will help your event stand out. </p>
                <p class="description"><i>Minimum resolution: 1400x600 (16:9)</i></p>
                <div class="banner-upload mx-auto">
                    <input onchange="handleFile('eventBanner', 'previewImage');" type="file" id="eventBanner"
                        name="eventBanner" accept="image/*" required
                    >
                    <div class="banner-preview pt-4 position-relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-image z-index-11">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <div style="color: black;" class=" z-index-11">Supported files: JPEG, JPG and PNG</div><br>
                        <label class=" upload-button btn btn-primary text-light d-inline rounded-pill py-2 px-3 z-index-11 " for="eventBanner">Upload Image</label><br>
                        @if ($event)
                            <img @class([
                                'd-none' => is_null($event->eventBanner),
                                'banner-preview-img z-index-4',
                            ]) src="{{ bladeImageNull($event->eventBanner) }}"
                                {!! trustedBladeHandleImageFailure() !!} id="previewImage" alt="Preview" 
                            >
                        @else
                            <img class="d-none banner-preview-img z-index-4" id="previewImage" alt="Preview" 
                                height="auto"
                            >
                        @endif
                    </div>
                  
                </div>
              
            </div>
        </div>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-8', 'timeline-2')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-launch-1', 'timeline-launch');" type="button"
                class="oceans-gaming-default-button"> Step 3 > </button>
        </div>
    </div>
</div>

<div class="text-center d-none" id="step-launch-1">
    <div class="welcome mb-2" >
            <h3>
                STEP 3: Set your event's <span class="text-primary">visibility</span>
            </h3>
    </div>
    <div class="payment-summary mt-3 ">
        @if ($isEventNotNull)
            @if ($status == 'DRAFT')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success my-2">Your event is currently saved as draft.</p>
                </div>
            @elseif ($status == 'SCHEDULED')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success my-2">Your {{ $event->sub_action_private }} event is scheduled to launch on:
                        {{ $combinedStr }} at
                        {{ $timePart }}. </p>
                </div>
            @elseif ($status == 'UPCOMING' || $status == 'ONGOING')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success my-2">Your {{ $event->sub_action_private }} event is live now
                    </p>
                </div>
            @elseif ($status == 'ENDED')
                <div>
                    <h5>Event Status</h5>
                    <p class="text-success my-2">Your {{ $event->sub_action_private }} event has already ended
                    </p>
                </div>
            @elseif ($status == 'PENDING')
            <div>
                    <p class="text-success my-2"> 
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
        <input @if (!$isEventNotNull || $isEventNotNull && $status == "DRAFT") checked @endif onchange="toggleRadio(this, 'draft'); updateLaunchButton('draft');" type="radio"
            id="draft" name="launch_visible" value="DRAFT" class="form-check-input"
        >
        <label for="draft"><u>Save as draft</u></label>
        <div class="radio-indent draft">
            <p class="my-2 py-0">Save your event and edit it later</p>
        </div>

        <!-- public? -->
        <input 
            @if ($isEventNotNull && $status != "DRAFT" && $event->sub_action_private ==  "public" ) checked @endif
            onchange="toggleRadio(this, 'public' ); updateLaunchButton('launch'); launchScheduleDefaultSelected('launch_schedule_default_1');"
            required type="radio" id="public" name="launch_visible" value="public"
            class="form-check-input"
        >
        <label for="public"><u>Public</u></label><br>
        <div class="radio-indent py-0 my-0 public">
            <p class="py-0 my-0">Everyone can see and join your event</p>
        </div>

        <div @class(["radio-indent-hidden my-0 py-0", "public", 
            "d-none" => !$isEventNotNull || $status == "DRAFT" || $event->sub_action_private ==  "private"
        ])>
            <input @if ($isEventNotNull && !$event->sub_action_public_date && $event->sub_action_private ==  "public") checked @endif
                onchange="updateLaunchButton('launch');" type="radio" class="launch_schedule_default_1 form-check-input"
                name="launch_schedule" value="now">
            <label for="sub_action_public"><u>Launch now</u></label><br>
            <input 
                @if ($isEventNotNull && $event->sub_action_public_date && $event->sub_action_private == "public") checked @endif 
                onchange="updateLaunchButton('schedule');" type="radio" id="launch_schedule"
                name="launch_schedule" value="schedule" class="form-check-input"
            >
            <label for="sub_action_public"><u>Schedule launch</u></label><br>
            <div class="container d-flex justify-content-start">
                <div class="box me-2">
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
            disabled
            @if ($isEventNotNull && $status != "DRAFT" && $event->sub_action_private ==  "private" ) checked @endif
            onchange="toggleRadio(this, 'private'); updateLaunchButton('launch'); launchScheduleDefaultSelected('launch_schedule_default_2');"
            required type="radio" id="private" name="launch_visible" value="private" class="form-check-input mt-3"
        >
        <label for="private" class="mt-2">
            <u>Private</u>
            <small class="text-primary text-center fw-bold ms-2"> COMING SOON</small>
        </label><br>
        <div class="radio-indent py-0 my-0 private">
            <p class="my-0 py-0">Only players you invite can see and join your event</p>
        </div>

        <div @class(["radio-indent-hidden py-0 my-0 ", "private", 
            "d-none" => !$isEventNotNull || $status == "DRAFT" || $event->sub_action_private ==  "public"
        ])>
            <!-- private launch now? -->
            <input
                @if ($isEventNotNull && !$event->sub_action_public_date && $event->sub_action_private == "private") checked @endif
                onchange="updateLaunchButton('launch');" type="radio" class="launch_schedule_default_2 form-check-input"
                name="launch_schedule" value="now">
            <label class="my-0 py-0" for="sub_action_public"><u>Launch now</u></label><br>

            <!-- private launch schedule? -->
            <input 
                @if ($isEventNotNull && $event->sub_action_public_date && $event->sub_action_private == "private") checked @endif
                onclick="updateLaunchButton('schedule');" type="radio" id="launch_schedule"
                name="launch_schedule" value="schedule" class="form-check-input"
            >
            <label for="sub_action_public" class="my-0 py-0" ><u>Schedule launch</u></label><br>
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

       
        
    </div>
    @if ($isEventNotNull || !$editMode) 
        <div class="text-center mt-3">
            <button type="button" class="btn btn-link border border-3 text-primary border-primary rounded-pill mt-2  " onclick="saveForLivePreview();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="#43a4d7" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="feather feather-eye">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                &nbsp;&nbsp;
                <span id="preview-button-text" class="text-primary">Preview your event page</span>
            </button>
        </div>
    @endif
    <br>
    <div class=" d-flex justify-content-between box-width back-next">
        <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button"
            class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
        <button onclick="goToPaymentPage();" type="button"
            class="oceans-gaming-default-button" id="launch-button"> Step 4 > </button>
    </div>
    <br>
</div>


<div class="text-center d-none" id="step-launch-2">
    <div class="welcome mb-1">
            <h3>
                STEP 3: Set your event's <span class="text-primary">visibility</span>
            </h3>
    </div>
    <div class="payment-summary mt-5" style="text-align: center">
        <br>
        <h5>Launch Event Now?</h5>
        <p>You are about to launch your your event to the world.</p>
        <p>Once your event is live, you will no longer be able to make any changes to it, and it will appear to players
            as it is.</p>
        <p>Are your sure you want to launch your event now?</p>
        <br>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-launch-1', 'timeline-launch'); " type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button"> Cancel </button>
            <button onclick="goToPaymentPage()" type="button" class="oceans-gaming-default-button"> Yes, I'm sure
            </button>
        </div>
    </div>
    <br>
</div>


<div class="text-center d-none" id="step-payment">
    <div class="welcome mb-1" >
            <h3>
                STEP 4: Complete the <span class="text-primary">payment </span>
            </h3>
        <div class="payment-summary mt-4">
            <h5>Payment Summary </h5>
            <div>Event Categories</div>
            <div class="ms-3">Type: <span id="paymentType"> </span></div>
            <div class="ms-3">Tier: <span id="paymentTier"> </span></div>
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
                        Payment Successful
                    </button>
                @else
                    <button onclick="setFormValues( {'goToCheckoutPage': 'yes'} ); saveEvent(false);" type="button" class="choose-payment-method">
                        Choose a payment method
                    </button>
                @endif
                <button onclick="goToNextScreen('step-1', 'timeline-1');" type="button"
                    class="choose-payment-method-condition-fulfilled" style="background-color: #EF4444;">
                    Choose event tier and title first
                </button>
            </div>
        </div>
        <br>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-launch-1', 'timeline-launch');" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="saveEvent(false)" type="button" type="button" 
                class="oceans-gaming-default-button"> Save </button>
        </div>
    </div>
</div>
