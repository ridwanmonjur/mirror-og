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

    <main x-data="alpineDataComponent">
        <div id="backgroundBanner" class="member-section px-2 pt-2"
            style="background-image: url({{ '/storage' . '/'. $userProfile->participant->backgroundBanner }} );"
        >
            @if($isOwnProfile)
                <div class="d-flex justify-content-end py-0 my-0 mb-2">
                    <input type="file" id="backgroundInput" class="d-none"> 
                    <button 
                        onclick="document.getElementById('backgroundInput').click();"
                        class="btn btn-secondary text-light rounded-pill py-2 me-3"> 
                        Change Background
                    </button>
                    <button 
                        x-cloak
                        x-show="!isEditMode"
                        x-on:click="isEditMode = true; fetchCountries();"
                        class="oceans-gaming-default-button oceans-gaming-primary-button px-3 py-2"> 
                        Edit Profile
                    </button>
                    <button 
                        x-cloak
                        x-show="isEditMode"
                        x-on:click="submitEditProfile(event)"
                        data-url="{{route('participant.profile.update')}}"
                        class="oceans-gaming-default-button oceans-gaming-transparent-button px-3 py-2"> 
                        Save
                    </button>
                </div>
             @endif
            <div class="d-flex justify-content-center align-items-center flex-wrap">
                <div class="member-image">
                    <div class="upload-container">
                        <label for="image-upload" class="upload-label">
                            <div class="circle-container">
                                  <div id="uploaded-image" class="uploaded-image"
                                        style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }} ); background-size: cover;"
                                    ></div>
                                <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                            </div>
                        </label>
                        <input type="file" id="image-upload" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div class="member-details">
                        <div x-cloak x-show="isEditMode">
                            <input 
                                placeholder = "Enter your nickname..."
                                style="width: 250px;"
                                class="form-control border-primary player-profile__input d-inline" 
                                x-model="participant.nickname" 
                            > 
                            <br>
                            <span class="d-inline-flex justify-content-between align-items-center">
                                <input
                                    placeholder = "Your bio..."
                                    style="width: 200px;"
                                    class="form-control border-primary player-profile__input d-inline me-3" 
                                    x-model="participant.bio" 
                                > 
                                <input 
                                    placeholder="Age"
                                    style="width: 60px;"
                                    class="form-control border-primary player-profile__input d-inline" 
                                    x-model="participant.age" 
                                >
                            </span> 
                            <br> <br>
                            <div class="w-100 d-flex justify-content-start align-items-center flex-wrap">
                                <span class="me-3">
                                    <svg
                                        class="me-2 align-middle" 
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <select
                                        x-model="participant.region" 
                                        style="width: 150px;"    
                                        class="form-control d-inline rounded-pill"
                                    >
                                        <template x-for="country in countries">
                                            <option x-bind:value="country.name.en">
                                            <span x-text="country.emoji_flag" class="mx-3"> </span>  
                                            <span x-text="country.name.en"> </span>
                                            </option>
                                        </template>
                                    </select> 

                                </span>
                                <span>
                                    <svg 
                                        class="align-middle"
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                        <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                        <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                    </svg>
                                    <input 
                                        style="width: 150px;"
                                        placeholder = "Enter your link..."
                                        class="form-control border-primary player-profile__input d-inline" 
                                        x-model="participant.domain"
                                    > 
                                </span>
                                <span>
                                    <svg
                                        class="align-middle"
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                    </svg>
                                    <span>Joined {{Carbon::parse($userProfile->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                                </span>
                            </div>
                                
                            <br><br>
                          

                            {{-- <img src="css/images/dota.png" class="icons-game"> Dota 2&nbsp;&nbsp;&nbsp;&nbsp; --}}
                            {{-- <img src="css/images/lol.png" class="icons-game"> League Of Legends&nbsp;&nbsp;&nbsp;&nbsp; --}}
                            {{-- <img src="css/images/valo.jpg" class="icons-game"> Valorant<br> --}}
                            <br>
                        </div>
                    <div x-cloak x-show="!isEditMode">
                        <h4>
                            {{$userProfile->name}}
                        </h4>
                        <p>{{$userProfile->participant->nickname}}, {{$userProfile->participant->age}}<p>
                        <p>
                            {{$userProfile->participant->bio}}
                        </p>
                        <div class="d-flex justify-content-between flex-wrap w-75">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                </svg>
                                <span>{{$userProfile->participant->region}}</span>
                            </span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                </svg>
                                <span>{{$userProfile->participant->domain}}</span>
                            </span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                                <span>Joined {{Carbon::parse($userProfile->participant->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                            </span>
                        <br><br><br>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Activity', 'outer-tab')">Activity</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Teams', 'outer-tab')">Teams</button>
        </div>
        <div class="tab-content pb-4  outer-tab" id="Overview">
            <br><br>
            <div class="d-flex justify-content-center"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center">
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
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @else
                        <div class="event-carousel-styles event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @endif
                 
                @endif
            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div @class(["showcase-box d-none-until-hover-parent" , 
                            "d-flex justify-content-between flex-wrap" => !isset($awardList[2])
                    ])>
                        <div>
                            <p>Events Joined: {{ $totalEventsCount }}</p>
                            <p>Wins: {{ $wins }}</p>
                            <p>Win Streak: {{ $streak }}</p>
                        </div>
                        <div class="d-none-until-hover">
                            <div class="d-flex justify-content-between w-100 h-100">
                                @foreach ($awardList as $award)
                                    <div>
                                        <img src="{{ '/' . 'storage/' . $award->awards_image }} " alt="Trophy" class="trophy me-2">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    @if (!isset($achievementList[0]))
                        <ul class="achievement-list mt-4">
                            <p>No achievements available</p>
                        </ul>
                    @else
                        <ul class="achievement-list">
                            @foreach ($achievementList as $achievement)
                                <li>
                                    <span class="additional-text d-flex justify-content-between">
                                        <span>
                                        {{ $achievement->title }} ({{ \Carbon\Carbon::parse($achievement->created_at)->format('Y') }})
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                        </svg>
                                    </span><br>
                                    <span class="ps-2"> {{ $achievement->description }} </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Activity">
            <br>
            <div class="tab-size"><b>New</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$user->id" :duration="'new'"> </livewire>
            <div class="tab-size"><b>Recent</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$user->id" :duration="'recent'"> </livewire>
            <div class="tab-size"><b>Older</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$user->id" :duration="'older'"> </livewire>
            
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Events">
             <br>
            <div class="tab-size"><b>Active Events</b></div>
            <br>
            @if (!isset($joinEventsActive[0]))
                <p class="tab-size">
                    This profile has no active events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
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
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Teams">
             <br>
            <div class="tab-size"><b>Current Teams</b></div>
            @if (isset($teamList[0]))
                <table class="member-table">
                    <thead>
                        <tr>
                            <th> </th>
                            <th>Team name</th>
                            <th>Region</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamList as $team)
                            <tr class="st">
                                <td> </td>
                                <td class="d-flex align-items-center">
                                    <img
                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/'. $team->teamBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} 
                                        height="40"
                                        width="40"
                                    > 
                                    <span>{{$team->teamName}}</span>
                                </td>
                                <td>China</td>
                                <td>{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No current teams</div>
            @endif
            <br> <br>
            <div class="tab-size"><b>Past Teams</b></div>

            @if (isset($pastTeam[0]))
                <table class="member-table">
                    <thead>
                        <tr>
                            <th> </th>
                            <th>Team name</th>
                            <th>Region</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastTeam as $team)
                            <tr class="st">
                                <td> </td>
                                <td class="d-flex align-items-center">
                                    <img
                                        class="d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/'. $team->teamBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} 
                                        height="40"
                                        width="40"
                                    > 
                                    <span>{{$team->teamName}}</span>
                                </td>
                                <td>China</td>
                                <td>{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No past teams</div>
            @endif
        </div>


    </main>


</body>

@livewireScripts
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('alpineDataComponent', () => ({
            isEditMode: false, 
            isAddGamesMode: false,
            countries: [], 
            participant: {
                id: {{ $userProfile->participant->id }},
                nickname : '{{$userProfile->participant->nickname}}',
                bio: '{{$userProfile->participant->bio}}',
                age: '{{$userProfile->participant->age}}',
                domain: '{{$userProfile->participant->domain}}',
                region: '{{$userProfile->participant->region}}',
            },
            errorMessage: null, 
            isCountriesFetched: false ,
            async fetchCountries () {
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
            },
            async submitEditProfile (event) {
                try {
                    event.preventDefault(); 
                    const url = event.target.dataset.url; 
                    this.participant.age = Number(this.participant.age);
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: window.loadBearerCompleteHeader(),
                        body: JSON.stringify({
                            ...Alpine.raw(this.participant),
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                        
                    if (data.success) {
                        let currentUrl = window.location.href;
                        if (currentUrl.includes('?')) {
                            currentUrl = currentUrl.split('?')[0];
                        } 

                        localStorage.setItem('success', true);
                        localStorage.setItem('message', data.message);
                        window.location.replace(currentUrl);
                    } else {
                        this.errorMessage = data.message;
                    }
                } catch (error) {
                    this.errorMessage = "Could not make the request";
                    console.error('There was a problem with the request:', error);
                } 
            },
        }))
    })

    const uploadButton = document.getElementById("upload-button");
    const imageUpload = document.getElementById("image-upload");
    const uploadedImage = document.getElementById("uploaded-image");
    const backgroundInput = document.getElementById("backgroundInput");
    const backgroundBanner = document.getElementById("backgroundBanner")
    uploadButton?.addEventListener("click", function() {
        imageUpload.click();
    });

     backgroundInput?.addEventListener("change", async function(e) {
        const file = e.target.files[0];

        try {
            const fileContent = await readFileAsBase64(file);
            const url = "{{ route('participant.userBackground.action', ['id' => $userProfile->id] ) }}";
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
                    backgroundBanner.style.backgroundImage = `url(${data.data.fileName})`;
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('There was a problem with the request:', error);
        }
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
