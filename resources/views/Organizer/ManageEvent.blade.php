@include('Organizer.Layout.ManageEventHeadTag')

<body>
    @include('CommonLayout.Navbar')
    
    <main>
        <br class="d-none-at-desktop">
        <div class="">
            <header class="flexbox-welcome">
                <u>
                    <h3>
                        Manage your events
                    </h3>
                </u>
                <input type="submit" value="Create Event" onclick="goToCreateScreen();">
               

            </header>
            <div class="flexbox-filter">
                <p>
                    All
                </p>
                <p>
                    Live
                </p>
                <p>
                    Scheduled
                </p>
                <p>
                    Drafts
                </p>
                <p>
                    Ended
                </p>
            </div>
            <br>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                <span> Filter </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                    <path d="M15 7h6v6" />
                </svg>
                <span>
                    Sort
                </span>
            </div>
            <br>
            <div class="search-bar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search search-bar2-adjust">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" id="search" placeholder="Search using title, description, or keywords">
            </div>
        </div>
        <br><br>
        <div class="grid-container">
            @foreach ($eventList as $event)
            @php
            $stylesEventStatus = '';
            $stylesEventStatus .= 'padding-top: -150px; ';
            $stylesEventStatus .= 'background-color: ' . $mappingEventState[$event->status]['buttonBackgroundColor'] .' ;' ;
            $stylesEventStatus .= 'color: ' . $mappingEventState[$event->status]['buttonTextColor'] .' ; ' ;
            $stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$event->status]['borderColor'] .' ; ';

            /*
            $stylesEventRatio = '';
            $ratio = (double) $event->registeredParticipants / $event->totalParticipants;
            if ($ratio > 0.9){
            $stylesEventRatio .= "background-color: red; color: white;";
            }
            elseif ($ratio == 0){
            $stylesEventRatio .= "background-color: #8CCD39; color: white;";
            }
            elseif ($ratio > 0.5){
            $stylesEventRatio .= "background-color: #FA831F; color: white;";
            }
            elseif ($ratio <= 0.5){ $stylesEventRatio .="background-color: #FFE325; color: black;" ; } */ $stylesEventRatio="background-color: #FFE325; color: black;" ; $eventTierLower=strtolower($event->eventCategory->eventTier);
                @endphp
                <a href="{{ route('event.show', $event->id) }}" style="text-decoration: none;">
                    <div class="{{'rounded-box rounded-box-' . $eventTierLower }}">
                        <div class="centered-absolute-game-tier">
                            <img src="{{ asset( '/assets/images/'. $eventTierLower . '.png' ) }}" width="120" height="80">
                        </div>
                        <div class="{{'card-image card-image-' . $eventTierLower }}">
                            <img src="{{ asset('/assets/images/1.png') }}" alt="">
                        </div>
                        <div class="card-text">
                            <div>
                                <div class="flexbox-centered-space">
                                    <img src="{{ asset('/assets/images/menu.png') }}" alt="menu" width="50" height="40">
                                    <button class="oceans-gaming-default-button" style="@php echo $stylesEventStatus; @endphp">
                                        <u>{{$event->status}}</u>
                                    </button>
                                </div>
                                <br>
                                <p style="height : 60px; text-overflow:ellipsis; overflow:hidden; "><u>{{$event->name}}</u></p>
                                <p class="small-text"><i>
                                        {{ $organizer->companyName  }}
                                    </i></p>
                                <div class="flexbox-welcome">
                                    @php
                                    $date = \Carbon\Carbon::parse($event->eventDetail->startDateTime);
                                    $dateStr = $date->toFormattedDateString() . " " . $date->toTimeString();
                                    @endphp
                                    <div>@php echo $dateStr; @endphp</div>
                                    <button style="@php echo $stylesEventRatio; @endphp" class="oceans-gaming-default-button oceans-gaming-default-button-small flexbox-centered-space">
                                        &nbsp;
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>

                                        <span>
                                            8 / 14
                                        </span>
                                        &nbsp;
                                    </button>
                                </div>
                                <div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        &nbsp;
                                        <span>Prize</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                        &nbsp;
                                        <span>Free</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        &nbsp;
                                        <span>{{ $event->venue }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </a>
                @endforeach
        </div>
        @stack('script')
        <script>
            function goToCreateScreen(){
                let url = "{{ route('event.create') }}";
                window.alert(url);  
                window.location.href = url;
            }
        </script>
    </main>


    <!-- <script src="script.js"></script> -->