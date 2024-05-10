<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
@php
    use Carbon\Carbon;
@endphp
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    

    <main 
        x-data="alpineDataComponent"
    >
        <div id="backgroundBanner" class="member-section px-0 pt-0"
        >
            <div style="background-color: #DFE1E2;" class="pb-4">
                <br>
                <div class="member-image">
                    <div class="upload-container">
                        <label for="image-upload" class="upload-label">
                            <div class="circle-container">
                                  <div id="uploaded-image" class="uploaded-image"
                                        style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }} );"
                                    ></div>
                                <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                            </div>
                        </label>
                        <input type="file" id="image-upload" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div class="member-details mx-auto text-center">
                    <div x-show="isEditMode">
                        <input 
                            placeholder = "Enter your name..."
                            class="form-control border-primary edit-mode-player-profile-input d-inline" 
                            value="{{$userProfile->name}}"
                        >
                        <input 
                            placeholder = "Enter your company name..."
                            class="form-control border-primary edit-mode-player-profile-input d-inline" 
                            value="{{$userProfile->organizer?->companyName}}"
                        > 
                        <button 
                            x-on:click="isEditMode = false;"
                            class="mt-4 oceans-gaming-default-button oceans-gaming-transparent-button px-5 py-1"> 
                            Save
                        </button>
                    </div>
                    <div x-show="!isEditMode">
                        <h5>
                            {{$userProfile->name}}
                        </h5>
                        <p> 
                            <span class="me-2"> </span>
                            <span class="me-3"> {{$userProfile->organizer?->companyName}} </span>
                            <span class="me-2"> </span>
                            <span class="me-3"> {{$followersCount}} followers </span>
                        </p>
                        @if ($isOwnProfile)
                            <div class="text-center">
                                <button 
                                    x-on:click="isEditMode = true; fetchCountries();"
                                    class="oceans-gaming-default-button oceans-gaming-primary-button py-1 px-5"> 
                                    Edit
                                </button>
                            </div>
                        @else
                            <div class="text-center">
                                <button 
                                    x-on:click="isEditMode = true; fetchCountries();"
                                    class="me-4 oceans-gaming-default-button oceans-gaming-primary-button rounded px-3 py-2"> 
                                    Follow
                                </button>
                                <button 
                                    x-on:click="isEditMode = false;"
                                    class="oceans-gaming-default-button oceans-gaming-transparent-button bg-light border-0 rounded px-3 py-2"> 
                                    Message
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="tabs px-5" x-show="!isEditMode">
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
        </div>
        <div x-show="!isEditMode" class="tab-content pb-4 outer-tab px-5" id="Overview">
            <br> 
            <div class="showcase tab-size showcase-box showcase-column pt-4 grid-4-columns tab-size text-center">
                <div> 
                    <h3> {{$lastYearEventsCount}} </h3>
                    <p> Events Organized in Last Year </p>
                </div>
                 <div> 
                    <h3> {{$allTimeEventsCount}} </h3>
                    <p> Events Organized Across All Time </p>
                </div>
                 <div> 
                    <h3> {{$teamsCount}} </h3>
                    <p> Teams Registered Across All Time </p>
                </div>
                 <div> 
                    <h3> {{$tierPrizeCount}} </h3>
                    <p> Total Prize Pool Across All Time </p>
                </div>
            </div>
            <br><br>
            <div class="tab-size"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center carousel-works">
                @if (!isset($joinEvents[0]))
                    <p>No events available!</p>
                @else
                    <button class="carousel-button position-absolute" style="top: 100px; left: 20px;"
                        onclick="carouselWork(-2)">
                        &lt;
                    </button>
                    <button class="carousel-button position-absolute" style="top: 100px; right: 20px;"
                        onclick="carouselWork(2)">
                        &gt;
                    </button>
                    @if (!isset($joinEvents[1]))
                        <div class="d-flex justify-content-center event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Organizer.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @else
                        <div class="event-carousel-styles event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Organizer.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @endif
                 
                @endif
            </div>
        </div>

        <div  x-show="!isEditMode" class="tab-content pb-4  outer-tab d-none" id="Events">
            <div class="mx-auto tab-size"><b>Active Events</b></div>
            <br>
            @if (!isset($joinEventsActive[0]))
                <p class="tab-size">
                    This profile has no active events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Organizer.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br>
            <div class="tab-size"><b>Past Events</b></div>
            <br>
            @if (!isset($joinEventsHistory[0]))
                <p class="tab-size">
                    This profile have no past events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        @include('Organizer.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br><br>

        </div>
        <div class="grid-2-columns tab-size">
            <div class="">
                <br>
                <div> About </div>
                <br>
                <div x-show.important="isEditMode">
                    <input 
                        placeholder = "Enter your company description..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->organizer?->companyDescription}}"
                    > 

                    <br>
                    <input 
                        placeholder = "Enter your company industry..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->organizer?->industry}}"
                    > 
                    <input 
                        placeholder = "Enter your company type..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->organizer?->type}}"
                    > 
                    <br>
                    <input 
                        placeholder = "Enter your address..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->address?->addressLine1}}"
                    >
                    {{-- <span>{{$userProfile->address?->addressLine1}}</span>
                    <span>{{$userProfile->address?->addressLine2}}</span>
                    <span>{{$userProfile->address?->city}}</span>
                    <span>{{$userProfile->address?->country}}</span>
                        
                    @if($userProfile->mobile_no)
                        <br>
                        <p>
                            <span>{{$userProfile->mobile_no}}</span>
                        </p>
                    @endif --}}
                </div>
                <div x-show.important="!isEditMode">
                    @if ($userProfile->organizer?->companyDescription)
                        <p> 
                            {{$userProfile->organizer?->companyDescription}}
                        </p>
                    @else
                        <p>
                            Add a description for your company...
                        </p>
                    @endif

                    <p> {{$userProfile->organizer?->companyDescription}} </p>
                    @if ($userProfile->organizer?->industry && $userProfile->organizer?->type))
                        <br>
                        <p> 
                            <span>{{$userProfile->organizer?->industry}}</span>
                            <span>{{$userProfile->organizer?->type}}</span>
                        </p>
                    @endif
                    @if (isset($userProfile->address) && $userProfile->address?->addressLine1 && $userProfile->address?->city)
                        <br>
                        <p> 
                        <span>{{$userProfile->address?->addressLine1}}</span>
                        <span>{{$userProfile->address?->addressLine2}}</span>
                        <span>{{$userProfile->address?->city}}</span>
                        <span>{{$userProfile->address?->country}}</span>
                        </p>
                    @endif
                    @if($userProfile->mobile_no)
                        <br>
                        <p>
                            <span>{{$userProfile->mobile_no}}</span>
                        </p>
                    @endif
                </div>
            </div>
            <div class="">
                <br>
                <div> Links </div>
                <br>
                <div x-show.important="isEditMore">
                    <input 
                            placeholder = "Enter your company description..."
                            class="form-control border-primary edit-mode-player-profile-input d-inline" 
                            value="{{$userProfile->organizer?->companyDescription}}"
                    > 
                    <br>
                    <input 
                        placeholder = "Enter your company industry..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->organizer?->industry}}"
                    > 
                    <input 
                        placeholder = "Enter your company type..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->organizer?->type}}"
                    > 
                    <br>
                    <input 
                        placeholder = "Enter your address..."
                        class="form-control border-primary edit-mode-player-profile-input d-inline" 
                        value="{{$userProfile->address?->addressLine1}}"
                    >
                </div>
                <div x-show.important="!isEditMore">

                    <p> 
                        <span> </span>
                        <span>{{$userProfile->email}}</span> 
                    </p>
                    @if ($userProfile->website_link)
                        <br>
                        <p> 
                            <span></span>
                            <span>{{$userProfile->website_link}}</span>
                        </p>
                    @endif
                    @if ($userProfile->facebook_link)
                        <br>
                        <p> 
                            <span></span>
                            <span>{{$userProfile->facebook_link}}</span>
                        </p>
                    @endif
                    @if ($userProfile->website_link)
                        <br>
                        <p> 
                            <span></span>
                            <span>{{$userProfile->website_link}}</span>
                        </p>
                    @endif
                    @if ($userProfile->twitter_link)
                        <br>
                        <p> 
                            <span></span>
                            <span>{{$userProfile->twitter_link}}</span>
                        </p>
                    @endif
                </div>
                
            </div>
        </div>
        <br> <br>
    </main>

</body>

@livewireScripts
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('alpineDataComponent', () => ({
            isEditMode: false, 
            countries: [], 
            errorMessage: '', 
            isCountriesFetched: false ,
            fetchCountries: () => {
                return fetch('/countries')
                    .then(response => response.json())
                    .then(data => {
                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.countries = data?.data;
                        } else {
                            this.errorMessage = "Failed to get data!"
                            this.countries = [{
                                 name: {
                                    en: 'No country'
                                },
                                emoji_flag: '🇦🇫'
                            }];
                        }
                    })
                    .catch(error => console.error('Error fetching countries:', error));
                }
            })
        )
    })

    const uploadButton = document.getElementById("upload-button");
    const imageUpload = document.getElementById("image-upload");
    const uploadedImage = document.getElementById("uploaded-image");

    uploadButton?.addEventListener("click", function() {
        imageUpload.click();
    });

    imageUpload?.addEventListener("change", async function(e) {
        const file = e.target.files[0];

        try {
            const fileContent = await readFileAsBase64(file);
            const url = "{{ route('participant.userBanner.action', ['id' => $userProfile->id] ) }}";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-type': 'application/json',
                    'Accept': 'application/json',
                    ...window.loadBearerHeader()
                },
                body: JSON.stringify({
                    file: {
                        filename: file.name,
                        type: file.type,
                        size: file.size,
                        content: fileContent
                        }
                    }),
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                    
                if (data.success) {
                    uploadedImage.style.backgroundImage = `url(${data.data.fileName})`;
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('There was a problem with the file upload:', error);
        }
    });

    async function readFileAsBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = function(event) {
                const base64Content = event.target.result.split(';base64,')[1];
                resolve(base64Content);
            };

            reader.onerror = function(error) {
                reject(error);
            };

            reader.readAsDataURL(file);
        });
    }


    const fetchCountries = () => {
        return fetch('/countries')
            .then(response => response.json())
            .then(data => {
                console.log({data})
                return data?.data;
            })
            .catch(error => console.error('Error fetching countries:', error));
    }

    function reddirectToLoginWithIntened(route) {
        route = encodeURIComponent(route);
        let url = "{{ route('participant.signin.view') }}";
        url += `?url=${route}`;
        window.location.href = url;
    }

    function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
        const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
        tabContents.forEach(content => {
            content.classList.add("d-none");
        });
        console.log({
            tabContents
        });

        const selectedTab = document.getElementById(tabName);
        selectedTab.classList.remove('d-none');
        selectedTab.classList.add('tab-button-active');
        console.log({
            selectedTab
        });

        const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
        tabButtons.forEach(button => {
            button.classList.remove("tab-button-active");
        });
        console.log({
            tabButtons
        });

        let target = event.currentTarget;
        target.classList.add('tab-button-active');
    }

    let currentIndex = 0;

    function carouselWork(increment = 0) {
        const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
        let boxLength = eventBoxes?.length || 0;
        let newSum = currentIndex + increment;
        if (newSum >= boxLength || newSum < 0) {
            return;
        } else {
            currentIndex = newSum;
        }

        // carousel top button working
        const button1 = document.querySelector('.carousel-button:nth-child(1)');
        const button2 = document.querySelector('.carousel-button:nth-child(2)');
        if (button1 && button2) {
            button1.style.opacity = (currentIndex <= 2) ? '0.4' : '1';
            button2.style.opacity = (currentIndex >= boxLength - 2) ? '0.4' : '1';

            // carousel swing
            for (let i = 0; i < currentIndex; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }

            for (let i = currentIndex; i < currentIndex + 2; i++) {
                eventBoxes[i]?.classList.remove('d-none');
            }

            for (let i = currentIndex + 2; i < boxLength; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }
        }
    }

    carouselWork();


    function redirectToProfilePage(userId) {
        window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }

   
</script>

</html>
