@include('Organizer.Layout.CreateEventHeadTag')

<body>
    @include('CommonLayout.Navbar')
    <main>
        <form action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
            @csrf
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
                <div class="text-center d-none" id="step-0">
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
                    <button id="submit" type="button" onclick="goToNextScreen('step-1', 'timeline-1')"> Continue </button>
                </div>

                <div id="step-1" class="">
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

                        <input type="hidden" name="gameTitle" id="gameTitle">
                        <input type="hidden" name="eventTier" id="eventTier">
                        <input type="hidden" name="eventType" id="eventType">

                        <div class="dropdown">
                            <button id="dropdownGameTitle" type="button" class="dropbtn" onclick="openDropDown(this)">
                                Select Game Title
                                <span class="dropbtn-arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                            <div class="dropdown-content d-none">
                                <div class="drop" onclick="closeDropDown(this, 'dropdownGameTitle', {'gameTitle': 'DOTA 2'} , 'gameTitle' );">
                                    <img src="{{ asset('storage/'. 'public/images/createEvent/dota.png') }}" alt="" height="30px" width="50px">
                                    <a href="#">DOTA 2</a>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button id="dropdownEventType" type="button" class="dropbtn" onclick="openDropDown(this)">
                                Select Event Type
                                <span class="dropbtn-arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                            <div class="dropdown-content d-none">
                                <div class="drop" onclick="closeDropDown(this, 'dropdownEventType', {'eventType': 'Round Robin'} , 'eventType' );">
                                    <a href="#">Round Robin</a>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button id="dropdownEventTier" type="button" class="dropbtn" onclick="openDropDown(this)">
                                Select Event Tier
                                <span class="dropbtn-arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                            <div class="dropdown-content d-none">
                                <div class="drop" onclick="closeDropDown(this, 'dropdownEventTier', {'eventTier': 'Turtle'} , 'eventTier' );">
                                    <img src="{{ asset('assets/images/turtle.png') }}" alt="" height="40px" width="60px">
                                    <a href="#">Turtle</a>
                                </div>
                                <div class="drop" onclick="closeDropDown(this, 'dropdownEventTier', {'eventTier': 'Dolphin',}, 'eventTier' );">
                                    <img src="{{ asset('assets/images/dolphin.png') }}" alt="" height="40px" width="60px">
                                    <a href="#">Dolphin</a>
                                </div>
                                <div class="drop" onclick="closeDropDown(this, 'dropdownEventTier', {'eventTier': 'Starfish'}, 'eventTier');">
                                    <img src="{{ asset('assets/images/starfish.png') }}" alt="" height="40px" width="60px">
                                    <a href="#">Starfish</a>
                                </div>
                                <div class="drop" onclick="closeDropDown(this, 'dropdownEventTier', {'eventTier': 'Mermaid'}, 'eventTier');">
                                    <img src="{{ asset('assets/images/mermaid.png') }}" alt="" height="40px" width="60px">
                                    <a href="#">Mermaid</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br><br><br><br><br>

                    <br><br><br>
                    <div class="flexbox box-width">
                        <button type="button" onclick="goToNextScreen('step-0', 'timeline-1')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                        <button type="button" onclick="goToNextScreen('step-2', 'timeline-2')" id="submit" class="oceans-gaming-default-button"> Next > </button>
                    </div>
                </div>

                <div class="text-center d-none" id="step-2" height="300vh">
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
                        <!-- <form action="#" method="POST"> -->
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
                                    <input type="date" id="startDate" onchange="checkValidDate('startDate', 'endDate');" name="startDate" placeholder=" Select a start date" required>
                                </div>
                                <div class="box">
                                    <p class="description"><b>End</b></p>
                                    <input type="date" id="endDate" onchange="checkValidDate('startDate', 'endDate');" name="endDate" placeholder=" Select an end date" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="startTime">Time of Event</label>
                            <p class="description">So that your players can set their alarms</p>
                            <div class="container">
                                <div class="box">
                                    <p class="description"><b>Start</b></p>
                                    <input type="time" id="startTime" onchange="checkValidTime('startTime', 'endTime', 'startDate', 'endDate');" name="startTime" placeholder=" Select a start time" required>
                                </div>
                                <div class="box">
                                    <p class="description"><b>End</b></p>
                                    <input type="time" id="endTime" name="endTime" onchange="checkValidTime('startTime', 'endTime', 'startDate', 'endDate');" placeholder=" Select an end time" required>
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

                        <div class="form-group">
                            <label for="eventTags">Event Tags</label>
                            <p class="description">Add some relevant keywords to help players find your event more easily</p>
                            <div class="box">
                                <input type="text" id="eventTags" name="eventTags" placeholder="Add tags" required>
                            </div>
                        </div>
                        <!-- </form> -->
                    </div>
                    <div class="flexbox box-width">
                        <button type="button" onclick="goToNextScreen('step-1', 'timeline-1')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                        <button type="button" onclick="goToNextScreen('step-3', 'timeline-3')" id="submit" class="oceans-gaming-default-button"> Next > </button>
                    </div>
                    <br><br><br>
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
                        <br>
                        <div>Event Categories</div>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;Type: <span id="paymentType"> </span></div>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;Tier: <span id="paymentTier"> </span></div>
                        <br>
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
                            <button onclick="" type="button" id="submit" class="choose-payment-method" data-bs-toggle="modal" data-bs-target="#payment-modal">
                                Choose a payment method
                            </button>
                        </div>
                        <!-- <div class="text-center">
                            <button type="button" class="oceans-gaming-default-button oceans-gaming-green-button"> <u>Payment successful</u></button>
                        </div> -->
                    </div>
                    <br>
                    <div class="flexbox box-width">
                        <button type="button" onclick="goToNextScreen('step-2', 'timeline-2')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                        <button type="button" onclick="goToNextScreen('step-4', 'timeline-4')" id="submit" class="oceans-gaming-default-button"> Next > </button>
                    </div>
                    <br><br><br>
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

                        <input onchange="toggleRadio(this, 'public')" required type="radio" id="public" name="launch_type" value="public">
                        <label for="public"><u>Public</u></label><br>
                        <div class="radio-indent public">
                            <p>Everyone can see and join your event</p>
                        </div>
                        <div class="radio-indent-hidden public d-none">
                            <input type="radio" id="sub_launch_public" name="launch_schedule" value="now">
                            <label for="sub_launch_public"><u>Launch now</u></label><br>
                            <input type="radio" id="sub_launch_public" name="launch_schedule" value="schedule">
                            <label for="sub_launch_public"><u>Schedule launch</u></label><br>
                            <input type="date" id="sub_launch_public_date" name="launch_date">
                            <input type="time" id="sub_launch_public_time" name="launch_time">
                        </div>

                        <input onchange="toggleRadio(this, 'private')" required type="radio" id="private" name="launch_type" value="private">
                        <label for="private"><u>Private</u></label><br>
                        <div class="radio-indent private">
                            <p>Only players you invite can see and join your event</p>
                        </div>
                        <div class="radio-indent-hidden private d-none">
                            <input type="radio" id="sub_launch_private" name="launch_schedule" value="now">
                            <label for="sub_launch_private"><u>Launch now</u></label><br>
                            <input type="radio" id="sub_launch_private" name="launch_schedule" value="schedule">
                            <label for="sub_launch_private"><u>Schedule launch</u></label><br>
                            <div>
                                <input type="date" id="sub_launch_date" name="launch_date">
                                <input type="time" id="sub_launch_time" name="launch_time">
                            </div>
                        </div>

                        <input onchange="toggleRadio(this, 'draft')" type="radio" id="draft" name="launch_type" required value="draft">
                        <label for="draft"><u>Save as draft</u></label>

                    </div>
                    <br><br>
                    <div class="text-center">
                        <button type="button" class="oceans-gaming-default-button">
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
                        <button type="button" onclick="goToNextScreen('step-3', 'timeline-3')" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Back </button>
                        <button type="submit" class="oceans-gaming-default-button"> Launch </button>
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
        </form>
        <div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="payment-modal-label">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    </main>
    @stack('script')
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function checkValidDate(startDateId, endDateId) {
            var startDateInput = document.getElementById(startDateId);
            var endDateInput = document.getElementById(endDateId);
            var today = new Date();
            today.setHours(0, 0, 0, 0); // Set the time to midnight for comparison


            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            if (startDate < today || endDate <= today) {
                if (startDate < today) {
                    startDateInput.value = ""
                }
                if (endDate <= today) {
                    endDateInput.value = ""
                }
                Toast.fire({
                    icon: 'error',
                    text: "Start date or end date cannot be earlier than today."
                });
            }
            if (startDateInput.value === "" || endDateInput.value === "") {
                return;
            }
            if (endDate < startDate) {
                Toast.fire({
                    icon: 'error',
                    text: "End date cannot be earlier than start date and time."
                });
                startDateInput.value = "";
            }
        }

        function checkValidTime(startTimeId, endTimeId, startDateId, endDateId) {
            var startDateInput = document.getElementById(startDateId);
            var endDateInput = document.getElementById(endDateId);
            var startTimeInput = document.getElementById(startTimeId);
            var endTimeInput = document.getElementById(endTimeId);

            var now = new Date();
            var startDate = new Date(startDateInput.value + " " + startTimeInput.value);
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
            var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (!allowedTypes.includes(selectedFile.type)) {
                selectedFile.value = '';
                Toast.fire({
                    icon: 'error',
                    text: "Invalid file type. Please upload a JPEG, PNG, or JPG file."
                })
            } else formHelper.previewSelectedImage('eventBanner', 'previewImage');
        }
    </script>
    <script>
        let stripe = Stripe('{{ env("STRIPE_KEY") }}')
        const elements = stripe.elements()
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px'
                }
            }
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

                const form = new FormData(cardForm);
                const data = {};
                form.forEach((value, key) => {
                    data[key] = value;
                });

                fetch("{{ route('stripe.organizerTeamPay') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(responseData => {
                        // Handle the response data here
                        console.log(responseData);
                    })
                    .catch(error => {
                        // Handle errors here
                        console.error(error);
                    });
            }
        })
    </script>

    <script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
    <script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>

    <!-- Including the Tagify library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initializing Tagify on the input field
        new Tagify(document.querySelector('#eventTags'), {});
    </script>


</body>