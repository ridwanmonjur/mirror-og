let csrfToken2 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let tabButtonBalueValue = localStorage.getItem("tab");
let currentTabIndexForNextBack = 0;
if (tabButtonBalueValue !== null || tabButtonBalueValue!== undefined){
    if (tabButtonBalueValue === "PendingMembersBtn") {
        currentTabIndexForNextBack = 1;
    }

    if (tabButtonBalueValue === "NewMembersBtn") {
        currentTabIndexForNextBack = 2;
    }
}

function goBackScreens () {
    if (currentTabIndexForNextBack <=0 ) {
        Toast.fire({
            'icon': 'success',
            'text': 'Notifications sent already!'
        });
    } else {
        let tabs = document.querySelectorAll('.tab-content');
        console.log({tabs, tabsChildren: tabs});
        for (let tabElement of tabs) {
            tabElement.classList.add('d-none');
        }

        currentTabIndexForNextBack--;
        tabs[currentTabIndexForNextBack].classList.remove('d-none');
    }
}

function goNextScreens () {
    if (currentTabIndexForNextBack >= 2) {
        document.getElementById('manageRosterUrl').click();
    } else {

        let tabs = document.querySelectorAll('.tab-content');
        console.log({tabs, tabsChildren: tabs, currentTabIndexForNextBack});

        for (let tabElement of tabs) {
            tabElement.classList.add('d-none');
        }

        currentTabIndexForNextBack++;
        tabs[currentTabIndexForNextBack].classList.remove('d-none');
    }
}

let actionMap = {
    'approve': approveMemberAction,
    'disapprove': disapproveMemberAction,
    'captain': capatainMemberAction,
    'deleteCaptain': deleteCaptainAction,
    'invite': inviteMemberAction,
    'deleteInvite': withdrawInviteMemberAction
};

let dialogForMember = new DialogForMember();




function loadTab() {
    let pageValue = localStorage.getItem('page');

    if (Number(pageValue)) {
        document.getElementById('NewMembersBtn').click();
    }
}

function generateHeaders() {
    return {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        ...window.loadBearerHeader(), 
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };
}


addOnLoad( () => { window.loadMessage(); loadTab(); } )


function reloadUrl(currentUrl, buttonName) {
    if (currentUrl.includes('?')) {
        currentUrl = currentUrl.split('?')[0];
    } 

    localStorage.setItem('success', 'true');
    localStorage.setItem('message', 'Successfully updated user.');
    localStorage.setItem('tab', buttonName);      
    let isRedirect = document.getElementById("isRedirectInput")?.value;
    if (isRedirect) {
        document.getElementById("manageMemberButton")?.click();
        return;
    } 
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

function approveMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('approve')
    window.dialogOpen('Continue with approval?', takeYesAction, takeNoAction)
}

function inviteMember(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('invite')
    window.dialogOpen('Are you sure you want to send invite to this member?', takeYesAction, takeNoAction)
}

function captainMember(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('captain')
    window.dialogOpen('Are you sure you want to this user captain?', takeYesAction, takeNoAction)
}

function deleteCaptain(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('deleteCaptain')
    window.dialogOpen('Are you sure you want to remove this user from captain?', takeYesAction, takeNoAction)
}

function withdrawInviteMember(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('deleteInvite')
    window.dialogOpen('Are you sure you want to delete your invite to this member??', takeYesAction, takeNoAction)
}

function disapproveMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('disapprove')
    window.dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
}


function slideEvents(direction) {
    const eventBoxes = document.querySelectorAll('.event-box');
    const visibleEvents = Array.from(eventBoxes).filter(eventBox => eventBox.style.display !== 'none');
    eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));
    let startIndex = 0;

    if (visibleEvents.length > 0) {
        startIndex = (Array.from(eventBoxes).indexOf(visibleEvents[0]) + direction + eventBoxes.length) % eventBoxes
            .length;
    }

    for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
        const index = (startIndex + i + eventBoxes.length) % eventBoxes.length;
        eventBoxes[index].style.display = 'block';
    }
}

function approveMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl('participantMemberUpdateUrl', memberId);

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'CurrentMembersBtn');
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error accepting member.', error);},  
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'team', 'status' : 'accepted'
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
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'CurrentMembersBtn');
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

async function capatainMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const teamId = dialogForMember.getTeamId();
    const urlTemplate = document.getElementById('participantMemberCaptainUrl').value;
    const url = urlTemplate
        .replace(':memberId', memberId)
        .replace(':id', teamId);
    console.log({
        memberId: dialogForMember.getMemberId(),
        action: dialogForMember.getActionName()
    });

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'CurrentMembersBtn');
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error making captain.', error); }, 
        {   headers: generateHeaders(), }
    );
}

async function deleteCaptainAction() {
    const memberId = dialogForMember.getMemberId();
    const teamId = dialogForMember.getTeamId();
    const urlTemplate = document.getElementById('participantMemberDeleteCaptainUrl').value;
    const url = urlTemplate
        .replace(':memberId', memberId)
        .replace(':id', teamId);
    console.log({
        memberId: dialogForMember.getMemberId(),
        action: dialogForMember.getActionName()
    });

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'CurrentMembersBtn');
            } else {
               toastError(responseData.message);
            }
        },
        function(error) {
            toastError('Error removing captain.', error);
        }, { headers: generateHeaders(), }
    );
}

async function inviteMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const teamId = dialogForMember.getTeamId();
    const urlTemplate = document.getElementById('participantMemberInviteUrl').value;
    const url = urlTemplate.replace(':userId', memberId).replace(':id', teamId);

    fetchData(
        url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'PendingMembersBtn');
            } else {
               toastError(responseData.message);
            }
        },
        function(error) { toastError('Error inviting members.', error); }, 
        {  headers: generateHeaders(),  }
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
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'PendingMembersBtn');
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error deleting invite members.', error);}, 
        {  headers: generateHeaders()  }
    );
}

async function fetchParticipants(event) {
    let input = event.currentTarget;
    let currentUrl = document.getElementById('participantMemberManageUrl').value;

    fetchData(
        url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('participantMemberManageUrl').value;
                reloadUrl(currentUrl, 'NewMembersBtn');
                window.location.replace(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) {
            toastError('Error fetching participants.', error);
        }, {
            headers: generateHeaders(), 
        }
    );
}

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

function redirectToProfilePage(userId) {
    window.location.href = getUrl('publicParticipantViewUrl', userId);
}


let newMembersForm = document.getElementById('newMembersForm');
let newMembersFormKeys = ['sortKeys', 'birthDate', 'region', 'status'];
let sortKeysInput = document.getElementById("sortKeys");
let countries = [];

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


async function fetchCountries () {
    try {
        const data = await storeFetchDataInLocalStorage('/countries');
        if (data?.data) {
            countries = data.data;
            const choices2 = document.getElementById('select2-country2');
            let countriesHtml = "<option value=''";
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

async function fetchMembers(event = null) {
    let route;
    let bodyHtml = '', pageHtml = '';
    let teamId = document.getElementById('teamId')?.value;
    if (event?.target && event.target?.dataset?.url) {
        route = event.target.dataset.url;
    } else {
        route = document.getElementById('membersUrl')?.value;
    }
    
    let formData = new FormData(newMembersForm);

    let links = [];
    data = await fetch(route, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken2
        },
        body: JSON.stringify({
            teamId,
            birthDate: formData.get("birthDate"),
            region: formData.get("region"),
            search: formData.get("search"),
            sortKeys: formData.get("sortKeys"),
            sortType: formData.get("sortType"),
            status: formData.getAll("status")
        })
    });

    data = await data.json();
    
    if (data.success && 'data' in data) {
        users = data?.data?.data;
        links = data?.data?.links;
        for (user of users) {
            bodyHtml+=`
                <tr class="st">
                    <td class="colorless-col px-0 mx-0">
                        <svg 
                            onclick="redirectToProfilePage(${user.id});"
                            class="gear-icon-btn"
                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                            <path
                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                        </svg>
                    </td>
                    <td class="coloured-cell px-1">
                        <div class="player-info">
                            <img 
                                onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                width="45" height="45" 
                                src="/storage/${user.userBanner}"
                                class="mx-2 random-color-circle object-fit-cover rounded-circle"
                            >
                            <span>${user.name}</span>
                        </div>
                    </td>
                    <td class="flag-cell coloured-cell px-3 fs-4">
                        <span>${user.participant.region_flag}</span>
                    </td>
                     <td class="coloured-cell px-3">
                        ${user.is_in_team ?
                            'Team status ' + user.members[0].status
                        :
                            'Not in team'
                        }
                    </td>
                    <td class="colorless-col" style="min-width: 1.875rem;">
                        <div class="gear-icon-btn ${user.is_in_team ? 'd-none' : ''}" onclick="inviteMember('${user.id}', '${teamId}')">
                            <img src="/assets/images/add.png" height="24px" width="24px">
                        </div>
                    </td>
                  
                </tr>
            `;
        }

        for (let link of links) {
            pageHtml += `
                <li
                    data-url='${link.url}'
                    onclick="{ fetchMembers(event); }"  
                    class="page-item ${link.active && 'active'} ${link.url && 'disabled'}" 
                > 
                    <a 
                        onclick="event.preventDefault()"
                        class="page-link ${link.active && 'text-light'}"
                    > 
                        ${link.label}
                    </a>
                </li>
            `;
        }

    }

    let tbodyElement = document.querySelector('#member-table-body tbody');
    tbodyElement.innerHTML = bodyHtml;  
    let pageLinks = document.querySelector('#member-table-links');
    pageLinks.innerHTML = pageHtml; 
};

fetchMembers();
fetchCountries();