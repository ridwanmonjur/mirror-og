@include('Organizer.Layout.CreateEventHeadTag')

<body>
    <nav class="navbar">
        <div class="logo">
            <img width="160px" height="60px" src="{{ asset('/assets/images/createEvent/logo-default.png') }}" alt="">
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu menu-toggle" onclick="toggleNavbar()">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
        <div class="search-bar d-none-at-mobile">
            <input type="text" name="search" id="search" placeholder="Search for events">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <div class="nav-buttons">
            <button class="oceans-gaming-default-button oceans-gaming-gray-button"> Where is moop? </button>
            <img width="50px" height="40px" src="{{ asset('/assets/images/createEvent/navbar-account.png') }}" alt="">
            <img width="70px" height="40px" src="{{ asset('/assets/images/createEvent/navbar-crown.png') }}" alt="">
        </div>
    </nav>
    <nav class="mobile-navbar d-centered-at-mobile d-none">
        <div class="search-bar search-bar-mobile ">
            <input type="text" name="search" id="search" placeholder="Search for events">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search" style="left: 40px;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <div class="nav-buttons search-bar-mobile d-centered-at-mobile">
            <img width="50px" height="40px" src="{{ asset('/assets/images/createEvent/navbar-account.png') }}" alt="">
            <img width="70px" height="40px" src="{{ asset('/assets/images/createEvent/navbar-crown.png') }}" alt="">

        </div>
    </nav>
    <main>
        <section class="time-line-box">
            <div class="swiper-container text-center">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1" onclick="goToNextScreen('step-1', 'timeline-1')">
                        <div class="timestamp"><span>Categories</span></div>
                        <div class="status__left"><span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2" onclick="goToNextScreen('step-2', 'timeline-2')">
                        <div class="timestamp"><span>Details</span></div>
                        <div class="status"><span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-3" onclick="goToNextScreen('step-3', 'timeline-3')">
                        <div class="timestamp"><span>Payment</span></div>
                        <div class="status"><span><small></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-4" onclick="goToNextScreen('step-4', 'timeline-4')">
                        <div class="timestamp"><span class="date">Launch</span></div>
                        <div class="status__right"><span><small></small></span></div>
                    </div>
                </div>
            </div>

            <div class="text-center" id="step-0">
                <header class="welcome">
                    <u>
                        <h2>
                            Welcome to Splash's Event Creator
                        </h2>
                    </u>
                    <br><br><br>
                    <p class="create-online-esports">
                        Create online esports events all on your own, right here on Splash, in just 4 steps.
                    </p>
                </header>
                <input type="submit" onclick="goToNextScreen('step-1', 'timeline-1')" value="Continue">
            </div>

            <div id="step-1" class="d-none">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            Step 1:
                        </h5>
                    </u>
                    <u>
                        <h3>
                            Welcome to Splash's Event Creator
                        </h3>
                    </u>
                </header>
                <div class="dropdown-container">
                    <div class="dropdown">
                        <button class="dropbtn" onclick="openDropDown(this)">
                            Select Game Title
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        @foreach ($eventCategory as $eventc)
                        <div class="dropdown-content d-none">
                            <div class="drop">
                                <img src="{{ asset('storage/'. $eventc->gameIcon) }}" alt="" height="30px" width="50px">
                                <a href="#">{{ $eventc->gameTitle}}</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="dropdown">
                        <button class="dropbtn" onclick="openDropDown(this)">
                            Select Event Type
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        @foreach ($eventCategory as $eventc)
                        <div class="dropdown-content d-none">
                            <a href="#">{{ $eventc->eventType}}</a>
                        </div>
                        @endforeach
                    </div>
                    <div class="dropdown">
                        <button class="dropbtn" onclick="openDropDown(this)">
                            Select Event Tier
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        @foreach ($eventCategory as $eventc)
                        <div class="dropdown-content d-none">
                            <div class="drop">
                                <img src="{{ asset('storage/'. $eventc->tierIcon) }}" alt="" height="40px" width="40px">

                                <a href="#">{{$eventc->eventTier}}</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <br><br><br><br><br><br><br><br>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-0', 'none')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-2', 'timeline-2')" type="submit" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div class="text-center d-none create" id="step-2" height="300vh">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            Step 2:
                        </h5>
                    </u>
                    <u>
                        <h3>
                            Fill in your Event Details
                        </h3>
                    </u>
                </header>
                <div class="event-details-form">
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label for="eventName">Name of Event</label>
                            <p class="description">Pick a good name for your event (max. 60 characters)</p>
                            <input type="text" id="eventName" name="eventName" placeholder=" Name of Event" required>
                        </div>

                        <div class="form-group">
                            <label for="startDate">Date of Event</label>
                            <p class="description">Tell your players when to mark their calendars</p>
                            <div class="container">
                                <div class="box">
                                    <p class="description"><b>Start</b></p>
                                    <input type="date" id="startDate" name="startDate" placeholder=" Select a start date" required>
                                </div>
                                <div class="box">
                                    <p class="description"><b>End</b></p>
                                    <input type="date" id="endDate" name="endDate" placeholder=" Select an end date" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="startTime">Time of Event</label>
                            <p class="description">So that your players can set their alarms</p>
                            <div class="container">
                                <div class="box">
                                    <p class="description"><b>Start</b></p>
                                    <input type="time" id="startTime" name="startTime" placeholder=" Select a start time" required>
                                </div>
                                <div class="box">
                                    <p class="description"><b>End</b></p>
                                    <input type="time" id="endTime" name="endTime" placeholder=" Select an end time" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventDescription">Event Description</label>
                            <p class="description">So, tell us a little bit about your event (max. 3, 000 characters)</p>
                            <textarea id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="eventBanner">Event Banner</label>
                            <p class="description">How about some visual aid for your event? (resolution to be decided)</p>
                            <div class="banner-upload">
                                <input type="file" id="eventBanner" name="eventBanner" accept="image/*" required>
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
                        </div>

                        <div class="form-group">
                            <label for="eventTags">Event Tags</label>
                            <p class="description">Add some relevant keywords to help players find your event more easily</p>
                            <div class="box">
                                <input type="text" id="eventTags" name="eventTags" placeholder="Add tags" required>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-1', 'timeline-1')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-3', 'timeline-3')" type="submit" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div class="text-center d-none" id="step-3">
                <header class="welcome">
                    <u>
                        <h5>
                            Step 3:
                        </h5>
                    </u>
                    <u>
                        <h3>
                            Complete the Payment
                        </h3>
                    </u>
                    <!-- http://127.0.0.1:5500/event_creation.html?action=public&sub_action_public=launch_now&sub_action_public_date=&sub_action_public_time=&sub_action_private_date=&sub_action_private_time=# -->
                </header>
                <div class="payment-summary" style="margin-top: -30px;">
                    <h5>Payment Summary </h5>
                    <div>Event Creation</div>
                    <br>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Type: League</div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Tier: Dolphin</div>
                    <br>
                    <div class="flexbox">
                        <span>TOTAL</span>
                        <span>RM 5, 000.00</span>
                    </div>
                    <br>
                    <div class="text-center">
                        <input type="submit" class="choose-payment-method" value="Choose a payment method">
                    </div>
                    <br>
                    <div class="text-center">
                        <button class="oceans-gaming-default-button oceans-gaming-green-button"> <u>Payment successful</u></button>
                    </div>
                </div>
                <br>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-2', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-4', 'timeline-4')" type="submit" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>


            <div class="text-center d-none" id="step-4">
                <header class="welcome">
                    <u>
                            <h5>
                                Step 4:
                            </h5>
                        </u>
                    <u>
                            <h3>
                                Launch your Event
                            </h3>
                        </u>
                </header>
                <div class="payment-summary" style="margin-top: -30px;">

                    <input onchange="toggleRadio(this, 'public')" required type="radio" id="public" name="action" value="public">
                    <label for="public"><u>Public</u></label><br>
                    <div class="radio-indent public">
                        <p>Everyone can see and join your event</p>
                    </div>
                    <div class="radio-indent-hidden public d-none">
                        <input type="radio" id="sub_action_public" name="sub_action_public" value="launch_now">
                        <label for="sub_action_public"><u>Launch now</u></label><br>
                        <input type="radio" id="sub_action_public" name="sub_action_public" value="launch_schedule">
                        <label for="sub_action_public"><u>Schedule launch</u></label><br>
                        <input type="date" id="sub_action_public_date" name="sub_action_public_date">
                        <input type="time" id="sub_action_public_time" name="sub_action_public_time">
                    </div>

                    <input onchange="toggleRadio(this, 'private')" required type="radio" id="private" name="action" value="private">
                    <label for="private"><u>Private</u></label><br>
                    <div class="radio-indent private">
                        <p>Only players you invite can see and join your event</p>
                    </div>
                    <div class="radio-indent-hidden private d-none">
                        <input type="radio" id="sub_action_private" name="sub_action_private" value="launch_now">
                        <label for="sub_action_private"><u>Launch now</u></label><br>
                        <input type="radio" id="sub_action_private" name="sub_action_private" value="launch_schedule">
                        <label for="sub_action_private"><u>Schedule launch</u></label><br>
                        <input type="date" id="sub_action_private_date" name="sub_action_private_date">
                        <input type="time" id="sub_action_private_time" name="sub_action_private_time">
                    </div>

                    <input onchange="toggleRadio(this, 'draft')" type="radio" id="draft" name="action" required value="draft">
                    <label for="draft"><u>Save as draft</u></label>

                </div>
                <br><br>
                <div class="text-center">
                    <button class="oceans-gaming-default-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-eye">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            &nbsp;&nbsp;
                            <u>Preview your event page</u>

                        </button>
                </div>
                <br>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-3', 'timeline-3')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="addEvent(this)" type="submit" class="oceans-gaming-default-button"> Launch > </button>
                </div>
            </div>

            <div id="feedback" class="text-center d-none">
                <header class="welcome">
                    <u>
                        <h3 id="heading"> </h3>
                    </u>
                </header>
                <div class="box-width">
                    <p id="notification"></p>
                    <small id="description"></small>
                </div>
            </div>

        </section>
    </main>

    <script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
    <script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
    <!-- Including the Tagify library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.min.js"></script>

    <script>
        // Initializing Tagify on the input field
        new Tagify(document.querySelector('#eventTags'), {});
    </script>


</body>
