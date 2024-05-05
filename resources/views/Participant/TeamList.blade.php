<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/manageEvent.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    <br><br> 
    <main>
        <div class="search-bar">
            <svg onclick= "handleSearch();" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-search search-bar2-adjust">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" id="searchInput"
                placeholder="Search using title, description, or keywords">
          
        </div>
         @include('Participant.TeamListPartial.FilterSort')
        <div class="grid-3-columns justify-content-center"> 
            @if ($count > 0)
                @foreach ($teamList as $team)
                    <a style="cursor:pointer;" class="mx-auto" href="/participant/team/{{ $team['id'] }}/manage">
                        <div class="wrapper">
                            <div class="team-section">
                                <div class="upload-container text-center">
                                    <div class="circle-container" style="cursor: pointer;">
                                        <div id="uploaded-image" class="uploaded-image"
                                            style="background-image: url({{ $team->teamBanner ? '/storage' . '/'. $team->teamBanner: '/assets/images/animations/empty-exclamation.gif' }} );"
                                        ></div>
                                        </label>
                                    </div>
                                    <div>
                                   
                                </div>
                            </div>
                            <div class="text-center">
                                <h3 class="team-name" id="team-name">{{ $team->teamName }}</h3>
                                    <span> Region: South East Asia (SEA) </span>  <br>
                                    <br>
                                    <span> Members:
                                        @if (isset($membersCount[$team->id]))
                                            {{ $membersCount[$team->id] }}
                                        @else {{ 0 }}
                                        @endif
                                    </span> <br>
                                    @if ($team->creator_id == $user->id)
                                    <small><i>Created by you</i></small>
                                    @endif
                                    <br>
                                    <span> Status: {{$membersCount[$team->id] > 5 ? 'Public (Apply)' : 'Private' }} </p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="wrapper mx-auto">
                    <div class="team-section mx-auto">
                        <div class="upload-container">
                            <label for="image-upload" class="upload-label">
                                <img                       
                                    src="{{asset('assets/images/animation/empty-exclamation.gif') }}"
                                    width="150"
                                    height="150"
                                >
                            </label>
                        </div>
                        <h3 class="team-name text-center" id="team-name">No teams yet</h3>
                        <br>
                    </div>
                </div>
            @endif
        </div>
        <br>
        <br>
    </main>

    
    @include('Participant.TeamListPartial.FilterScripts')
    <script>
        function goToScreen() {
            window.location.href = "{{route('participant.request.view')}}";
        }
    </script>
</body>

</html>
