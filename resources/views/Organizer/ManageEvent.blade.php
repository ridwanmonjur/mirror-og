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
                    <a href="{{ route('event.index', 
                        array_merge(request()->query(), ['status' => 'ALL', 'page' => 1])
                        ) }}">All</a>
                </p>
                <p>
                    <a href="{{ route('event.index',    
                        array_merge(request()->query(), ['status' => 'LIVE', 'page' => 1])
                        ) }}">Live</a>
                </p>
                <p>
                    <a href="{{ route('event.index',
                        array_merge(request()->query(), ['status' => 'SCHEDULED', 'page' => 1])
                        ) }}">Scheduled</a>
                </p>
                <p>
                    <a href="{{ route('event.index',
                        array_merge(request()->query(), ['status' => 'DRAFT', 'page' => 1])
                        ) }}">Drafts</a>
                </p>
                <p>
                    <a href="{{ route('event.index',
                        array_merge(request()->query(), ['status' => 'ENDED', 'page' => 1])
                        ) }}">Drafts</a>
                </p>
            </div>
            <br>
            <div>
                <span class="icon2" onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2" onclick="openElementById('close-option'); openElementById('sort-option'); closeElementById('filter-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                        <path d="M15 7h6v6" />
                    </svg>
                    <span>
                        Sort
                    </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2 d-none" id="close-option" onclick="closeElementById('close-option'); closeElementById('filter-option'); closeElementById('sort-option');">
                    <span style="font-weight: 900;">X</span>
                    <span>
                        Close
                    </span>
                </span>
            </div>
            <br>
            <div id="sort-option" class="d-none">
                <form action="{{ route('event.index') }}" method="get">

                    <!-- Include existing request parameters -->
                    @foreach(request()->except('sort', 'sortType') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="page" value="1">
                    <label> Sort by:</label>
                    &emsp;&emsp;&emsp;
                    <input type="hidden" name="sortType">
                    <span class="sort-box">
                        <input type="checkbox" name="sort" value="startDate">
                        <label for="startDate">Start Date</label>
                    </span>
                    &ensp; &ensp;
                    <span class="sort-box">
                        <input type="checkbox" name="sort" value="endDate">
                        <label for="endDate">End Date</label>
                    </span>
                    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                    <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button" style="background: #8CCD39 !important">
                        Reset
                    </button>
                    <button type="submit" class="oceans-gaming-default-button">Sort</button>
                    <br> &emsp;&emsp;&emsp;&emsp;&emsp;
                    <input type="hidden" name="sortType" value="1">
                    &nbsp; &nbsp;
                    <!-- <input type="hidden" name="sortType" value="0">
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; -->
                </form>
            </div>

            <div id="filter-option" class="">
                <form name="filter" action="{{ route('event.index') }}" method="get">
                    <!-- Include existing request parameters -->
                    @foreach(request()->except('gameTitle', 'eventTier', 'eventType') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="page" value="1">
                    <div>
                        <label> Game Title:</label>
                        &emsp;
                        <input type="checkbox" name="gameTitle" value="Dota2">
                        <label for="gameTitle">Dota 2</label>
                        &ensp;
                        <input type="checkbox" name="gameTitle" value="Dota">
                        <label for="gameTitle">Dota</label>
                        &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                        <button type="submit" class="oceans-gaming-default-button">Filter</button>
                    </div>
                    <div>
                        <label> Event Type:</label>
                        &emsp;
                        <input type="checkbox" name="eventTier" value="Tier">
                        <label for="eventTier">Tier</label>
                        &ensp;
                        <input type="checkbox" name="eventTier" value="League">
                        <label for="eventTier">League</label>
                    </div>
                    <div>
                        <label> Event Tier: </label>
                        &emsp;&nbsp;&nbsp;
                        <input type="checkbox" name="eventType" value="Dolphin">
                        <label for="eventType">Dolphin</label>
                        &ensp;
                        <input type="checkbox" name="eventType" value="Turtle">
                        <label for="eventType">Turtle</label>
                        &ensp;
                        <input type="checkbox" name="eventType" value="Starfish">
                        <label for="eventType">Starfish</label>
                    </div>
                </form>
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

        <div class="scrolling-pagination grid-container">
            @include("Organizer.ManageEventScroll")
        </div>

        <div class="no-more-data d-none" style="margin-top: 150px;"></div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // hyperlink
            function resetUrl() {
                let url = "{{ route('event.index') }}";
                window.location.href = url;
            }

            function goToCreateScreen() {
                let url = "{{ route('event.create') }}";
                window.location.href = url;
            }

            // open/ close
            function openElementById(id) {
                const element = document.getElementById(id);
                if (element) element?.classList.remove("d-none");
            }

            function closeElementById(id) {
                const element = document.getElementById(id);
                if (element && !(element.classList.contains("d-none"))) element?.classList.add("d-none");
            }


            function sortAscending(index) {
                const list = document.querySelectorAll('.ascending');
                setHiddenElementValue(index, 'asc')
                element = list[index];
                element.classList.toggle('d-none');
                element.nextElementSibling.classList.toggle('d-none');
            }

            function sortDescending(index) {
                const list = document.querySelectorAll('.descending');
                setHiddenElementValue(index, 'desc')
                element = list[index]
                element.classList.toggle('d-none');
                element.nextElementSibling.classList.toggle('d-none');
            }

            function sortNone(index) {
                const list = document.querySelectorAll('.no-sort');
                setHiddenElementValue(index, 'desc')
                element = list[index]
                element.classList.toggle('d-none');
                element.previousElementSibling.previousElementSibling.classList.toggle('d-none');
            }

            function setHiddenElementValue(key, value) {
                let input = document.querySelector(`input[name=sortType]`);
                const sortType = JSON.parse(input.value) ?? {};
                sortType[key] = value;
                input.value = JSON.stringify(sortType);
            }
            const urlParams = new URLSearchParams(window.location.search);
            console.log({
                urlParams
            })
            
            window.onload = function() {
                const urlParams = new URLSearchParams(window.location.search);
                const inputNameList = ['gameTitle', 'eventTier', 'eventType'];
                for (let j = 0; j < inputNameList.length; j++) {
                    const inputName = inputNameList[j];
                    const inputParamValueListFromName = urlParams.getAll(inputName);
                    for (let i = 0; i < inputParamValueListFromName.length; i++) {
                        const inputParamValueFromName = inputParamValueListFromName[i];
                        const checkbox = document.querySelector(`input[type="checkbox"][name="${inputName}"][value="${inputParamValueFromName}"]`);
                        if (checkbox) {
                            checkbox.checked = true
                            console.log({
                                inputParamValueFromName,
                                inputName,
                                checkbox,
                            });
                        }
                    }
                }
            }
            // const eventTypes = urlParams.getAll('eventType');

            const elementList = document.getElementsByClassName("sort-box");
            for (let i = 0; i < elementList.length; i++) {
                elementList[i].innerHTML +=
                    `
                    <span class="ascending" 
                        onclick="sortAscending(${i});"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-up"><polyline points="17 11 12 6 7 11"></polyline><polyline points="17 18 12 13 7 18"></polyline></svg>
                    </span>
                    <span class="descending d-none"
                        onclick="sortDescending(${i});"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-down"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 6 12 11 17 6"></polyline></svg>                    
                    </span>
                    <span class="no-sort d-none"
                        onclick="sortNone(${i});"
                    >
                        &nbsp; 
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    `;
            }
        </script>
        <script>
            var ENDPOINT = "{{ route('event.index') }}";
            var page = 1;
            window.addEventListener(
                "scroll",
                throttle((e) => {
                    var windowHeight = window.innerHeight;
                    var documentHeight = document.documentElement.scrollHeight;
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop + windowHeight >= documentHeight - 250) {
                        page++;
                        infinteLoadMore(page);
                    }
                }, 300)
            );
        </script>
    </main>


    <!-- <script src="script.js"></script> -->