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
    <br>
    <main>
        <h5> Your Teams </h5> <br> 
        <form id="newMembersForm">
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
        </form>
        <br>
        <br>
    </main>

    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    @include('Participant.__TeamListPartial.FilterScripts')
    <script>
        function goToScreen() {
            window.location.href = "{{route('participant.request.view')}}";
        }

        let newMembersForm = document.getElementById('newMembersForm');
        let filteredSortedMembers = [];
        let newMembersFormKeys = ['sortKeys', 'birthDate', 'region', 'status'];
        let sortKeysInput = document.getElementById("sortKeys");

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

        function setSortForFetch(value) {
            const element = document.getElementById("sortKeys");

            if (element) {
                element.value = value;
                const event = new CustomEvent("formChange", {
                    detail: {
                        name: 'sortKeys',
                        value: value,
                    }
                }); 
                window.dispatchEvent(event);
                fetchMembers();
            }
        }

        
        window.addEventListener('formChange',
            debounce((event) => {
                changeFilterSortUI(event);
                fetchMembers();
            }, 300)
        );
        
        newMembersForm.addEventListener('change',
            debounce((event) => {
                changeFilterSortUI(event);
                fetchMembers();
            }, 300)
        );

        function changeFilterSortUI(event) {
            let target = event.target; 
            if (event.detail) {
                target = event.detail;
            }    

            name = target.name;
            value = target.value;
            
            if (name == "search") {
                return;
            }
                
            let formData = new FormData(newMembersForm);
            let targetElemnetParent = document.querySelector(`small[data-form-parent="${name}"]`);
            let defaultFilter = document.querySelector(`small[data-form-parent="default-filter"]`);
            
            let isShowDefaults = true;
            for (let newMembersFormKey of newMembersFormKeys) {
                let elementValue = formData.getAll(newMembersFormKey);
                if (elementValue != "" || (Array.isArray(elementValue) && elementValue[0] )) {
                    isShowDefaults = isShowDefaults && false;
                }
            }

            if (isShowDefaults) {
                defaultFilter.classList.remove('d-none');
            } else {
                defaultFilter.classList.add('d-none');
            }

            console.log({targetElemnetParent});
            targetElemnetParent.innerHTML = '';

            let valuesFormData = formData.getAll(name);
            if (value == "" || (Array.isArray(valuesFormData) && valuesFormData[0] == null )) {
                return;
            }
            

            console.log("HI", {name, value, LIST: formData.getAll(name)});
            console.log("HI", {name, value, LIST: formData.getAll(name)});
            console.log("HI", {name, value, LIST: formData.getAll(name)});
            console.log("HI", {name, value, LIST: formData.getAll(name)});

            targetElemnetHeading = document.createElement('small');
            targetElemnetHeading.classList.add('me-2');
            targetElemnetHeading.innerHTML = String(name)?.toUpperCase();
            targetElemnetParent.append(targetElemnetHeading);
            for (let formValue of valuesFormData) {
                targetElemnet = document.createElement('small');
                targetElemnet.classList.add('btn', 'btn-secondary', 'text-light', 
                    'rounded-pill', 'px-2', 'py-0', 'me-1'
                );
                targetElemnet.innerHTML = formValue;
                targetElemnetParent.append(targetElemnet);
            }
                    
        }

        function sortMembers(membersJson) {
            return membersJson;
        }

        async function fetchMembers(event = null) {
            let route;
            let bodyHtml = '';

            let formData = new FormData(newMembersForm);
            let sortedMembers = sortMembers(teamListServerValue);
            filteredSortedMembers = [];

            for (let sortedMember of sortedMembers) {
                let isToBeAdded = true;
                let nameFilter = String(formData.get('search')).toLowerCase().trim();
                let regionFilter = formData.get('region');
                let ageFilter = formData.get('birthDate');

                let statusListFilter = formData.getAll('status');
                if (nameFilter != "" && !(
                    String(sortedMember?.user?.name).includes(nameFilter) ||
                    String(sortedMember?.user?.email).includes(nameFilter)
                )) {
                    isToBeAdded = isToBeAdded && false;
                } 

                if (regionFilter != "" && sortedMember?.user?.participant?.region != regionFilter) {
                    isToBeAdded = isToBeAdded && false;
                } 

                let isArrayFilter = statusListFilter && statusListFilter[0] == null;
                for (let statusItemFilter of statusListFilter) {
                    if (statusItemFilter === sortedMember?.status) isArrayFilter = true || isArrayFilter;
                }
                isToBeAdded = isArrayFilter && isToBeAdded;

                if (ageFilter != "" && new Date(sortedMember?.user?.participant?.birthday) < new Date(ageFilter)) {
                    isToBeAdded = isToBeAdded && false;
                } 

            if (isToBeAdded) {
                    filteredSortedMembers.push(sortedMember);
            }
            }

            paintScreen(filteredSortedMembers, membersCountServerValue, countServerValue);

        }

        async function fetchCountries () {
            try {
                const data = await storeFetchDataInLocalStorage('/countries');
                if (data?.data) {
                    countries = data.data;
                    const choices2 = document.getElementById('select2-country2');
                    let countriesHtml = "<option value=''>Choose a country</option>";
                    countries.forEach((value) => {
                        countriesHtml +=`
                            <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                        `;
                    });

                    choices2.innerHTML = countriesHtml;
                } else {
                    errorMessage = "Failed to get data!";
                }
            } catch (error) {
                console.error('Error fetching countries:', error);
            }
        }

        function resetInput(name) {
            document.querySelector(`[name="${name}"]`).value = '';
            let formData = new FormData(newMembersForm);
            let newValue = name == "sortKeys" ? [] : ""; 
            formData.set(name, newValue);
            const event = new CustomEvent("formChange", {
                detail: {
                    name: name,
                    value: newValue
                }
            }); 
            window.dispatchEvent(event);
        }

        fetchCountries();

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
