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
        <form action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
            @csrf
        <section class="time-line-box">
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
                <input type="button" onclick="goToNextScreen('step-1', 'timeline-1')" value="Continue">
            </div>

            <div id="step-1" class="d-none">
                <header class="welcome text-center">
                    <u>
                        <h3>
                            STEP 1: Choose your Event Categories
                        </h3>
                    </u>
                    <br>
                    <p>First, select an esport title</p>
                    <br>
                    <div class="image-scroll-container">
                        @foreach ($eventCategory as $eventc)
                        <div class="scroll-images">
                            <a href=""><img class="selectable-image focused" src="{{ asset('storage/'. $eventc->gameIcon) }}" alt="" style="border-radius: 20px; height: 330px; width: 220px;"></a>
                            <h5 style="padding-top: 10px;">{{ $eventc->gameTitle}}</h5>
                        </div>
                        @endforeach
                        <!-- Add more images and titles here -->
                    </div>
                </header>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-0', 'none')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div id="step-2" class="d-none">
                <header class="welcome text-center">
                    <u>
                        <h3>
                            STEP 1: Choose your Event Categories
                        </h3>
                    </u>
                    <br>
                    <p>Then, select what kind of event you want to run.</p>
                </header>
                @foreach ($eventCategory as $eventc)
                <div class="cont">
                    <a href="" class="box_2nd selectable-box">
                        <h2 class="box-title">{{ $eventc->eventType}}</h2>
                        <p class="box-text" style="text-align: left;">Insert definition here</p>
                    </a>
                </div>
                @endforeach
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-1', 'none')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-3', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>

            </div>

            <div id="step-3" class="d-none">
                <header class="welcome text-center">
                    <u>
                        <h3>
                            STEP 1: Choose your Event Categories
                        </h3>
                    </u>
                    <br>
                    <p>Finally, choose a tier for your event.</p>
                </header>
                @foreach ($eventCategory as $eventc)
                <section class="featured-events">
                    <a href="" class="event selectable-box">
                        <div class="event_head_container">
                            <img id='starfish' src="{{ asset('storage/'. $eventc->tierIcon) }}" class="event_head">
                        </div>
                        <br>
                        <h4 style="text-align: center;">Starfish</h4>
                        <div class="event_row">
                            <div class="icon_container">
                                <img src="{{ asset('/assets/images/createEvent/user.png') }}" class="event_icon">

                            </div>
                            <div class="info_container">
                                <p>16</p>
                                <small>team slots</small>
                            </div>
                        </div>
                        <div class="event_row">
                            <div class="icon_container">
                                <img src="{{ asset('/assets/images/createEvent/trophy.png') }}" class="event_icon">
                            </div>
                            <div class="info_container">
                                <p>RM 5000</p>
                                <small>prize pool</small>
                            </div>
                        </div>
                        <div class="event_row">
                            <div class="icon_container">
                                <img src="{{ asset('/assets/images/createEvent/dollar.png') }}" class="event_icon">
                            </div>
                            <div class="info_container">
                                <p>RM 20</p>
                                <small>team entry fee</small>
                            </div>
                        </div>
                    </a>
                   @endforeach
                </section>

                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-2', 'none')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-4', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div id="step-4" class="d-none">
                <header class="welcome text-center">
                    <u>
                        <h3>
                            STEP 1: Choose your Event Categories
                        </h3>
                    </u>
                    <br>
                    <p>Here are the categories you've chosen for your event.</p>
                </header>
                <section class="cont">
                    <img src="css/images/dotaPoster.jpg" alt="" style="border-radius: 20px; width: 230px; height: 350px;">
                    <div class="box_3rd">
                        <h2>League</h2>
                        <p style="text-align: left;">Insert definition here</p>
                    </div>
                    <div class="event_extra">
                        <div class="event_head_container">
                            <img id="dolphin" src="css/images/logo/6.png" class="event_head">
                        </div>
                        <br><br>
                        <h4 style="text-align: center;">Starfish</h4>
                        <div class="event_row">
                            <div class="icon_container">
                                <img src="css/images/user.png" class="event_icon">
                            </div>
                            <div class="info_container">
                                <p>64</p>
                                <small>team slots</small>
                            </div>
                        </div>
                        <div class="event_row">
                            <div class="icon_container">
                                <img src="css/images/trophy.png" class="event_icon">
                            </div>
                            <div class="info_container">
                                <p>RM 15000</p>
                                <small>prize pool</small>
                            </div>
                        </div>
                        <div class="event_row">
                            <div class="icon_container">
                                <img src="css/images/dollar.png" class="event_icon">
                            </div>
                            <div class="info_container">
                                <p>RM 100</p>
                                <small>team entry fee</small>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-3', 'none')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-5', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Step 2 > </button>
                </div>
            </div>

            <div class="text-center d-none create" id="step-5">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            STEP 2: Fill in your Event Details
                        </h5>
                    </u>
                    <p>
                        First, when is your event happening?
                    </p>
                </header>
                <div class="event-details-form">
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
                </div>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-4', 'timeline-1')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-6', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div class="text-center d-none create" id="step-6">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            STEP 2: Fill in your Event Details
                        </h5>
                    </u>
                    <p>
                        Don't forget to name your event!
                    </p>
                </header>
                <div class="event-details-form">
                        <div class="form-group">
                            <label for="eventName">Name of Event</label>
                            <p class="description">Pick a good name for your event (max. 60 characters)</p>
                            <input type="text" id="eventName" name="eventName" placeholder=" Name of Event" required>
                        </div>
                </div>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-5', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-7', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div class="text-center d-none create" id="step-7">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            STEP 2: Fill in your Event Details
                        </h5>
                    </u>
                    <p>
                        Next, what's your event about?
                    </p>
                </header>
                <div class="event-details-form">
                        <div class="form-group">
                            <label for="eventDescription">Event Description</label>
                            <p class="description">So, tell us a little bit about your event (max. 3,000 characters)</p>
                            <textarea id="eventDescription" name="eventDescription" rows="4" placeholder=" Description for event" required></textarea>
                        </div>
                </div>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-6', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-8', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div class="text-center d-none create" id="step-8">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            STEP 2: Fill in your Event Details
                        </h5>
                    </u>
                    <p>
                        Keywords wil help players find your event!
                    </p>
                </header>
                <div class="event-details-form">
                        <div class="form-group">
                            <label for="eventTags">Event Tags</label>
                            <p class="description">Add some relevant keywords to help players find your event more easily</p>
                            <div class="box">
                                <input type="text" id="eventTags" name="eventTags" placeholder="Add tags" required>
                            </div>
                        </div>
                </div>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-7', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-9', 'timeline-2')" type="button" class="oceans-gaming-default-button"> Next > </button>
                </div>
            </div>

            <div class="text-center d-none create" id="step-9">
                <header class="welcome text-center">
                    <u>
                        <h5>
                            STEP 2: Fill in your Event Details
                        </h5>
                    </u>
                    <p>
                        Finally, some visual aid!
                    </p>
                </header>
                <div class="event-details-form">
                        <div class="form-group">
                            <label for="eventBanner">Event Banner</label>
                            <p class="description">A distinctive banner will help your event stand out (resolution TBD).</p>
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
                </div>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-8', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-10', 'timeline-3')" type="button" class="oceans-gaming-default-button"> Step 3 > </button>
                </div>
            </div>

            <div class="text-center d-none" id="step-10">
                <header class="welcome">
                    <u>
                        <h5>
                            STEP 3: Complete the Payment
                        </h5>
                    </u>
                </header>
                <div class="payment-summary" style="margin-top: -30px;">
                    <h5>Payment Summary </h5>
                    <div>Event Categories</div>
                    @foreach ($eventCategory as $eventc)
                    <br>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Title&nbsp;&nbsp;&nbsp;: {{ $eventc->gameTitle}}</div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Type&nbsp;&nbsp;: {{ $eventc->eventType}}</div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;Tier&nbsp;&nbsp;&nbsp;&nbsp;: {{ $eventc->eventTier}}</div>
                    <br>
                    @endforeach
                    <div class="flexbox">
                        <span>Subtotal</span>
                        <span>RM 15, 000.00</span>
                    </div>
                    <div class="flexbox">
                        <span>Event creation fee rate</span>
                        <span>20%</span>
                    </div>
                    <div class="flexbox">
                        <span>Event creation fee total</span>
                        <span>3,000</span>
                    </div>
                    <br>
                    <div class="flexbox">
                        <h5>Total</h5>
                        <h5>RM 18, 000.00</h5>
                    </div>
                    <br>
                    <div class="text-center">
                        <input type="button" class="choose-payment-method" value="Choose a payment method">
                    </div>
                    <br>
                    <!-- <div class="text-center">
                        <button class="oceans-gaming-default-button oceans-gaming-green-button"> <u>Payment successful</u></button>
                    </div> -->
                </div>
                <br>
                <div class="flexbox box-width">
                    <button onclick="goToNextScreen('step-9', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-11', 'timeline-4')" type="button" class="oceans-gaming-default-button"> Step 4 > </button>
                </div>
            </div>

            <div class="text-center d-none" id="step-11">
                <header class="welcome">
                    <u>
                            <h5>
                                STEP 4: Launch your event
                            </h5>
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
                        <div class="container">
                            <div class="box">
                                <input type="date" id="sub_action_public_date" name="sub_action_public_date">
                            </div>
                            <div class="box">
                                <input type="time" id="sub_action_public_time" name="sub_action_public_time">
                            </div>
                        </div>
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
                        <div class="container">
                            <div class="box">
                                <input type="date" id="sub_action_public_date" name="sub_action_public_date">
                            </div>
                            <div class="box">
                                <input type="time" id="sub_action_public_time" name="sub_action_public_time">
                            </div>
                        </div>
                    </div>

                    <input onchange="toggleRadio(this, 'draft')" type="radio" id="draft" name="action" required value="draft">
                    <label for="draft"><u>Save as draft</u></label>
                    <div class="radio-indent draft">
                        <p>Save your event and edit it later</p>
                    </div>

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
                    <button onclick="goToNextScreen('step-10', 'timeline-3')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                    <button onclick="goToNextScreen('step-12', 'timeline-4')" type="submit" class="oceans-gaming-default-button"> Step 4 > </button>
                </div>
            </div>

            <div class="text-center d-none" id="step-12">
                <header class="welcome">
                    <u>
                            <h5>
                                STEP 4: Launch your event
                            </h5>
                        </u>
                </header>
                <div class="payment-summary" style="margin-top: -30px; text-align: center">
                    <h5>Launch Event Now?</h5>
                    <p>You are about to launch your your event to the world.</p>
                    <p>Once your event is live, you will no longer be able to make any changes to it, and it will appear to players as it is.</p>
                    <p>Are your sure you want to launch your event now?</p>
                    <br>
                    <div class="flexbox">
                        <button onclick="goToNextScreen('step-11', 'timeline-4')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Cancel </button>
                        <button onclick="addEvent(this)" type="submit" class="oceans-gaming-default-button"> Yes, I'm sure </button>
                    </div>
                    <br>
                </div>
                <br>
            </div>

            <div id="feedback" class="text-center d-none">
                <header class="welcome">
                    <u>
                        <h3 id="heading"></h3>
                    </u>
                </header>
                <div class="box-width">
                    <p id="notification"></p>
                </div>
                <br><br>
                <input type="submit" onclick="goToNextScreen('', '')" value="Continue">
            </div>

        </section>
        </form>
    </main>
    <script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
    <script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>

    <!-- Including the Tagify library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.min.js"></script>

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

        // Function to close the dropdown
        function closeDropDown(button) {
            const dropdownContent = button.nextElementSibling;
            dropdownContent.classList.remove('d-block');
        }

        // Function to open the dropdown
        function openDropDown(button) {
            const dropdownContent = button.nextElementSibling;
            dropdownContent.classList.add('d-block');
        }
    </script>


</body>
