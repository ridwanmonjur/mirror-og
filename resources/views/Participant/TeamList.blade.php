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
    @include('__CommonPartials.NavbarGoToSearchPage')
    <br><br> 
    <main>
        <h5> Your Teams </h5> <br> 
        <div class="search-bar">
            <input type="hidden" id="countServer" value="{{$count}}">
            <input type="hidden" id="teamListServer" value="{{json_encode($teamList)}}">
            <input type="hidden" id="membersCountServer" value="{{json_encode($membersCount)}}">
            <input type="hidden" id="userIdServer" value="{{$user->id}}">

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
         @include('Participant.__TeamListPartial.FilterSort')
        <div class="grid-3-columns justify-content-center" id="filter-sort-results"> 
        </div>
        <br>
        <br>
    </main>

    
    @include('Participant.__TeamListPartial.FilterScripts')
    <script>
        function goToScreen() {
            window.location.href = "{{route('participant.request.view')}}";
        }

        let teamListServer = document.getElementById('teamListServer');
        let userIdServer = document.getElementById('userIdServer');
        let membersCountServer = document.getElementById('membersCountServer');
        let countServer = document.getElementById('countServer');
        let teamListServerValue = JSON.parse(teamListServer.value);
        let membersCountServerValue = JSON.parse(membersCountServer.value);
        let countServerValue = Number(countServer.value);
        let userIdServerValue = Number(userIdServer.value);
        let filterSortResultsDiv = document.getElementById('filter-sort-results');
        console.log({teamListServerValue, membersCountServerValue, countServerValue});

        function paintScreen(teamListServerValue, membersCountServerValue, countServerValue) {
            let html = ``;
            if (countServerValue <= 0) {
                html+=`
                    <div class="wrapper mx-auto">
                    <div class="team-section mx-auto">
                        <div class="upload-container">
                            <label for="image-upload" class="upload-label">
                                <img                       
                                    src="/assets/images/animation/empty-exclamation.gif"
                                    width="150"
                                    height="150"
                                >
                            </label>
                        </div>
                        <h3 class="team-name text-center" id="team-name">No teams yet</h3>
                        <br>
                    </div>
                </div>
                `;
            } else {
                for (let team of teamListServerValue) {
                    html+=`
                        <a style="cursor:pointer;" class="mx-auto" href="/participant/team/${team?.id}/manage">
                            <div class="wrapper">
                                <div class="team-section">
                                    <div class="upload-container text-center">
                                        <div class="circle-container" style="cursor: pointer;">
                                            <img
                                                onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                                id="uploaded-image" class="uploaded-image"
                                                src="${team?.teamBanner ? '/storage' + '/' + team?.teamBanner : '/assets/images/animations/empty-exclamation.gif' }"
                                            >
                                            </label>
                                        </div>
                                        <div>
                                    
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h3 class="team-name" id="team-name">${team?.teamName}</h3>
                                        <span> Region: ${team?.country_name ? team?.country_name: '-'} </span>  <br>
                                        <br>
                                        <span> Members:
                                            ${membersCountServerValue[team?.id] ? membersCountServerValue[team?.id] : 0}
                                        </span> <br>
                                        <small class="${team?.creator_id != userIdServerValue && 'd-none'}"><i>Created by you</i></small>
                                        <br>
                                        <span> 
                                            ${membersCountServerValue[team?.id] ? 
                                                (membersCountServerValue[team?.id] > 5 ? 'Status: Public (Apply)' : 'Status: Private')
                                                : '' 
                                            } 
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                }
            }
            console.log({html})

            filterSortResultsDiv.innerHTML = html;
        }

        paintScreen(teamListServerValue, membersCountServerValue, countServerValue);
    </script>
</body>

</html>
