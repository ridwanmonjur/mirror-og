let filteredSortedMembers = [];
let countries = [];
let membersJsonInput = document.getElementById('membersJson');
let captainJsonInput = document.getElementById('captainJson');
let membersJson = JSON.parse(membersJsonInput.value);
let captainJson = JSON.parse(captainJsonInput.value);

let newMembersForm = document.getElementById('newMembersForm');
let newMembersFormKeys = ['sortKeys', 'birthDate', 'region', 'status'];
let sortKeysInput = document.getElementById("sortKeys");

function resetInput(name) {
    let formData = new FormData(newMembersForm);
    let newValue = name == "sortKeys" ? [] : ""; 
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

function setSortForFetch(value) {
    const sortByTitleId = document.getElementById('sortByTitleId');
    sortByTitleId.innerText = formatStringUpper(value);
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
        // fetchMembers();
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

    fetchMembers();
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
    let target = null, type = null, value = null; 
    if (event.detail) {
        target = event.detail; 
    }  else {
        target = event.target; 
    }
    
    name = target.name;
    value = target.value;
    type = target.type;

    
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

    targetElemnetParent.innerHTML = '';

    let valuesFormData = formData.getAll(name);
    if (value == "" || (Array.isArray(valuesFormData) && valuesFormData[0] == null )) {
        return;
    }
    
    targetElemnetHeading = document.createElement('span');
    targetElemnetHeading.classList.add('me-2');
    targetElemnetHeading.innerHTML = formatStringUpper(name);
    targetElemnetParent.append(targetElemnetHeading);
    console.log({valuesFormData});
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

async function fetchCountries () {
    try {
        const choices2 = document.getElementById('select2-country2');

        const data = await storeFetchDataInLocalStorage('/countries');
        if (data?.data && choices2) {
            countries = data.data;
            let regionsHtml = "";
            let countriesHtml = "";
            let countriesOptionsHtml = "";
            countriesHtml += "<option value=''>No country</option>";
            // Add default "No country" option

            // Single loop through all countries data
            countries.forEach((value) => {
                if (value.type === 'region') {
                    regionsHtml += `
                        <option value='${value.name}'>${value.emoji_flag} ${value.name}</option>
                    `;
                } else if (value.type === 'country') {
                    countriesOptionsHtml += `
                        <option value='${value.name}'>${value.emoji_flag} ${value.name}</option>
                    `;
                }
            });

            // Add regions optgroup if there are regions
            if (regionsHtml) {
                countriesHtml += "<optgroup label='Regions'>";
                countriesHtml += regionsHtml;
                countriesHtml += "</optgroup>";
            }

            // Add countries optgroup if there are countries
            if (countriesOptionsHtml) {
                countriesHtml += "<optgroup label='Countries'>";
                countriesHtml += countriesOptionsHtml;
                countriesHtml += "</optgroup>";
            }

            if (choices2) {
                choices2.innerHTML = countriesHtml;
            }

        } else {
            errorMessage = "Failed to get data!";
        }
    } catch (error) {
        console.error('Error fetching countries:', error);
    }
}

function sortMembers(arr, sortKey, sortOrder) {
    if (sortKey === "" || sortKey === null || sortOrder === "" || sortOrder === null) {
       const sortByTitleId = document.getElementById('sortByTitleId');
        sortByTitleId.innerText = "Sort By";
      
      return membersJson;  
    }
    
    let arr2 = [...arr];
    console.log({sortKey, sortOrder});

    let sortTypeToKeyMap = {
        "birthDate" : "user.participant.birthday",
        "region" : "user.participant.region",
        "status": "status",
        "name": "user.name",
        "recent": "id"
    };

    arr2 = sortByProperty(arr2, sortTypeToKeyMap[sortKey], sortOrder == "asc");
    return arr2;
}

async function fetchMembers() {
    let bodyHtml = '';

    let formData = new FormData(newMembersForm);
    console.log(membersJson);
    let sortedMembers = sortMembers(membersJson, formData.get("sortKeys"), formData.get("sortType"));
    filteredSortedMembers = [];

    for (let sortedMember of sortedMembers) {
        let isToBeAdded = true;
        let nameFilter = String(formData.get('search')).toLowerCase().trim();
        let regionFilter = formData.get('region');
        let ageFilter = formData.get('birthDate');

        let statusListFilter = formData.getAll('status');
        if (nameFilter != "" && !(
            String(sortedMember?.user?.name).includes(nameFilter) 
        )) {
            isToBeAdded = isToBeAdded && false;
        } 

        if (regionFilter != "" && sortedMember?.user?.participant?.region_name != regionFilter) {
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
    
    for (member of filteredSortedMembers) {
        bodyHtml+=`
        <div class="card border-2 mb-2 bg-white hover-shadow-sm position-relative"
            onmouseover="this.style.transform='translateY(-2px)'" 
            onmouseout="this.style.transform='translateY(0)'"
        >
            <div class="card-body cursor-pointer" onclick="redirectToProfilePage('${member.user_id}', '${member?.user?.slug}');" >
                <div class="row align-items-center">
                    <div class="col-12 col-lg-11 d-flex align-items-center gap-3">
                        <div class="position-relative">
                            
                            <img src="/storage/${member?.user?.userBanner}" 
                                onerror="this.error=null;this.src='/assets/images/404.png';"
                                class="rounded-circle object-fit-cover border border-primary"
                                style="width: 48px; height: 48px;">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-1  py-1">
                                <span class="  ">
                                    <img 
                                        class="z-99 rounded-pill me-1 captain-crown ${captainJson && member.id == captainJson.team_member_id ? ' d-inline-block' : '  d-none '} "
                                        height="20" 
                                        width="20" 
                                        src="/assets/images/participants/crown-straight.png"
                                    >
                                </span>
                                ${member?.user?.name}
                                <span class="fs-4 ms-3">${member?.user?.participant?.region_flag ?? '' }</span>
                                <span class="fw-bold fs-7 text-muted">${member?.user?.participant?.region_name ?? '' }</span>
                            </h6>
                            <td class="text-secondary">${window.formatDateLuxon(member.created_at)}</td>
                            </div>
                    </div>
        
                    <div class="col-12 col-lg-1 d-flex align-items-center my-2 gap-3">
                       
                        <a 
                        class="text-decoration-none  btn btn-sm gear-icon-btn text-body-secondary cursor-pointer">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        `;
    }

    let tbodyElement = document.querySelector('#member-table-body div.team-body');
    tbodyElement.innerHTML = bodyHtml;  
};

addOnLoad(()=> {
    fetchMembers();

})
fetchCountries();

function reddirectToLoginWithIntened(route) {
    route = encodeURIComponent(route);
    let url = document.getElementById('signin_url')?.value;
    url+= `?url=${route}`;
    window.location.href = url;
}

carouselWork();
window.addEventListener('resize', debounce((e) => {
    carouselWork();
}, 250));

const searchInputs = document.querySelectorAll('.search_box input');
const memberTables = document.querySelectorAll('.member-table');

searchInputs.forEach((searchInput, index) => {
    searchInput.addEventListener("input", function() {
        const searchTerm = searchInput.value.toLowerCase();
        const memberRows = memberTables[index].querySelectorAll('tbody tr');

        memberRows.forEach(row => {
            const playerName = row.querySelector('.player-info span')
                .textContent.toLowerCase();

            if (playerName.includes(searchTerm)) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

window.onbeforeunload = function(){window.location.reload();}

function redirectToProfilePage(userId, title) {
    let route = document.getElementById('profile_route').value;
    route = route.replace(':id', userId);
    route = route.replace(':title', title);
    window.location.href = route;
}
