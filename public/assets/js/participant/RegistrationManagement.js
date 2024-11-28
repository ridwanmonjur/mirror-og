function submitConfirmCancelForm(event, text, id) {
    let form = event.target.dataset.form;
    window.dialogOpen(text, ()=> {
        document.querySelector(`#${id}.${form}`).submit();
    }, null)
}

let csrfToken5 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let registrationPaymentModalMap = {}; 

function updateInput(input) {
    let ogTotal = Number(input.dataset.totalAmount);
    let total = ogTotal;
    let pending = Number(input.dataset.pendingAmount);
    let modalId = input.dataset.modalId;

    if (!(registrationPaymentModalMap.hasOwnProperty(modalId))) {
        registrationPaymentModalMap[modalId] = 0;
    } 

    let index = registrationPaymentModalMap[modalId];
    let totalLetters = 4;
    let newValue = input.value.replace(/[^\d]/g, '');
    let lettersToTake = index - totalLetters;
    let isMoreThanTotalLetters = lettersToTake >= 0;
    if (isMoreThanTotalLetters) {
        let length = newValue.length;
        newValue = newValue.substr(0, lettersToTake + 3) + '.' + newValue.substr(lettersToTake + 3, 2);
    } else { 
        newValue = newValue.substr(1, 2) + '.' + newValue.substr(3, 2);
    }
   

    if (+newValue >= +pending) {
        newValue = pending.toFixed(2);
    }

    registrationPaymentModalMap[modalId] ++;
    
    input.value = newValue;
    putAmount(input.dataset.modalId, newValue, ogTotal, pending, Number(input.dataset.existingAmount));
}

function keydown(input) {
    if (event.key === "Backspace" || event.key === "Delete") { 
        event.preventDefault();
        document.getElementById('currencyResetInput').click();
    }

    if (event.key.length === 1 && !/\d/.test(event.key)) {
        event.preventDefault();
    }
}

function moveCursorToEnd(input) {
    input.focus(); 
    input.setSelectionRange(input.value.length, input.value.length);
}

function putAmount(modalId, inputValue, total, pending, existing) {
    let putAmountTextSpan = document.querySelector('#payModal' + modalId + ' .putAmountClass');
    let pieChart = document.querySelector('#payModal' + modalId + ' .pie');
    inputValue = Number(inputValue);
    let percent = ((existing + inputValue) * 100) / total; 
    let color = 'red';
    if (percent === 0) {
        color = 'gray';  
    } else if (percent > 50 && percent < 100) {
        color = 'orange';
    } else if (percent >= 100) {
        color = '#179317';
    }

    pieChart.style.setProperty('--c', color);
    pieChart.style.setProperty('--p', percent ? percent : 0);
    pieChart.innerText = percent.toFixed(0) + "%" ;
    putAmountTextSpan.innerText = inputValue.toFixed(2);
}

function resetInput(button) {
    let input = document.querySelector('#payModal' + button.dataset.modalId + " input[name='amount']");
    registrationPaymentModalMap[button.dataset.modalId] = 0;
    input.value = input.defaultValue;
    putAmount(button.dataset.modalId, 0.00, Number(button.dataset.totalAmount), Number(button.dataset.pendingAmount), Number(button.dataset.existingAmount));
}

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}


document.addEventListener("DOMContentLoaded", function() {
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

 

});

addOnLoad(()=> {
    const parents = document.querySelectorAll('.popover-parent');
    parents.forEach((parent) => {
        const contentElement = parent.querySelector(".popover-content");
        const parentElement = parent.querySelector(".popover-button");
        if (contentElement) {
            window.addPopover(parentElement, contentElement, 'mouseenter');
        }
    });
});

let headers = {
    'X-CSRF-TOKEN': csrfToken5,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
};

const bladeData = document.getElementById('blade-data').dataset;

function getData(key) {
    return bladeData[key];
}

function getUrl(name) {
    return bladeData[name + 'Url'];
}

let currentUrl = getUrl('register');

function voteForEvent(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    let {rosterId, voteToQuit} = element.dataset;
    const url = getUrl('vote');

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                localStorage.setItem('success', true);
                localStorage.setItem('message', responseData.message);
                window.location.replace(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) {
            toastError('Error making captain.', error);
        }, {
            headers,
            body: JSON.stringify({
                'roster_id': rosterId,
                'vote_to_quit': voteToQuit == "1" ? true: false,
            })
        }
    );
}

function approveMemberAction(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    let {memberId, joinEventId, userId} = element.dataset;
    const url = getUrl('approve');

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                localStorage.setItem('success', true);
                localStorage.setItem('message', responseData.message);
                window.location.replace(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) {
            toastError('Error making captain.', error);
        }, {
            headers,
            body: JSON.stringify({
                'user_id': userId,
                'join_events_id': joinEventId,
                'team_member_id': memberId,
                'team_id': getData('teamId')
            })
        }
    );
}

async function disapproveMemberAction(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    const url = getUrl('disapprove');
    let {teamId, joinEventId, userId} = element.dataset;
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                localStorage.setItem('success', true);
                localStorage.setItem('message', responseData.message);
                window.location.replace(currentUrl);
            } else {
                toastError(responseData.message)
            }
        },
        function(error) {
            toastError('Error disapproving member.', error);
        }, {
            headers, 
            body: JSON.stringify({
                'team_id': teamId,
                'user_id': userId,
                'join_events_id': joinEventId,
            })
        }
    );
}

async function capatainMemberAction(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    const url = getUrl('rostercaptain');
    let {joinEventId, rosterCaptainId} = element.dataset;
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                localStorage.setItem('success', true);
                localStorage.setItem('message', responseData.message);
                window.location.replace(currentUrl);
            } else {
                toastError(responseData.message)
            }
        },
        function(error) {
            toastError('Error disapproving member.', error);
        }, {
            headers, 
            body: JSON.stringify({
                'roster_captain_id': rosterCaptainId == 0 ? null : rosterCaptainId,
                'join_events_id': joinEventId,
            })
        }
    );
}

function drawEventTable(eventDetails, followCounts) {
    let innerHTML = ``;

    innerHTML+=`
        <div class="mx-3 py-2 px-3 w-75 mx-auto mt-3 mb-3 border-${eventDetails.tier.eventTier} " >
            <div>
                <img 
                    onerror="this.onerror=null; this.src='/assets/images/404.png';"
                    src="${eventDetails?.game?.gameIcon ? '/storage/' + eventDetails.game.gameIcon : '/assets/images/404.png'}"
                    class="object-fit-cover rounded-circle me-1" width="30" height="30"
                >
                <p class=" d-inline my-0 ms-2"> ${eventDetails.eventName}</p>
            </div>
            <div class="d-flex pt-2 justify-content-start">
                <img {!! trustedBladeHandleImageFailureBanner() !!} 
                    src="${eventDetails?.user?.userBanner ? '/storage/' + eventDetails.user.userBanner : '/assets/images/404.png'}"
                    width="30"
                    height="30" class="me-1 object-fit-cover  rounded-circle random-color-circle"
                >
                <div class="ms-2">
                    <small class="d-block py-0 my-0">
                        ${eventDetails.user.name }
                    </small>
                    <small
                        class="p-0 my-0">
                        ${followCounts?? 0} follower${followCounts == 0 || followCounts> 1? 's': ''}
                    </small>
                </div>
            </div>
        </div>
    `;
    return innerHTML;
}



function addRosterMembers(event) {
    event.preventDefault();
    event.stopPropagation();
    let buttonEl = event.currentTarget;
    let { joinEventId } = buttonEl.dataset;
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const rosterMapContainer = document.getElementById('roster-id-list-' + joinEventId);
    const {eventDetails: eventDetailsJSON, membersValue: membersValueJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    let membersValue = JSON.parse(membersValueJSON);

    let { rosterMap: rosterMapJSON } = rosterMapContainer.dataset;
    let rosterMap = JSON.parse(rosterMapJSON);
    
    let modal = document.getElementById('addRosterModal');
    let modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);

    let rosterCount = 0;   
    let modalBody =  modal.querySelector('.modal-body');

    modalBody.innerHTML = `
        ${drawEventTable(eventDetails, followCounts)}
        <table class="responsive table-striped my-2 table mx-auto" style="width: 90%">
            <thead>
                <tr>
                    <th> </th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th> <th>
                </tr>
            </thead>
            <tbody>
                ${membersValue?.map(member => {
                    rosterCount++;
                    if (rosterMap[member.user.id]) return '';
                    return `
                    <tr>
                        <td class="ps-4">
                            <a href="/view/participant/${member.user.id}">
                                <svg class="gear-icon-btn"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path  d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            </a>
                        </td>
                        <td >
                            <img 
                                class="object-fit-cover rounded-circle"
                                src="${
                                    member.user.userBanner ?
                                    '/storage/' + member.user.userBanner
                                    :  '/assets/images/404.png'
                                }" 

                                onerror="this.onerror=null;this.src='/assets/images/404.png';"

                                width="25" height="25"
                            >
                            <span>${member.user.name || ''}</span>
                        </td>
                        <td >${member.user.email || ''}</td>
                        <td >${window.formatDateLuxon(member.created_at)}</td>
                        <td> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" 
                                onclick="approveMemberAction(event);"
                                data-join-event-id="${ joinEventId }"
                                data-user-id="${member.user.id}"
                                class="rounded-circle cursor-pointer gear-icon-btn z-99 border-green" viewBox="0 0 16 16"
                            >
                            <path stroke="green" stroke-width="1.5" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                        </td>
                    </tr>
                `}).join('')}
                
            </tbody>
        </table>
               
    `;

    let teamDisplay = '';
    if (!rosterCount ) {
        teamDisplay = `<p class='text-center mt-3 pt-3'> No roster members left to be added. </p>`; 
    }

    modalBody.innerHTML += teamDisplay;
    
    modalInstance.show();
}

document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('[bootstrapodal]');
    buttons.forEach(button => {
        button.addEventListener('click', () => handleEventMembers(button));
    });
});