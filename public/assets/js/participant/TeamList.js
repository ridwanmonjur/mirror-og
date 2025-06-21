
// Elements
let newTeamsForm = document.getElementById('newTeamsForm');
let filteredSortedTeams = [];
let newTeamsFormKeys = ['sortKeys', 'created_at', 'region', 'region2', 'status', 'membersCount', 'esports_title'];
let sortKeysInput = document.getElementById("sortKeys");
let csrfToken99 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
        let nameFilter = String(formData.get('search')).toLowerCase().trim();
        // let regionFilter = formData.get('region');
        let regionFilter2 = formData.get('region');
        let esportsTitleFilter = formData.get('esports_title');
        let createdAtFilter = formData.get('created_at');
        let statusListFilter = formData.getAll('status');
        let membersCountFilter = formData.getAll('membersCount');

        if (nameFilter != "" && !(
            String(sortedTeam?.teamName).includes(nameFilter) 
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

            let allCategoriesHtml = all_categories.map((element, index, arr) => {
                let title = element.gameTitle.length > 8 
                    ? element.gameTitle.slice(0, 8) + '..' 
                    : element.gameTitle;

                let comma = index < arr.length - 1 ? ',' : '';

                return `
                    <small 
                        class="btn text-nowrap btn-link btn-sm d-inline-block text-truncate mx-0 px-0" 
                        style="max-width: 15ch; overflow: hidden;">
                        ${title}${comma}
                    </small>
                `;
            }).join('');

            if (!all_categories.length) {
                allCategoriesHtml = `No Games`;
            }

            
           
            team['membersCount'] = membersCountServerValue[team?.id] ?? 0;
            html += `
                <a style="cursor:pointer;"  href="/participant/team/${team?.id}/manage">
                    <div class="wrapper">
                        <div class="team-section">
                            <div class="upload-container text-center">
                                <div class="circle-container" style="cursor: pointer;">
                                    <img
                                        onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                        id="uploaded-image" class="uploaded-image border-secondary object-fit-cover"
                                        src="${team?.teamBanner ? '/storage' + '/' + team?.teamBanner : '/assets/images/animations/empty-exclamation.gif' }"
                                    >
                                    </label>
                                </div>
                                <div>
                                </div>
                            </div>
                            <div class="text-center position-relative">
                                <h6  class="team-name  text-wrap " id="team-name">${team?.teamName}</h6>
                                <span> Region: ${team?.country_name ? team?.country_name: '-'} </span>  <br>
                                <div data-bs-toggle="tooltip" title="${allCategoriesTooltip}" class=" px-1 text-nowrap" style="max-width: 270px;">
                                    <span class="text-primary">Games: </span> ${allCategoriesHtml}
                                </div>
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
    paintScreen(teamListServerValue, membersCountServerValue, countServerValue);

});


async function fetchTeams(event = null) {
    let route;
    let bodyHtml = '', pageHtml = '';

    route = '/api/teams/list';
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

    let links = [];
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
        })
    });

    data = await data.json();
    console.log(data);
    console.log(data);
    console.log(data);
    
    // if (data.success && 'data' in data) {
        let {teamList, count, membersCount} = data?.data;
        paintScreen(teamList, membersCount, count);
    // }
    //     let {next_page_url, prev_page_url} = data?.data;
    //     let newPagination = [];
    //     if (prev_page_url) {
    //         newPagination.push(
    //             {'url' : prev_page_url, 'label': 'Previous Page'},
    //         );
    //     }

    //     if (next_page_url) {
    //         newPagination.push(
    //             {'url' : next_page_url, 'label': 'Next Page'},
    //         );
    //     }
          
    //     links = [...newPagination];

    //     if (!users[0]) {
    //         Swal.fire({
    //             position: "center",
    //             icon: 'info',
    //             confirmButtonColor: '#43a4d7',
    //             text: 'No users found by this search. Please change your search.',
    //         })
    //     }

    //     for (user of users) {
    //         bodyHtml+=`
    //             <tr class="st py-2">
    //                 <td class="colorless-col px-0 mx-0   cursor-pointer  ">
    //                     <svg 
    //                         onclick="redirectToProfilePage(${user.id}, ${user.slug});"
    //                         class="gear-icon-btn"
    //                         xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
    //                         class="bi bi-eye-fill" viewBox="0 0 16 16">
    //                         <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
    //                         <path
    //                             d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
    //                     </svg>
    //                 </td>
    //                 <td class="colored-cell px-1   cursor-pointer  " onclick="redirectToProfilePage(${user.id}, ${user.slug});">
    //                     <div class="player-info">
    //                         <img 
    //                             onerror="this.onerror=null;this.src='/assets/images/404.png';"
    //                             width="45" height="45" 
    //                             src="/storage/${user.userBanner}"
    //                             class="mx-2 my-1 border border-2 border-secondary object-fit-cover rounded-circle"
    //                         >
    //                         <span>${user.name}</span>
    //                     </div>
    //                 </td>
    //                 <td class="flag-cell colored-cell text-start text-lg-center px-3 fs-4">
    //                     <span>${user?.participant?.region_flag? user.participant.region_flag: '-'}</span>
    //                 </td>
    //                  <td class="colored-cell px-3">
    //                     ${user.is_in_team ?
    //                         'Team status ' + user.members[0].status
    //                     :
    //                         'Not in team'
    //                     }
    //                 </td>
    //                 <td class="colorless-col ps-0 pe-2 py-2 text-start text-lg-center" style="min-width: 1.875rem;">
    //                     <div class="gear-icon-btn ${user.is_in_team ? 'd-none' : ''}" onclick="inviteMember('${user.id}', '${teamId}')">
    //                         <img src="/assets/images/add.png" height="24px" width="24px">
    //                     </div>
    //                 </td>
                  
    //             </tr>
    //         `;
    //     }

    //     for (let link of links) {
    //         pageHtml += `
    //             <li
    //                 data-url='${link.url}'
    //                 onclick="{ fetchMembers(event); }"  
    //                 class="page-item " 
    //             > 
    //                 <a 
    //                     onclick="event.preventDefault()"
    //                     class="page-link"
    //                 > 
    //                     ${link.label}
    //                 </a>
    //             </li>
    //         `;
    //     }

    // }

    // let tbodyElement = document.querySelector('#member-table-body tbody');
    // tbodyElement.innerHTML = bodyHtml;  
    // let pageLinks = document.querySelector('#member-table-links');
    // pageLinks.innerHTML = pageHtml; 
};