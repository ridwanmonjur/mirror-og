
// Elements
let newTeamsForm = document.getElementById('newTeamsForm');
let filteredSortedTeams = [];
let newTeamsFormKeys = ['sortKeys', 'created_at', 'region', 'region2', 'status', 'membersCount', 'esports_title'];
let sortKeysInput = document.getElementById("sortKeys");
let csrfToken99 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const noResultsDiv = document.getElementById('no-results-div');
const pageLinks = document.getElementById('page-links');
let dialogForMember = new DialogForMember();

let teamListServer = document.getElementById('teamListServer');
let userIdServer = document.getElementById('userIdServer');

let membersCountServer = document.getElementById('membersCountServer');
let countServer = document.getElementById('countServer');
let teamListServerValue = JSON.parse(teamListServer.value);
let membersCountServerValue = JSON.parse(membersCountServer.value);
let countServerValue = Number(countServer.value);
let userIdServerValue = Number(userIdServer.value);
let filterSortResultsDiv = document.getElementById('filter-sort-results');
let isModeMyTeams = true;


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
    let formatted = str
        .replace(/^./, (match) => match.toUpperCase())
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/(_|\s)([a-z])/g, (match, p1, p2) => p1 + p2.toUpperCase())
        .replace(/_/g, ' ');
    return formatted.length > 30 ? formatted.slice(0, 30) + '...' : formatted;

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

    changeTeamsGrid();
}


window.addEventListener('formChange',
    debounce((event) => {
        changeFilterSortUI(event);
        changeTeamsGrid();
    }, 300)
);

newTeamsForm.addEventListener('change',
    debounce((event) => {
        changeFilterSortUI(event);
        changeTeamsGrid();
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

    if (name =="membersCount")  {
        let memberCountDiv = document.querySelector('[data-form-parent="membersCount"]');
        if (value=="0") {
            memberCountDiv.classList.add('d-none');
        } else {
            memberCountDiv.classList.remove('d-none');
        }
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
            'rounded-pill', 'px-2', 'py-0', 'me-1', 
            'category-button', 'text-truncate', 'd-inline-block'
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

        if (targetElemnet.dataset.type === "checkbox") {
            targetElemnet.onclick = function (event2) {
                let target2 = event2.currentTarget; 
                let name2 = target2.dataset.name;
                let value2 = target2.dataset.value;
                let checkbox = document.querySelector(`input[type="checkbox"][name="${name2}"][value="${value2}"]`);
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

const storage = document.querySelector('.team-head-storage');
const routes = {
    allCategories: storage.dataset.allCategories
};

let allCategoriesOg = JSON.parse(routes.allCategories);
let allCategoriesName = allCategoriesOg['byTitle'];
let allCategoriesId = allCategoriesOg['byId'];
console.log(allCategoriesOg)

async function changeTeamsGrid(event = null) {
    if (!isModeMyTeams) {
        fetchTeams();
        return;
    } 

    let formData = new FormData(newTeamsForm);
   
    
    let sortedTeams = sortTeams(teamListServerValue, formData.get("sortKeys"), formData.get("sortType"));

    filteredSortedTeams = [];

    for (let sortedTeam of sortedTeams) {
        let isToBeAdded = true;
        let nameFilter = String(formData.get('search')).toLowerCase();
        // let regionFilter = formData.get('region');
        let regionFilter2 = formData.get('region');
        let esportsTitleFilter = formData.get('esports_title');
        let createdAtFilter = formData.get('created_at');
        let statusListFilter = formData.getAll('status');
        let membersCountFilter = formData.getAll('membersCount');
        if (nameFilter != "" && (
            !String(sortedTeam?.teamName).toLowerCase().includes(nameFilter) 
        )) {
            isToBeAdded = isToBeAdded && false;
        }

      
        if ( regionFilter2 != "" && sortedTeam.country_name != regionFilter2) {
            isToBeAdded = isToBeAdded && false;
        }

        let esports = allCategoriesName[esportsTitleFilter];
        if (esports) {
            if (esportsTitleFilter !== "") {
                const categoryString = sortedTeam?.all_categories || "";
                isToBeAdded = isToBeAdded && categoryString.includes(`|${esports.id}|`);
            }
        }

        if (membersCountFilter != "" && sortedTeam?.membersCount < membersCountFilter) {
            isToBeAdded = isToBeAdded && false;
        }

        let isArrayStatusFilter = statusListFilter && statusListFilter[0] == null;
        for (let statusItemFilter of statusListFilter) {
            let isItemGood = statusItemFilter === "private" && sortedTeam?.membersCount >= 5
                || statusItemFilter === "public" && sortedTeam?.membersCount < 5;

            if (isItemGood) {
                isArrayStatusFilter = true || isArrayFilter;
            }
        }
        isToBeAdded = isArrayStatusFilter && isToBeAdded;

   

        if (createdAtFilter != "" && new Date(sortedTeam?.created_at) < new Date(createdAtFilter)) {
            isToBeAdded = isToBeAdded && false;
        }

        if (isToBeAdded) {
            filteredSortedTeams.push(sortedTeam);
        }

    }

    paintScreen(filteredSortedTeams, membersCountServerValue, countServerValue);
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

function parseAllCategories(userCategories) {
    if (!userCategories || typeof userCategories !== 'string') return [];
    const ids = userCategories
        .split('|')
        .filter(id => id !== '') // Removes leading/trailing empty strings
        .filter(id => !isNaN(id));
  
    return ids
        .map(id => allCategoriesId[id] ?? allCategoriesId[id.toString()] ?? null)
        .filter(Boolean);
}


function setIsMyMode(event) {
    let target = event.currentTarget;
    if (!target) return;
    target.disabled = true;
    let button2 = target.querySelector('#browse-teams-btn');
    let membersBtn = document.getElementById('members-dropdown');
   
    if (!button2) return;
    isModeMyTeams = !isModeMyTeams;
    if (isModeMyTeams) {
        button2.innerText = 'Browse Teams';
        membersBtn.classList.remove('d-none');
        paintScreen(teamListServerValue, membersCountServerValue, countServerValue)
    } else {
        membersBtn.classList.add('d-none');
        button2.innerText = 'Go Back To My Teams';
        fetchTeams();
    }

    setTimeout(() => {
        target.disabled = false;
    }, 3000);

}

function paintScreen(teamListServerValue, membersCountServerValue, countServerValue) {
    let html = ``;
    filterSortResultsDiv.innerHTML = html;
    console.log(teamListServerValue, membersCountServerValue, countServerValue)
    if (countServerValue > 0)  {
        for (let team of teamListServerValue) {
            let all_categories = parseAllCategories(team?.all_categories) || [];

            let allCategoriesTooltip = all_categories
                ?.map(cat => cat.gameTitle)
                .join(', ');

            all_categories = all_categories
                .filter(cat => typeof cat?.gameTitle === 'string')
                .slice(0, 3);
            
            let game = allCategoriesId[team?.default_category_id] ??  null;
            let imgString = game ? `<img width="45" height="45" class="rounded-2 border border-secondary" src="/storage/${game.gameIcon}">` : '';

            let allCategoriesHtml = all_categories.map((element, index, arr) => {
                let title = element.gameTitle.length > 8 
                    ? element.gameTitle.slice(0, 8).trim() + '..' 
                    : element.gameTitle;

                let comma = index < arr.length - 1 ? ',' : '';

                return `
                    <small 
                        class="fw-bold text-nowrap  py-0 my-0  text-truncate mx-0 px-0" 
                        style="max-width: 15ch; font-size: 0.9rem;  overflow: hidden;">
                        ${title}${comma}
                    </small>
                `;
            }).join('');

            if (!all_categories.length) {
                allCategoriesHtml = ` <small class="fw-bold" style="max-width: 15ch;  font-size: 0.9rem; overflow: hidden;">No Games</small>`;
            }

            let statusText = '';
            if (team?.creator_id == userIdServerValue) {
                statusText= `<i class="text-primary">Created by you</i>`;
            } else {
                if (team.member_status) {
                    const status = team.member_status;
                    const actor = team.member_actor;

                    if (status === "accepted") {
                        statusText = `<i class="text-success">You're a team member</i>`;
                    } 
                    else if (status === "pending") {
                        if (actor === "team") {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button onclick="event.preventDefault(); event.stopPropagation(); approveMember(${team.member_id})" style="font-size: 0.875rem;" class="btn rounded-pill btn-success bg-white btn-sm btn-link me-1" type="button">
                                    <span class="text-success">Yes, join team</span>
                                </button>
                                <button onclick="event.preventDefault(); event.stopPropagation(); rejectMember(${team.member_id})" style="font-size: 0.875rem;" class="btn rounded-pill border border-danger bg-white btn-sm btn-link" type="button">
                                    <span class="text-red">Reject</span>
                                </button>
                            </div>
                            `;
                        } else if (actor === "user") {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button style="font-size: 0.875rem;" class="btn rounded-pill btn-primary bg-white btn-sm btn-link" type="button">
                                    <span>Requested</span>
                                </button>
                                <button class="gear-icon-btn mt-0 ms-1" onclick="event.preventDefault(); event.stopPropagation(); withdrawInviteMember(${team.member_id})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                </button>
                            </div>
                            `;
                        }
                    } 
                    else if (status == "left") {
                        if (actor == "team") {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button disabled style="pointer-events: none; border: none;" class="me-2 btn-sm bg-white text-red py-1 px-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                    </svg>
                                    <span>Removed by team</span>
                                </button>
                            </div>
                            `;
                        } else if (actor == "user") {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button class="me-2 btn rounded-pill btn-sm text-red bg-white py-1 px-2" style="border: 1px solid red; pointer-events: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                    </svg>
                                    <span>Left Team</span>
                                </button>
                                <button onclick="event.preventDefault(); event.stopPropagation(); rejoinTean(${team.member_id})" style="font-size: 0.875rem;" class="btn rounded-pill border border-success bg-white btn-sm" type="button">
                                    <span class="text-success">Change decision</span>
                                </button>
                            </div>
                            
                            `;
                        }
                    } 
                    else if (status === "rejected") {
                        if (actor == "team") {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button disabled style="pointer-events: none; border: none;" class="me-2 btn-sm bg-white text-red py-1 px-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                    </svg>
                                    <span>Team Rejected</span>
                                </button>
                            </div>
                            `;
                        } else if (actor == "user") {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button class="me-2 btn rounded-pill btn-sm text-red bg-white py-1 px-2" style="border: 1px solid red; pointer-events: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                    </svg>
                                    <span>You Rejected</span>
                                </button>
                                <button onclick="event.preventDefault(); event.stopPropagation(); approveMember(${team.member_id})" style="font-size: 0.875rem;" class="btn rounded-pill border border-success bg-white btn-sm" type="button">
                                    <span class="text-success">Change decision</span>
                                </button>
                            </div>
                            
                            `;
                        }
                    }
                } else {
                    let membersCount = membersCountServerValue[team?.id] ?? 0;
                    if (membersCount>=10) {
                    statusText = `
                    <div class="d-block d-lg-inline-block pt-1 px-0">
                        <button disabled style="font-size: 0.875rem;" class="btn rounded-pill btn-secondary text-dark px-3 btn-sm" type="button">
                            Full
                        </button>
                    </div>
                    `;
                    } else if (membersCount<5) {
                        statusText = `
                        <div class="d-block d-lg-inline-block pt-1 px-0">
                            <button onclick=" joinTeam(event, ${team.id})" style="font-size: 0.875rem;" class="btn rounded-pill btn-primary text-white px-3 btn-sm" type="button">
                                Join
                            </button>
                        </div>
                        `;
                        } else {
                            statusText = `
                            <div class="d-block d-lg-inline-block pt-1 px-0">
                                <button onclick=" joinTeam(event, ${team.id})" style="font-size: 0.875rem;" class="btn rounded-pill border-primary bg-white text-primary border-3 px-3 btn-sm" type="button">
                                    Apply to Join
                                </button>
                            </div>
                            `;
                        }
                } 

                
            }
           
            
           
            team['membersCount'] = membersCountServerValue[team?.id] ?? 0;
            html += `
                <a style="cursor:pointer;"  href="/participant/team/${team?.id}/manage">
                    <div class="wrapper">
                        <div class="team-section position-relative">
                            <div class="position-absolute top-0 right-0 w-100">
                                <div class="d-flex justify-content-end">${imgString}</div>
                            </div>
                            <div class="upload-container  text-center">
                                <div class="circle-container" style="cursor: pointer;">
                                    <img
                                        onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                        id="uploaded-image" class="uploaded-image border-secondary object-fit-cover"
                                        src="${team?.teamBanner ? '/storage' + '/' + team?.teamBanner : '/assets/images/animations/empty-exclamation.gif' }"
                                    >
                                    </label>
                                </div>
                               
                            </div>
                            <div class="text-center position-relative">
                                <h6  class="team-name  text-wrap " id="team-name">${team?.teamName}</h6>
                                <span> Region: ${team?.country_name ? team?.country_name: '-'} </span>  <br>
                                <div data-bs-toggle="tooltip" title="${allCategoriesTooltip}" class=" px-1 text-nowrap" style="max-width: 270px;">
                                    <small > 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#b4b4b4" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814z"/>
                                    </svg>
                                    ${allCategoriesHtml}
                                    </small>
                                </div>
                                <span> Members:
                                    ${membersCountServerValue[team?.id] ? membersCountServerValue[team?.id] : 0}/10
                                </span> <br>
                                <small style="font-size: 0.9rem;"">${statusText}</small>
                                <br>
                                <span> 
                                 Status: ${team?.status?.charAt(0).toUpperCase() + team?.status?.slice(1).toLowerCase()}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            `;
        }
    } else {
            Swal.fire({
                position: "center",
                icon: 'info',
                confirmButtonColor: '#43a4d7',
                text: 'No teams found by this search. Please change your search.',
            })

            noResultsDiv.classList.remove('d-none');
            pageLinks.classList.add('d-none');
            return;
    }

    noResultsDiv.classList.add('d-none');
    pageLinks.classList.add('d-none');
   
    filterSortResultsDiv.innerHTML = html;

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
        const existingTooltip = window.bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (existingTooltip) {
            existingTooltip.dispose();
        }
        
        return new window.bootstrap.Tooltip(tooltipTriggerEl);
    });
}

addOnLoad(()=> {
    if (isModeMyTeams) {
        paintScreen(teamListServerValue, membersCountServerValue, countServerValue);
    } else {
        fetchTeams();
    }
});


async function fetchTeams(route = null) {
    if (!route) {
        route = '/api/teams/list';
    }

    let formData = new FormData(newTeamsForm);
  
    formData.set('isTeamBrowse', true)
    const selectedTitle = formData.get('esports_title');
    const selectedCategory = allCategoriesName[selectedTitle];
    let esportsTitleId = null;
    
    if (selectedCategory && selectedCategory.id) {
        esportsTitleId = `|${selectedCategory.id}|`;
    }  else{
        esportsTitleId = null;
    }

    data = await fetch(route, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken99,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            region: formData.get('region'),
            esports_title: esportsTitleId,
            created_at: formData.get('created_at') == "" ? null: formData.get('created_at'),
            status: formData.getAll('status'),
            search: formData.get('search'),
            sortKey: formData.get("sortKeys"), 
            sortType: formData.get("sortType")
        })
    });

    data = await data.json();
    console.log(data);
    console.log(data);
    console.log(data);
    
    // if (data.success && 'data' in data) {
    let {teamList, count, membersCount, links} = data?.data;
    paintScreen(teamList, membersCount, count);
    
    let {next_page_url, prev_page_url, has_more} = links;
        let newPagination = [];
        if (prev_page_url) {
            newPagination.push(
                {'url' : prev_page_url, 'label': '&lt; Previous'},
            );
        }

        if (next_page_url) {
            newPagination.push(
                {'url' : next_page_url, 'label': 'Next &gt;'},
            );
        }
          
        links = [...newPagination];

        if (!teamList[0]) {
            Swal.fire({
                position: "center",
                icon: 'info',
                confirmButtonColor: '#43a4d7',
                text: 'No teams found by this search. Please change your search.',
            })

            noResultsDiv.classList.remove('d-none');
            pageLinks.classList.add('d-none');
            return;
        }

        let pageHtml = '';
        for (let link of links) {
            pageHtml += `
                <li
                    data-url='${link.url}'
                    onclick="fetchTeams('${link.url}');"  
                    class=" mt-4 border  border-secondary  mx-auto text-center rounded-3"
                    style="width: 120px;"
                    type='button' 
                > 
                <a class="page-link">${link.label}</a>
                </li>
            `;
        }

        noResultsDiv.classList.add('d-none');
        pageLinks.innerHTML = pageHtml;
        pageLinks.classList.remove('d-none');
    
};

// pagination + sorting + buttons + swal



let actionMap = {
    'approve': approveMemberAction,
    'disapprove': disapproveMemberAction,
    'deleteInvite': withdrawInviteMemberAction,
    'reject': rejectMemberAction,
};

let csrfToken77 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function generateHeaders() {
    return {
        'X-CSRF-TOKEN': csrfToken77,
        'credentials': 'include', 
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };
}


addOnLoad( () => { window.loadMessage(); } )
function reloadUrl(currentUrl) {
    if (currentUrl.includes('?')) {
        currentUrl = currentUrl.split('?')[0];
    } 

    localStorage.setItem('success', 'true');
    localStorage.setItem('message', 'Successfully updated team.');
    window.location.replace(currentUrl);
}

function toastError(message, error = null) {
    console.error(error)
    Toast.fire({
        icon: 'error',
        text: message
    });
}

function takeYesAction() {
    console.log({
        memberId: dialogForMember.getMemberId(),
        action: dialogForMember.getActionName()
    })

    const actionFunction = actionMap[dialogForMember.getActionName()];
    if (actionFunction) {
        actionFunction();
    } else {
        Toast.fire({
            icon: 'error',
            text: "No action found."
        })
    }
} 

function takeNoAction() {
    dialogForMember.reset();
}

function joinTeam(event, teamId) {
    event.preventDefault(); 
    event.stopPropagation();
    let button = event.currentTarget;
    window.dialogOpen('Send join request?', ()=> {
        const url = getUrl("memberPendingUrl", teamId);
        fetchData(url,
            function(responseData) {
                if (responseData.success) {
                    button.classList.remove('btn-primary');
                    button.classList.remove('text-white');
                    button.classList.add('btn-success');
                    button.classList.add('text-dark');
                    button.innerText = "Requested";
                    button.disabled = true;
                } else {
                    toastError(responseData.message)
                }
            },
            function(error) { toastError('Error joining team.', error);}, 
            {
                headers: generateHeaders(), 
                body: JSON.stringify({
                   'actor' : 'user', 'status' : 'pending'
                })
            }
        );
    }, ()=> {

    }, {
        innerHTML: "<strong class='text-success'>Do you want to join this team?</strong><br><em class='text-muted'>You can join and take part in events with them.</em><br><span class='text-muted'>This action will send a notification to the team creator.</span>"
    })
}

function approveMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('approve')
    window.dialogOpen('Accept Team Request?', takeYesAction, takeNoAction, {
        innerHTML: "<strong class='text-success'>Do you want to approve this member to your team?</strong><br><em class='text-muted'>They can join and take part in events with you.</em><br><span class='text-muted'>This action will send a notification to the member.</span>"
    })
}

function rejoinTean(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('approve')
    window.dialogOpen('Rejoin Team?', takeYesAction, takeNoAction, {
        innerHTML: "<strong class='text-success'>Do you want to rejoin your ex-team?</strong><br><em class='text-muted'>You can take part in events with them.</em><br>"
    })
}

function withdrawInviteMember(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('deleteInvite')
    window.dialogOpen('Withdraw Join Request?', takeYesAction, takeNoAction, {
        innerHTML: "<strong class='text-secondary'>Do you want to cancel your invitation to this user?</strong><br><em class='text-muted'>The pending invitation will be removed.</em><br><span class='text-muted'>The user will no longer see your team invitation.</span>"
    })
}

function disapproveMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('disapprove')
    window.dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction, {
        innerHTML: "<strong class='text-red'>Do you want to disapprove this member's request?</strong><br><em class='text-muted'>They will not be added to your team.</em><br><span class='text-muted'>This action will notify the member of the decision.</span>"
    })
}

function rejectMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('reject')
    window.dialogOpen('Reject join invitation?', takeYesAction, takeNoAction, {
        innerHTML: "<strong class='text-red'>Do you want to reject this member from your team?</strong><br><em class='text-muted'>This will permanently decline their request.</em><br><span class='text-muted'>This action cannot be undone and will notify the member.</span>"
    })
}


function approveMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl('participantMemberUpdateUrl', memberId);

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error accepting member.', error);},  
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'user', 'status' : 'accepted'
            })
        }
    );
}

async function disapproveMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl("participantMemberUpdateUrl", memberId);
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message)
            }
        },
        function(error) { toastError('Error disapproving member.', error);}, 
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'team', 'status' : 'left'
            })
        }
    );
}

async function rejectMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl("participantMemberUpdateUrl", memberId);
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message)
            }
        },
        function(error) { toastError('Error disapproving member.', error);}, 
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'user', 'status' : 'rejected'
            })
        }
    );
}


async function withdrawInviteMemberAction() {
    const memberId = dialogForMember.getMemberId();
    
    const urlTemplate = document.getElementById('participantMemberDeleteInviteUrl').value;
    const url = urlTemplate.replace(':id', memberId);

    fetchData(
        url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error deleting invite members.', error);}, 
        {  headers: generateHeaders()  }
    );
}

