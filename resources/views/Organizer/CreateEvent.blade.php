    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        <main>
            <div>
                @include('CommonLayout.Navbar')
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        <input type="hidden" name="gameTitle" id="gameTitle" value="{{ $event ? $event->gameTitle: ''  }}">
                        <input type="hidden" name="eventTier" id="eventTier" value="{{ $event ? $event->eventTier: ''  }}">
                        <input type="hidden" name="eventType"  id="eventType" value="{{ $event ? $event->eventType: ''  }}">
                        <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod">

                        <div class="time-line-box">
                            <div class="swiper-container text-center">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                                        <div class="timestamp"><span>Categories</span></div>
                                        <div class="status__left"><span><small></small></span></div>
                                    </div>
                                    <div class="swiper-slide" id="timeline-2">
                                        <div class="timestamp"><span>Details</span></div>
                                        <div class="status"><span><small></small></span></div>
                                    </div>
                                    <div class="swiper-slide" id="timeline-3">
                                        <div class="timestamp"><span>Payment</span></div>
                                        <div class="status"><span><small></small></span></div>
                                    </div>
                                    <div class="swiper-slide swiper-slide__right" id="timeline-4">
                                        <div class="timestamp"><span class="date">Launch</span></div>
                                        <div class="status__right"><span><small></small></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center d-none" id="step-0">
                            <div class="welcome">
                                <u>
                                    <h2>
                                        Welcome to Splash's Event Creator
                                    </h2>
                                </u>
                                <br><br><br>
                                <p class="create-online-esports">
                                    Create online esports events all on your own, right here on Splash, in just 4 steps.
                                </p>
                            </div>
                            <input type="button" onclick="goToNextScreen('step-1', 'timeline-1')" value="Continue">
                        </div>

                        <div id="step-1" class="">
                            <div class="welcome text-center" style="margin-bottom: -25px !important;">
                                <u>
                                    <h3>
                                        STEP 1: Choose your Event Categories
                                    </h3>
                                </u>
                                <p>First, select an esport title</p>
                                <div class="image-scroll-container box-width">
                                    @foreach ($eventCategory as $category)
                                    @if ($category->gameIcon)
                                    <div class="scroll-images" onclick="
                                    setFormValues( {'gameTitle': {{Js::from($category->gameTitle)}} } ); 
                                    goToNextScreen('step-2', 'timeline-1');
                                    let gameTitleImg = this.children[0].children[0].src;
                                    localStorage.setItem('gameTitleImg', gameTitleImg);
                                    ">
                                        <a href="#">
                                            <img class="selectable-image focused" src="<?php echo asset("storage/images/$category->gameIcon"); ?>" alt="" style="object-fit: cover; border-radius: 20px; height: 325px; width: 220px;"></a>
                                        <h5 style="padding-top: 10px;">{{ $category->gameTitle}}</h5>
                                    </div>
                                    @endif
                                    @endforeach
                                    <!-- Add more images and titles here -->
                                </div>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-0', 'none')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                    <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next > </button>
                                </div>
                            </div>
                        </div>

                        <div id="step-2" class="d-none">
                            <div class="welcome text-center" style="margin-bottom: 0px !important;">
                                <u>
                                    <h3>
                                        STEP 1: Choose your Event Categories
                                    </h3>
                                </u>
                                <p>Then, select what kind of event you want to run.</p>
                                <div class="box-width">
                                    <div class="grid-2-columns box-width" style="margin-top: -20px !important;">
                                        @foreach ($eventCategory as $category)
                                        @if ($category->eventDefinitions)
                                        <div onclick="setFormValues( {'eventType': {{Js::from($category->eventType) }} } ); 
                                goToNextScreen('step-3', 'timeline-1');
                                let eventTypeTitle = this.querySelector('.inputEventTypeTitle u').innerHTML;
                                let eventTypeDefinition = this.querySelector('.inputEventTypeDefinition').innerHTML;
                                localStorage.setItem('eventTypeTitle', eventTypeTitle);
                                localStorage.setItem('eventTypeDefinition', eventTypeDefinition);
                                " class="container-border">
                                            <a href="#" class="box_2nd selectable-box">
                                                <h2 class="inputEventTypeTitle" class="box-title"><u>{{ $category->eventType}}</u></h2>
                                                <span class="inputEventTypeDefinition" class="box-text" style="text-align: left;">{{ $category->eventDefinitions}}</span>
                                            </a>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-1', 'none')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                    <button onclick="goToNextScreen('step-3', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next > </button>
                                </div>
                            </div>
                        </div>



                        <div id="step-3" class="d-none">
                            <div class="welcome text-center" style="margin-bottom: -10px !important;">
                                <u>
                                    <h3>
                                        STEP 1: Choose your Event Categories
                                    </h3>
                                </u>
                                <br>
                                <p>Finally, choose a tier for your event.</p>
                                <div class="flexbox box-width">
                                    @foreach ($eventCategory as $category)
                                    <section onclick="setFormValues( {'eventTier': {{Js::from($category->eventTier)}} } ); 
                                    goToNextScreen('step-4', 'timeline-1'); 
                                    let eventTierImg = this.querySelector('.inputEventTierImg').src;
                                    let eventTierPerson = this.querySelector('.inputEventTierPerson').innerHTML;
                                let eventTierPrize = this.querySelector('.inputEventTierPrize').innerHTML;
                                let eventTierEntry = this.querySelector('.inputEventTierEntry').innerHTML;
                                let eventTierTitle = this.querySelector('.inputEventTierTitle').innerHTML;
                                localStorage.setItem('eventTierPerson', eventTierPerson);
                                localStorage.setItem('eventTierPrize', eventTierPrize);
                                localStorage.setItem('eventTierImg', eventTierImg);
                                localStorage.setItem('eventTierEntry', eventTierEntry);
                                localStorage.setItem('eventTierTitle', eventTierTitle);
                                fillStepValues();
                                    
                                    ;" class="featured-events">
                                        <a href="#" class="event selectable-box">
                                            <!-- 0 -->
                                            <div class="event_head_container ">
                                                <img id='starfish' src="{{ asset('storage/images/'. $category->tierIcon) }}" class="inputEventTierImg event_head">
                                            </div>
                                            <!-- 1 -->
                                            <br>
                                            <!-- 2 -->
                                            <h4 class="inputEventTierTitle" style="text-align: center;">{{ $category->eventTier }}</h4>
                                            <!-- 3 -->
                                            <!-- 4 -->
                                            <div style="display: flex; justify-content: start; align-items: center">
                                                <!-- 4.0 -->
                                                <img style="width: 25px; height: 25px; margin-right: 20px;" src="{{ asset('/assets/images/createEvent/user.png') }}">

                                                <div>
                                                    <span class="inputEventTierPerson">{{ $mappingTierState[$category->eventTier]['person'] }}</span>
                                                    <span>team slots</span>
                                                </div>
                                            </div>
                                            <!-- 5 -->
                                            <br>
                                            <!-- 6 -->
                                            <div style="display: flex; justify-content: start; align-items: center">
                                                <img style="width: 25px; height: 25px; margin-right: 20px;" src="{{ asset('/assets/images/createEvent/trophy.png') }}">
                                                <div>
                                                    <span class="inputEventTierPrize">RM {{ $mappingTierState[$category->eventTier]['prize'] }}</span>
                                                    <span>prize pool</span>
                                                </div>
                                            </div>
                                            <!-- 7 -->
                                            <br>
                                            <!-- 8 -->
                                            <div style="display: flex; justify-content: start; align-items: center">
                                                <img style="width: 25px; height: 25px; margin-right: 20px;" src="{{ asset('/assets/images/createEvent/dollar.png') }}">
                                                <div>
                                                    <span class="inputEventTierEntry">RM {{ $mappingTierState[$category->eventTier]['entry'] }}</span>
                                                    <span>team entry fee</span>
                                                </div>
                                            </div>
                                            <br>
                                        </a>
                                    </section>
                                    @endforeach
                                </div>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-2', 'none')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                    <button onclick="goToNextScreen('step-4', 'timeline-1'); fillStepValues();" type="button" class="oceans-gaming-default-button"> Next > </button>
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
                            <section class="container-border" style="display: flex; justify-content: center;">
                                <img id="outputGameTitleImg" src="{{asset('assets/images/createEvent/exclamation.png')}}" alt="" style="border-radius: 20px; width: 225px;  object-fit: cover; ">
                                <div class="box_3rd" style="max-width: 300px;">
                                    <h4 id="outputEventTypeTitle">League/ Tier</h4>
                                    <p id="outputEventTypeDefinition" style="text-align: left;">Choose your event type...</p>
                                </div>
                                <div class="event_extra">
                                    <div class="event_head_container">
                                        <img id="outputEventTierImg" src="{{asset('assets/images/createEvent/question.png')}}" class="event_head">
                                    </div>
                                    <br>
                                    <h4 id="outputEventTierTitle" style="text-align: center; margin-top: 25px;">Choose a tier</h4>
                                    <div class="event_row">
                                        <div class="icon_container" style="margin-right: 10px;">
                                            <img style="width: 25px; height: 25px;" id="outputEventTierImg" src="{{ asset('assets/images/user.png') }}" class="event_icon">
                                        </div>
                                        <div class="info_container">
                                            <p id="outputEventTierPerson">X</p>
                                            <small>team slots</small>
                                        </div>
                                    </div>
                                    <div class="event_row">
                                        <div class="icon_container" style="margin-right: 10px;">
                                            <img style="width: 25px; height: 25px; margin-right: 20px;" src="{{ asset('/assets/images/createEvent/trophy.png') }}" class="event_icon">
                                        </div>
                                        <div class="info_container">
                                            <p id="outputEventTierPrize">RM Y</p>
                                            <small>prize pool</small>
                                        </div>
                                    </div>
                                    <div class="event_row">
                                        <div class="icon_container" style="margin-right: 10px;">
                                            <img style="width: 25px; height: 25px;" src="{{ asset('assets/images/dollar.png') }}" class="event_icon">
                                        </div>
                                        <div class="info_container">
                                            <p id="outputEventTierEntry">RM Z</p>
                                            <small>team entry fee</small>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <div class="flexbox box-width">
                                <button onclick="goToNextScreen('step-3', 'timeline-1'); fillStepValues();" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                <button onclick="goToNextScreen('step-5', 'timeline-2');" type="button" class="oceans-gaming-default-button"> Step 2 > </button>
                            </div>
                        </div>

                        <div class="text-center d-none create" id="step-5">
                            <div class="welcome text-center" style="margin-bottom: 0px !important;">
                                <u>
                                    <h5>
                                        STEP 2: Fill in your Event Details
                                    </h5>
                                </u>
                                <p>
                                    First, when is your event happening?
                                </p>
                                <div class="event-details-form" style="width: 100% !important; margin: 0px auto;">
                                    <div>
                                        <div class="form-group">
                                            <label for="startDate">Date of Event</label>
                                            <p class="description">Tell your players when to mark their calendars</p>
                                            <div class="container">
                                                <div class="box">
                                                    <p class="description"><b>Start</b></p>
                                                    <input type="date" id="startDate" onchange="checkValidTime();" name="startDate" placeholder=" Select a start date" required>
                                                </div>
                                                <div class="box">
                                                    <p class="description"><b>End</b></p>
                                                    <input type="date" id="endDate" onchange="checkValidTime();" name="endDate" placeholder=" Select an end date" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="startTime">Time of Event</label>
                                            <p class="description">So that your players can set their alarms</p>
                                            <div class="container">
                                                <div class="box">
                                                    <p class="description"><b>Start</b></p>
                                                    <input type="time" id="startTime" onchange="checkValidTime();" name="startTime" placeholder=" Select a start time" required>
                                                </div>
                                                <div class="box">
                                                    <p class="description"><b>End</b></p>
                                                    <input type="time" id="endTime" name="endTime" onchange="checkValidTime();" placeholder=" Select an end time" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-4', 'timeline-1')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                    <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
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
                            <div class="event-details-form box-width">
                                <div class="form-group">
                                    <label for="eventName">Name of Event</label>
                                    <p class="description">Pick a good name for your event (max. 60 characters)</p>
                                    <input type="text" id="eventName" name="eventName" placeholder=" Name of Event" required class="@error('title') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="flexbox box-width">
                                <button onclick="goToNextScreen('step-5', 'timeline-2')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                <button onclick="goToNextScreen('step-7', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
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
                            <div class="event-details-form box-width">
                                <div class="form-group">
                                    <label for="eventDescription">Event Description</label>
                                    <p class="description">So, tell us a little bit about your event (max. 3,000 characters)</p>
                                    <textarea id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event" required></textarea>
                                </div>
                            </div>
                            <div class="flexbox box-width">
                                <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                <button onclick="goToNextScreen('step-8', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
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
                            <div class="event-details-form box-width">
                                <div class="form-group">
                                    <label for="eventTags">Event Tags</label>
                                    <p class="description">Add some relevant keywords to help players find your event more easily</p>
                                    <div class="box">
                                        <input type="text" id="eventTags" name="eventTags" placeholder="Add tags" required>
                                    </div>
                                </div>
                            </div>
                            <div class="flexbox box-width">
                                <button onclick="goToNextScreen('step-7', 'timeline-2')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
                            </div>
                        </div>

                        <div class="text-center create d-none" id="step-9">
                            <div class="welcome text-center" style="margin-bottom: 0px !important; padding-top: 10px !important; padding-bottom: 0px !important;">
                                <u>
                                    <h5>
                                        STEP 2: Fill in your Event Details
                                    </h5>
                                </u>
                                <p>
                                    Finally, some visual aid!
                                </p>

                                <div class="event-details-form box-width">
                                    <div class="form-group">
                                        <label for="eventBanner">Event Banner</label>
                                        <p class="description">A distinctive banner will help your event stand out (resolution TBD).</p>
                                        <div class="banner-upload">
                                            <input onchange="handleFile('eventBanner', 'previewImage');" type="file" id="eventBanner" name="eventBanner" accept="image/*" required>
                                            <div class="banner-preview">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                    <polyline points="21 15 16 10 5 21"></polyline>
                                                </svg>
                                            </div>
                                            Supported files: JPEG, JPG, PNG<br>
                                            <label class="upload-button" for="eventBanner">Upload Image</label>
                                            <br>
                                        </div>
                                        <img class="d-none banner-preview" id="previewImage" alt="Preview" style="max-width: 200px; max-height: 200px;">

                                    </div>
                                </div>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-8', 'timeline-2')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                    <button onclick="goToNextScreen('step-10', 'timeline-3')" type="button" class="oceans-gaming-default-button"> Step 3 > </button>
                                </div>
                            </div>
                        </div>

                        <div class="text-center d-none" id="step-10">
                            <div class="welcome" style="margin-bottom: -20px;">
                                <u>
                                    <h5>
                                        STEP 3: Complete the Payment
                                    </h5>
                                </u>
                                <br>
                                <div class="payment-summary">
                                    <h5>Payment Summary </h5>
                                    <div>Event Categories</div>
                                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Type: <span id="paymentType"> </span></div>
                                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Tier: <span id="paymentTier"> </span></div>
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
                                        <button onclick="" type="button" class="choose-payment-method" data-bs-toggle="modal" data-bs-target="#payment-modal">
                                            Choose a payment method
                                        </button>
                                        <button onclick="goToNextScreen('step-1', 'timeline-1');" type="button" class="choose-payment-method-condition-fulfilled" style="background-color: red;">
                                            Choose event tier and title first
                                        </button>
                                    </div>
                                </div>
                                <br>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                    <button onclick="goToNextScreen('step-11', 'timeline-4')" type="button" class="oceans-gaming-default-button"> Step 4 > </button>
                                </div>
                            </div>

                        </div>

                        <div class="text-center d-none" id="step-11">
                            <div class="welcome" style="margin-bottom: 0px;">
                                <u>
                                    <h5>
                                        STEP 4: Launch your event
                                    </h5>
                                </u>
                            </div>
                            <br>
                            <div class="payment-summary">

                                <input onchange="toggleRadio(this, 'public')" required type="radio" id="public" name="launch_visible" value="public">
                                <label for="public"><u>Public</u></label><br>
                                <div class="radio-indent public">
                                    <p>Everyone can see and join your event</p>
                                </div>
                                <div class="radio-indent-hidden public d-none">
                                    <input type="radio" id="sub_action_public" name="launch_schedule" value="now">
                                    <label for="sub_action_public"><u>Launch now</u></label><br>
                                    <input type="radio" id="sub_action_public" name="launch_schedule" value="schedule">
                                    <label for="sub_action_public"><u>Schedule launch</u></label><br>
                                    <div class="container">
                                        <div class="box">
                                            <input type="date" id="sub_action_public_date" name="launch_date">
                                        </div>
                                        <div class="box">
                                            <input type="time" id="sub_action_public_time" name="launch_time">
                                        </div>
                                    </div>
                                </div>

                                <input onchange="toggleRadio(this, 'private')" required type="radio" id="private" name="launch_visible" value="public">
                                <label for="private"><u>Private</u></label><br>
                                <div class="radio-indent private">
                                    <p>Only players you invite can see and join your event</p>
                                </div>
                                <div class="radio-indent-hidden private d-none">
                                    <input type="radio" id="sub_action_private" name="launch_schedule" value="now">
                                    <label for="sub_action_private"><u>Launch now</u></label><br>
                                    <input type="radio" id="sub_action_private" name="launch_schedule" value="schedule">
                                    <label for="sub_action_private"><u>Schedule launch</u></label><br>
                                    <div class="container">
                                        <div class="box">
                                            <input type="date" id="sub_action_private" name="launch_date">
                                        </div>
                                        <div class="box">
                                            <input type="time" id="sub_action_private" name="launch_time">
                                        </div>
                                    </div>
                                </div>

                                <input checked onchange="toggleRadio(this, 'draft')" type="radio" id="draft" name="launch_visible" required value="draft">
                                <label for="draft"><u>Save as draft</u></label>
                                <div class="radio-indent draft">
                                    <p>Save your event and edit it later</p>
                                </div>

                            </div>
                            <br>
                            <div class="text-center">
                                <button class="oceans-gaming-default-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    &nbsp;&nbsp;
                                    <u>Preview your event page</u>
                                </button>
                            </div>
                            <br>
                            <div class="flexbox box-width">
                                <button onclick="goToNextScreen('step-10', 'timeline-3')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
                                <button onclick="saveEvent()" type="button" type="button" class="oceans-gaming-default-button"> Step 4 > </button>
                            </div>
                            <br>

                        </div>

                        <div class="text-center d-none" id="step-12">
                            <div class="welcome">
                                <u>
                                    <h5>
                                        STEP 4: Launch your event
                                    </h5>
                                </u>
                            </div>
                            <div class="payment-summary" style="margin-top: -25px; text-align: center">
                                <h5>Launch Event Now?</h5>
                                <p>You are about to launch your your event to the world.</p>
                                <p>Once your event is live, you will no longer be able to make any changes to it, and it will appear to players as it is.</p>
                                <p>Are your sure you want to launch your event now?</p>
                                <br>
                                <div class="flexbox box-width">
                                    <button onclick="goToNextScreen('step-13', 'timeline-4')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Cancel </button>
                                    <button onclick="saveEvent()" type="button" class="oceans-gaming-default-button"> Yes, I'm sure </button>
                                </div>
                                <br>
                            </div>
                            <br>
                        </div>
                        <div class="text-center d-none" id="step-13">
                            <div class="welcome">
                                <u>
                                    <h3 id="heading"></h3>
                                </u>
                            </div>
                            <div class="box-width">
                                <p id="notification"></p>
                            </div>
                            <br><br>
                            <input onclick="goToNextScreen('', '')" value="Continue">
                        </div>

                    </form>
                </div>

                <div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-div" style="display: flex !important; justify-content: space-between !important;">
                                <h5 class="modal-title" id="payment-modal-label"> &nbsp; &nbsp;Payment method</h5>
                                <button type="button" class="btn-close" id="modal-close" data-bs-dismiss="modal" aria-label="Close">&nbsp;X&nbsp;</button>
                            </div>
                            <div class="modal-body">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h4>Make A Payment</h4>
                                        @if (session()->has('success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('success') }}
                                        </div>
                                        @endif
                                        <form id="card-form">
                                            @csrf
                                            <div class="form-group form-group2">
                                                <label for="card-name" class="">Your name</label>
                                                <input type="text" name="name" id="card-name" class="">
                                            </div>
                                            <div class="form-group form-group2">
                                                <label for="email" class="">Email</label>
                                                <input type="email" name="email" id="email" class="">
                                            </div>
                                            <div class="form-group form-group2">
                                                <label for="card" class="">Card details</label>

                                                <div class="form-group form-group2">
                                                    <div id="card"></div>
                                                </div>
                                            </div>
                                            <button type="submit" class="oceans-gaming-default-button">Pay 👉</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button" data-bs-dismiss="modal"> Back </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
        </main>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
        <script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
        <!-- Including the Tagify library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.min.js"></script>
        <script>
            // Initializing Tagify on the input field
            new Tagify(document.querySelector('#eventTags'), {});
        </script>
        <script>
            window.onload = function() {
                let $event = {
                    {
                        Js::from($event)
                    }
                };
                console.log({
                    $event
                })
                console.log({
                    $event
                })
                console.log({
                    $event
                })
                console.log({
                    $event
                })
                console.log({
                    $event
                })
                console.log({
                    $event
                })
                let isCreateEventView = $event == null;
                if (isCreateEventView) {
                    ['eventTypeTitle', 'gameTitleImg', 'eventTierPrize', 'eventTierPerson',
                        'eventTierTitle', 'eventTierEntry', 'eventTypeDefinition', 'eventTierImg'
                    ].forEach((key) => {
                        localStorage.removeItem(key);
                    });
                }
                else{
                    localStorage.setItem('eventTypeTitle', $event.eventTypeTitle);
                    localStorage.setItem('gameTitleImg', $event.gameTitleImg);
                    localStorage.setItem('eventTierPrize', $event.eventTierPrize);
                    localStorage.setItem('eventTierPerson', $event.eventTierPerson);
                    localStorage.setItem('eventTierTitle', $event.eventTierTitle);
                }
            }
            $(document).on("keydown", ":input:not(textarea)", function(event) {
                if (event.key == "Enter") {
                    event.preventDefault();
                }
            });

            function checkStringNullOrEmptyAndReturn(value) {
                let _value = String(value).trim();
                return (value === null || value === undefined || _value === "") ? null : _value;
            }

            function fillStepValues() {
                let formValues = getFormValues(['eventTier', 'eventType', 'gameTitle']);
                if (
                    // do later this way
                    'eventTier' in formValues &&
                    'gameTitle' in formValues &&
                    'eventType' in formValues
                ) {
                    let eventTier = formValues['eventTier'];
                    let eventType = formValues['eventType'];
                    let gameTitle = formValues['gameTitle'];

                    console.log({
                        eventTier
                    })
                    console.log({
                        eventType
                    })
                    console.log({
                        gameTitle
                    })

                    // let inputEGameTitleImg = document.querySelector(`img#inputGameTitle${gameTitle}Img`);
                    let outputGameTitleImg = document.querySelector('img#outputGameTitleImg');
                    let outputGameTitleImgSrc = localStorage.getItem('gameTitleImg');
                    console.log({
                        outputGameTitleImgSrc
                    })
                    console.log({
                        outputGameTitleImgSrc
                    })
                    console.log({
                        outputGameTitleImgSrc
                    })
                    let second = checkStringNullOrEmptyAndReturn(outputGameTitleImgSrc);
                    console.log({
                        second
                    })
                    console.log({
                        second
                    })
                    console.log({
                        second
                    })
                    if (outputGameTitleImgSrc != null) {
                        console.log({
                            outputGameTitleImgSrc
                        })
                        console.log({
                            outputGameTitleImgSrc
                        })
                        console.log({
                            outputGameTitleImgSrc
                        })
                        outputGameTitleImg.src = outputGameTitleImgSrc;
                    }
                    let outputEventTypeTitle = document.getElementById('outputEventTypeTitle');
                    let outputEventTypeDefinition = document.getElementById('outputEventTypeDefinition');

                    let outputEventTypeTitleInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTypeTitle'));
                    if (outputEventTypeTitleInnerHTML != null)
                        outputEventTypeTitle.innerHTML = outputEventTypeTitleInnerHTML;

                    let outputEventTypeDefinitionInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTypeDefinition'));
                    if (outputEventTypeDefinitionInnerHTML != null)
                        outputEventTypeDefinition.innerHTML = outputEventTypeDefinitionInnerHTML;

                    let outputEventTierImg = document.querySelector(`img#outputEventTierImg`);
                    let outputEventTierTitle = document.getElementById('outputEventTierTitle');
                    let outputEventTierPerson = document.getElementById('outputEventTierPerson');
                    let outputEventTierPrize = document.getElementById('outputEventTierPrize');
                    let outputEventTierEntry = document.getElementById('outputEventTierEntry');

                    let outputEventTierImgSrc = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierImg'));
                    if (outputEventTierImgSrc != null)
                        outputEventTierImg.src = outputEventTierImgSrc;

                    let outputEventTierPersonInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierPerson'));
                    if (outputEventTierPersonInnerHTML != null)
                        outputEventTierPerson.innerHTML = outputEventTierPersonInnerHTML;

                    let outputEventTierPrizeInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierPrize'));
                    if (outputEventTierPrizeInnerHTML != null)
                        outputEventTierPrize.innerHTML = outputEventTierPrizeInnerHTML;

                    let outputEventTierEntryInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierEntry'));
                    if (outputEventTierEntryInnerHTML != null)
                        outputEventTierEntry.innerHTML = outputEventTierEntryInnerHTML;

                    let outputEventTierTitleInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierTitle'));
                    if (outputEventTierEntryInnerHTML != null)
                        outputEventTierTitle.innerHTML = outputEventTierTitleInnerHTML;

                }
            }

            function checkValidTime() {
                var startDateInput = document.getElementById('startDate');
                var endDateInput = document.getElementById('endDate');
                var startTimeInput = document.getElementById('startTime');
                var endTimeInput = document.getElementById('endTime');
                const startDateInputValue = startDateInput.value;
                const endDateInputValue = endDateInput.value;
                const startTimeInputValue = startTimeInput.value;
                const endTimeInputValue = endTimeInput.value;
                var now = new Date();
                var startDate = new Date(startDateInputValue + " " + startTimeInput.value);
                var endDate = new Date(endDateInput.value + " " + endTimeInput.value);
                if (startDate < now || endDate <= now) {
                    Toast.fire({
                        icon: 'error',
                        text: "Start date or end date cannot be earlier than current time."
                    });
                    if (startDate < now) {
                        startDateInput.value = ""
                    } else if (endDate < now) {
                        endDateInput.value = ""
                    }
                }
                if (startTimeInput.value === "" || endTimeInput.value === "") {
                    return;
                }
                if (endDate < startDate) {
                    Toast.fire({
                        icon: 'error',
                        text: "End  and time cannot be earlier than start date and time."
                    });
                    startDateInput.value = "";
                    startTimeInput.value = "";
                }
            }

            function handleFile(inputFileId, previewImageId) {
                var selectedFile = document.getElementById(inputFileId).files[0];
                console.log({
                    selectedFile
                })
                console.log({
                    selectedFile
                })
                console.log({
                    selectedFile
                })
                console.log({
                    selectedFile
                })
                console.log({
                    selectedFile
                })
                var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                if (!allowedTypes.includes(selectedFile.type)) {
                    selectedFile.value = '';
                    Toast.fire({
                        icon: 'error',
                        text: "Invalid file type. Please upload a JPEG, PNG, or JPG file."
                    })
                } else previewSelectedImage('eventBanner', 'previewImage');
            }
        </script>
        <script>
            let stripe = Stripe('{{ env("STRIPE_KEY") }}')
            // const appearance = {
            //     theme: 'stripe',

            //     variables: {
            //         colorPrimary: '#0570de',
            //         colorBackground: '#ffffff',
            //         colorText: '#30313d',
            //         colorDanger: '#df1b41',
            //         fontFamily: 'Ideal Sans, system-ui, sans-serif',
            //         spacingUnit: '2px',
            //         borderRadius: '4px',
            //         border: "10px solid black"
            //         // See all possible variables below
            //     }
            // };
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px'
                    }
                },
                hidePostalCode: true
            })
            const cardForm = document.getElementById('card-form')
            const cardName = document.getElementById('card-name')
            cardElement.mount('#card')
            cardForm.addEventListener('submit', async (e) => {
                e.preventDefault()
                const {
                    paymentMethod,
                    error
                } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: cardName.value
                    }
                })
                if (error) {
                    console.log(error)
                } else {
                    let input = document.createElement('input')
                    input.setAttribute('type', 'hidden')
                    input.setAttribute('name', 'payment_method')
                    input.setAttribute('value', paymentMethod.id)
                    cardForm.appendChild(input)
                    // payment method created
                    setFormValues({
                        'isPaymentDone': true,
                        paymentMethod: paymentMethod.id
                    });
                    goToNextScreen('step-11', 'timeline-4');
                    document.getElementById('modal-close').click();
                    const form = new FormData(cardForm);
                    const data = {};
                    form.forEach((value, key) => {
                        data[key] = value;
                    });
                    fetch("{{ route('stripe.organizerTeamPay') }}", {
                            method: "POST",
                            divs: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(responseData => {

                        })
                        .catch(error => {
                            // Handle errors here
                            console.error(error);
                        })
                }
            })
        </script>


        <!-- Including the Tagify library -->

        <script>
            // Initializing Tagify on the input field
            new Tagify(document.querySelector('#eventTags'), {});

            function selectOption(element, label, imageUrl) {
                // Add the selected class to the parent button
                const dropdownButton = element.closest('.dropdown').querySelector('.dropbtn');
                dropdownButton.classList.add('selected');

                // Handle selection logic here
                const selectedLabel = dropdownButton.querySelector('.selected-label');
                const selectedImage = dropdownButton.querySelector('.selected-image img');
                selectedLabel.textContent = label;
                selectedImage.src = imageUrl;

                // Close the dropdown
                closeDropDown(dropdownButton);
            }

            // // Function to close the dropdown
            // function closeDropDown(button) {
            //     const dropdownContent = button.nextElementSibling;
            //     dropdownContent.classList.remove('d-block');
            // }

            // // Function to open the dropdown
            // function openDropDown(button) {
            //     const dropdownContent = button.nextElementSibling;
            //     dropdownContent.classList.add('d-block');
            // }
        </script>


    </body>