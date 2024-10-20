function goToScreen() {
    let url = document.getElementById('request_view_route')?.value;
    window.location.href = url;
}

// Elements
let newTeamsForm = document.getElementById('newTeamsForm');
let filteredSortedTeams = [];
let newTeamsFormKeys = ['sortKeys', 'created_at', 'region', 'status', 'membersCount'];
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
console.log({
    teamListServerValue,
    membersCountServerValue,
    countServerValue
});

// UTILITIES

function getNestedValue(obj, propertyPath) {
    return propertyPath.split('.').reduce((acc, part) => acc && acc[part], obj);
}

function sortByProperty(arr, propertyPath, ascending = true) {
    return arr.sort((a, b) => {
        let aValue = getNestedValue(a, propertyPath);
        let bValue = getNestedValue(b, propertyPath);

        if (typeof aValue === 'string' && typeof bValue === 'string') {
            return ascending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        } else {
            return ascending ? aValue - bValue : bValue - aValue;
        }
    });
}


function formatStringUpper(str) {
    return str
        .replace(/^./, (match) => match.toUpperCase())
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/(_|\s)([a-z])/g, (match, p1, p2) => p1 + p2.toUpperCase())
        .replace(/_/g, ' ');
}

function setSortForFetch(value) {
    const element = document.getElementById("sortKeys");
    const sortByTitleId = document.getElementById('sortByTitleId');
    sortByTitleId.innerText = formatStringUpper(value);
    if (element) {
        element.value = value;
        const event = new CustomEvent("formChange", {
            detail: {
                name: 'sortKeys',
                value: value,
            }
        });
        window.dispatchEvent(event);
    }
}

function changeSortType() {
    let sortTypeElement = document.getElementById("sortType");
    let sortIconList = document.querySelector(".sort-icon-list");
    let currentSortType = sortTypeElement?.value;
    if (!sortTypeElement) return;
    sortIconList?.querySelectorAll("svg").forEach((element)=>{
        element.classList.add("d-none");
    })

    if (currentSortType === "") {
        sortTypeElement.value = "asc";
        sortIconList.querySelector(`[data-value="asc-icon"]`).classList.remove("d-none");
    }

    if (currentSortType === "asc") {
        sortTypeElement.value = "desc";
        sortIconList.querySelector(`[data-value="desc-icon"]`).classList.remove("d-none");
    }

    if (currentSortType === "desc") {
        sortTypeElement.value = "";
    }

    fetchTeams();
}


window.addEventListener('formChange',
    debounce((event) => {
        changeFilterSortUI(event);
        fetchTeams();
    }, 300)
);

newTeamsForm.addEventListener('change',
    debounce((event) => {
        changeFilterSortUI(event);
        fetchTeams();
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

    if (name ===  "sortKeys") {
        let sortTypeElement = document.getElementById("sortType");
        let sortIconList = document.querySelector(".sort-icon-list");
        let currentSortType = sortTypeElement?.value;
        if (!sortTypeElement) return;
        sortIconList?.querySelectorAll("svg").forEach((element)=>{
            element.classList.add("d-none");
        })

        sortTypeElement.value = "asc";
        sortIconList.querySelector(`[data-value="asc-icon"]`).classList.remove("d-none");
    }

    let formData = new FormData(newTeamsForm);
    let targetElemnetParent = document.querySelector(`small[data-form-parent="${name}"]`);
    let defaultFilter = document.querySelector(`small[data-form-parent="default-filter"]`);

    let isShowDefaults = true;
    for (let newTeamsFormKey of newTeamsFormKeys) {
        let elementValue = formData.getAll(newTeamsFormKey);
        if (elementValue != "" || (Array.isArray(elementValue) && elementValue[0])) {
            isShowDefaults = isShowDefaults && false;
        }
    }

     if (isShowDefaults) {
        defaultFilter.classList.remove('d-none');
    } else {
        defaultFilter.classList.add('d-none');
    }

    targetElemnetParent.innerHTML = '';

    let valuesFormData = formData.getAll(name);
    if (value == "" || (Array.isArray(valuesFormData) && valuesFormData[0] == null)) {
        return;
    }

    targetElemnetHeading = document.createElement('span');
    targetElemnetHeading.classList.add('me-2');
    targetElemnetHeading.innerHTML = formatStringUpper(name);
    targetElemnetParent.append(targetElemnetHeading);        
    for (let formValue of valuesFormData) {
        let targetElemnet = document.createElement('small');
        targetElemnet.classList.add('btn', 'btn-secondary', 'text-light', 
            'rounded-pill', 'px-2', 'py-0', 'me-1'
        );

        targetElemnet.dataset.type = target.type === "checkbox" ? "checkbox" : target.type;
        targetElemnet.dataset.name = name;
        targetElemnet.dataset.value = formValue;
        targetElemnet.innerHTML = `
            <span> ${formatStringUpper(formValue)} </span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle ms-2" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
            </svg>
        `;
        
        targetElemnetParent.append(targetElemnet);

        console.log({name, value: formValue, targetElemnet, type: target.type});
        if (targetElemnet.dataset.type === "checkbox") {
            targetElemnet.onclick = function (event2) {
                let target2 = event2.currentTarget; 
                let name2 = target2.dataset.name;
                let value2 = target2.dataset.value;
                let checkbox = document.querySelector(`input[type="checkbox"][name="${name2}"][value="${value2}"]`);
                console.log({checkbox, target2, dataset: target2.dataset});
                checkbox.checked = false;
                checkbox.removeAttribute('checked');
                window.dispatchEvent(new CustomEvent("formChange", {
                    detail: {
                        name: name2,
                        value: formData.getAll(name2),
                        type: "checkbox"
                    }
                }) );
            }
        }   else {
            targetElemnet.onclick = function (event3) {
                let target3 = event3.currentTarget; 
                let name3 = target3.dataset.name;
                let value3 = target3.dataset.value; 
                console.log({target3, dataset: target3.dataset});

                let resetButton = document.querySelector(`#${name3}ResetButton`);
                resetButton.click();
            }
        }
    }

}

function sortTeams(arr, sortKey, sortOrder) {
    if (sortKey === "" || sortKey === null || sortOrder === "" || sortOrder === null) {
    const sortByTitleId = document.getElementById('sortByTitleId');
        sortByTitleId.innerText = "Sort By";
        return teamListServerValue;  
    }
    
    let arr2 = [...arr];
    console.log({sortKey, sortOrder});

    let sortTypeToKeyMap = {
        "created_at" : "created_at",
        "region" : "country",
        "status": "status",
        "name": "teamName",
        "recent": "id",
        "membersCount" : "membersCount"  
    };

    arr2 = sortByProperty(arr2, sortTypeToKeyMap[sortKey], sortOrder == "asc");
    return arr2;
}

async function fetchTeams(event = null) {
    let route;
    let bodyHtml = '';

    let formData = new FormData(newTeamsForm);
    let sortedTeams = sortTeams(teamListServerValue, formData.get("sortKeys"), formData.get("sortType"));
    filteredSortedTeams = [];

    for (let sortedTeam of sortedTeams) {
        let isToBeAdded = true;
        let nameFilter = String(formData.get('search')).toLowerCase().trim();
        let regionFilter = formData.get('region');
        let createdAtFilter = formData.get('created_at');
        let statusListFilter = formData.getAll('status');
        let membersCountFilter = formData.getAll('membersCount');

        
        if (nameFilter != "" && !(
                String(sortedTeam?.teamName).includes(nameFilter) 
            )) {
            isToBeAdded = isToBeAdded && false;
        }

        if (regionFilter != "" && sortedTeam?.country != regionFilter) {
            isToBeAdded = isToBeAdded && false;
        }

        if (membersCountFilter != "" && sortedTeam?.membersCount < membersCountFilter) {
            isToBeAdded = isToBeAdded && false;
        }

        let isArrayFilter = statusListFilter && statusListFilter[0] == null;
        for (let statusItemFilter of statusListFilter) {
            let isItemGood = statusItemFilter === "private" && sortedTeam?.membersCount >= 5
                || statusItemFilter === "public" && sortedTeam?.membersCount < 5;

            if (isItemGood) {
                isArrayFilter = true || isArrayFilter;
            }
        }

        isToBeAdded = isArrayFilter && isToBeAdded;

        if (createdAtFilter != "" && new Date(sortedTeam?.created_at) < new Date(createdAtFilter)) {
            isToBeAdded = isToBeAdded && false;
        }

        if (isToBeAdded) {
            filteredSortedTeams.push(sortedTeam);
        }
    }

    paintScreen(filteredSortedTeams, membersCountServerValue, countServerValue);

}

async function fetchCountries() {
    try {
        const data = await storeFetchDataInLocalStorage('/countries');
        if (data?.data) {
            countries = data.data;
            const choices2 = document.getElementById('select2-country2');
            let countriesHtml = "<option value=''>Choose a country</option>";
            countries.forEach((value) => {
                countriesHtml += `
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
    let formData = new FormData(newTeamsForm);
    let newValue = "";
    if (name == "sortKeys") {
        newValue = [];
    }

    if (name == "membersCount") {
        newValue = "0";
    } 

    document.querySelector(`[name="${name}"]`).value = newValue;
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
        html += `
            <div class="wrapper mx-auto">
            <div class="team-section mx-auto">
                <div class="upload-container">
                    <label for="image-upload" class="upload-label">
                        <img                       
                            src="/assets/images/animation/empty-exclamation.gif"
                            width="150"
                            height="150"
                            class="object-fit-cover"
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
            team['membersCount'] = membersCountServerValue[team?.id] ?? 0;
            html += `
                <a style="cursor:pointer;" class="mx-auto" href="/participant/team/${team?.id}/manage">
                    <div class="wrapper">
                        <div class="team-section">
                            <div class="upload-container text-center">
                                <div class="circle-container" style="cursor: pointer;">
                                    <img
                                        onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                        id="uploaded-image" class="uploaded-image object-fit-cover"
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
                                        (membersCountServerValue[team?.id] < 5 ? 'Status: Public (Apply)' : 'Status: Private')
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
   
    filterSortResultsDiv.innerHTML = html;
}

paintScreen(teamListServerValue, membersCountServerValue, countServerValue);