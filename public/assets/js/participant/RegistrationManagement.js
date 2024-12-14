
let ROSTER_STATUS_ENUMS = {
    'ROSTER_APPROVE': 1,
    'ROSTER_DISAPPROVE': 2,
    'VOTE_QUIT': 3,
    'VOTE_STAY': 4,
    'CAPTAIN_APPROVE': 5,
    'CAPTAIN_REMOVE': 6,
};

const successSwal = (html = '', cb) => {
    return Swal.fire({
        html,
        showConfirmButton: true,
        confirmButtonText: 'Ok',
        confirmButtonColor: '#52A8D5',
        showClass: {
            popup: 'animate__animated animate__fadeIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
        }
    }).then(() => {
        cb();
    });
};


const actionResponse = { 
    [ROSTER_STATUS_ENUMS.ROSTER_APPROVE]: respondRosterApprove,
    [ROSTER_STATUS_ENUMS.ROSTER_DISAPPROVE]: respondRosterDisapprove,
    [ROSTER_STATUS_ENUMS.VOTE_QUIT]: voteYes,
    [ROSTER_STATUS_ENUMS.VOTE_STAY]: voteNo,
    [ROSTER_STATUS_ENUMS.CAPTAIN_APPROVE]: captainApprove,
    [ROSTER_STATUS_ENUMS.CAPTAIN_REMOVE]: captanRemove,
};

function respondRosterApprove(joinEventId) {
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {
        eventDetails: eventDetailsJSON, 
        followCounts,
    } = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    
    successSwal(`
        <h5 class="my-4">You have added this user to the roster!</h5>
        ${drawEventTable(eventDetails, followCounts)}
    `, () => scrollSwal(joinEventId));
}

function respondRosterDisapprove(joinEventId) {
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {
        eventDetails: eventDetailsJSON, 
        followCounts,
    } = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    
    successSwal(`
        <h5 class="my-4">You have removed this user from the roster!</h5>
        ${drawEventTable(eventDetails, followCounts)}
    `, () => scrollSwal(joinEventId));
}

function voteYes(joinEventId) {
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    
    successSwal(`
        <h5 class="my-4">You have voted to stay in this event!</h5>
        ${drawEventTable(eventDetails, followCounts)}
    `, () => scrollSwal(joinEventId));
}

function voteNo(joinEventId) {
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    
    successSwal(`
        <h5 class="my-4">You have voted to leave this roster!</h5>
        ${drawEventTable(eventDetails, followCounts)}
        
    `, () => scrollSwal(joinEventId));
}

function captainApprove(joinEventId) {
    successSwal(`
        <p class="mt-4 mb-0 pb-0">You have assigned a new captain!</p>
       
    `, () => scrollSwal(joinEventId));
}

function captanRemove(joinEventId) {
 
    successSwal(`
        <p  class="mt-4 mb-0 pb-0">You have removed the captaincy successfully</p>
    `, () => scrollSwal(joinEventId));
}

function confirmSuccess(successMessage, joinEventId) {
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    console.log({memberDataContainer, successMessage, joinEventId});
    const {eventDetails: eventDetailsJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    
    successSwal(`
        <h5 class="my-4">Success</h5>
        ${drawEventTable(eventDetails, followCounts)}
        <p class="text-center text-primary my-4"> ${successMessage} </p>
        <p class="text-center"> The event starts on ${window.formatDateMySqlLuxon(eventDetails.startDate, eventDetails.startTime)} </p>
    `, () => scrollSwal(joinEventId));
}


function rosterCountCaptainHtmlGenerater(roster, rosterCaptainId) {
    const count = roster.length;
    return `
        <div class="roster-container">
            <div class="my-3 text-start">Current roster (${count} /${getData('maxRosterSize')})</div>
            ${roster.map(player => 
                `<div class="d-flex align-items-center gap-2 my-2">
                    <img
                        width="20"
                        height="20"
                        onerror="this.onerror = null; this.src= '/assets/images/404.png';"
                        class="rounded-circle random-color-circle"
                        src="${player?.user?.userBanner ? '/storage/' + player.user.userBanner : '/assets/images/404.png'}"
                    >
                    <small>
                        ${player.user.name}
                        ${player.id == rosterCaptainId ? `
                            <img 
                            class="z-99 rounded-pill me-1 gear-icon-btn"
                            height="20" 
                            width="20" 
                            src="{{asset('assets/images/participants/crown-straight.png')}}"
                            >

                        ` : ''}
                    </small>
                </div>`
            ).join('')}
        </div>`;
}

function rosterHtmlGenerater (roster) {
    return roster.map(player => 
        `<div class="d-flex align-items-center gap-2 mb-2">
            <img
                width="25"
                height="25"
                onerror="this.onerror = null; this.src= '/assets/images/404.png';"
                class="  rounded-circle random-color-circle"
                src="${player?.user?.userBanner ? '/storage/' + player.user.userBanner : '/assets/images/404.png'}"
            >
            <span>${player.user.name}</span>
        </div>`
    ).join('')
};

function registrationManage(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    let registrationStatusEnum = {
        'OVER' : 1,
        'EARLY' : 2,
        'NORMAL' : 3,
        'TOO_EARLY' : 4
    };

    let {modalId, joinEventId, status} = element.dataset;

    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const rosterMapContainer = document.getElementById('roster-id-list-' + joinEventId);
    const {eventDetails: eventDetailsJSON, membersValue: membersValueJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    let membersValue = JSON.parse(membersValueJSON);
    let { rosterMap: rosterMapJSON } = rosterMapContainer.dataset;
    let rosterMap = JSON.parse(rosterMapJSON);

    if (status == registrationStatusEnum['NORMAL'] 
    ) {
        showNormalRegistration();
    }

    else if (
        (status == registrationStatusEnum['EARLY'])
        || status == registrationStatusEnum['TOO_EARLY']
    ) {
        showRegistrationSteps();
    }

    function showRegistrationSteps() {
        Swal.fire({
            html: `
                <h5 class="my-4"> Normal Registration VS Early Registration </h5>
                <p class="text-muted mb-3">When you register normally, all entry fees will be refunded in full if the event falls through. However, if slots are filling up quickly, you may lose your chance to secure a spot in the event.</p>
                <p class="text-muted">Alternatively, you can register in advance, which allows you to lock in your slot immediately. If the event falls through, your entry fees will be consumed, but you will automatically receive a coupon as full reimbursement for your next event.</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Okay, next',
            cancelButtonText: 'Cancel',
            confirmButtonColor: "#43A4D7",

        }).then((result) => {
            if (result.isConfirmed) {
                showRosterConfirmation();
            }
        });
    }

    function showRosterConfirmation() {
        Swal.fire({
            html: `
                <h5 class="my-4"> Confirm your registration in advance? </h5>
                ${drawEventTable(eventDetails, followCounts)}
                <p class="text-muted mb-3">Confirming your registration now will lock in the current roster as your team's official roster for this event and secure your slot.</p>
                <p class="text-muted mb-2">The following players are in the current roster for this event:</p>
                <div class="border rounded p-3 text-start">
                    ${rosterHtmlGenerater(membersValue)}
                </div>
                <p class="text-primary mt-3 mb-0">Lock in this roster and confirm your registration now?</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm registration now',
            cancelButtonText: 'Wait, go back',
            confirmButtonColor: "#43A4D7",

        }).then((result) => {
            if (result.isConfirmed) {
                const modal = document.querySelector(modalId);
    
                if (modal) {
                    const bootstrapModal =  bootstrap.Modal.getInstance(modal);
                    bootstrapModal.show();
                }
            }

            if (result.isDismissed) {
                showRegistrationSteps();
            }
        });
    }
    
    
    function showNormalRegistration() {
        Swal.fire({
            html: `
                <h5 class="my-4"> Confirm your registration? </h5>
                ${drawEventTable(eventDetails, followCounts)}
                <p class="text-muted mb-3">Confirming your registration now will lock in the current roster as your team's official roster for this event.</p>
                <p class="text-muted mb-2">The following players are in the current roster for this event:</p>
                <div class="border rounded p-3 text-start">
                    ${rosterHtmlGenerater(membersValue)}
                </div>
                <p class="text-primary mt-3 mb-0">Lock in this roster and confirm your registration now?</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm registration now',
            cancelButtonText: 'Wait, go back',
            confirmButtonColor: "#43A4D7",

        }).then((result) => {
            if (result.isConfirmed) {
                const modal = document.querySelector(modalId);
    
                if (modal) {
                    const bootstrapModal =  bootstrap.Modal.getInstance(modal);
                    bootstrapModal.show();
                }
            }

            if (result.isDismissed) {
                showRegistrationSteps();
            }
        });
    }
    
    
}

function submitConfirmCancelForm(event) {
    let registrationStatusEnum = {
        'OVER' : 1,
        'EARLY' : 2,
        'NORMAL' : 3,
        'TOO_EARLY' : 4
    };
    
    event.preventDefault();
    event.stopPropagation();
    let buttonEl = event.currentTarget;
    let { joinEventId, form, cancel, joinStatus, registrationStatus } = buttonEl.dataset;
    cancel = cancel == "1" ? true: false;

    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, membersValue: membersValueJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    let membersValue = JSON.parse(membersValueJSON);
    
    function submitForm() {
        document.querySelector(`.${form}`).submit();
    }
   
    if (cancel) {
        if (joinStatus=== "canceled") {
            toastError("Canceled already");
            return;
        }

        console.log({joinStatus});

        showQuitEventPopup(joinStatus == "confirmed");
    } else {
        showRegistrationSteps();
    }
    
    function showQuitEventPopup(isConfirmed = false) {
        const message = isConfirmed
        ? 'The registration for this event has already been confirmed. Any paid entry fees will NOT be refunded upon cancellation of registration.'
        : 'The registration for this event has not been confirmed. Any paid entry fees will be refunded in full upon cancellation of registration.';
        
        const messageColor = isConfirmed ? 'red' : '#43a4d7';
    
        Swal.fire({
            html: `
                <h5 class="my-4">Call a vote to quit this event? </h5>
                ${drawEventTable(eventDetails, followCounts)}

                <p style="color: ${messageColor}; margin-bottom: 15px; font-size: 0.9em;">
                    ${message}
                </p>
                <p style="color: #666; font-size: 0.9em;">
                    A vote to quit can only be called once per event. All players in the current roster must vote. Results will be decided by the majority vote after all votes have been casted.
                </p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, call a vote to quit',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#43a4d7',
            cancelButtonColor: '#999',
            customClass: {
                popup: 'custom-popup-class',
                confirmButton: 'custom-confirm-button',
                cancelButton: 'custom-cancel-button'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });;
    }

    function showRegistrationSteps() {
        Swal.fire({
            html: `
                <h5 class="my-4"> Normal Registration VS Early Registration </h5>
                <p class="text-muted mb-3">When you register normally, all entry fees will be refunded in full if the event falls through. However, if slots are filling up quickly, you may lose your chance to secure a spot in the event.</p>
                <p class="text-muted">Alternatively, you can register in advance, which allows you to lock in your slot immediately. If the event falls through, your entry fees will be consumed, but you will automatically receive a coupon as full reimbursement for your next event.</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Okay, next',
            cancelButtonText: 'Cancel',
            confirmButtonColor: "#43A4D7",

        }).then((result) => {
            if (result.isConfirmed) {
                if (registrationStatus == registrationStatusEnum['NORMAL']) {
                    showNormalRegistration();
                }
            
                if (  registrationStatus == registrationStatusEnum['EARLY']
                    || registrationStatus == registrationStatusEnum['TOO_EARLY']
                ) {
                    showEarlyRosterConfirmation();
                }
            }
        });
    }

    function showEarlyRosterConfirmation() {
        Swal.fire({
            html: `
                <h5 class="my-4"> Confirm your registration in advance? </h5>
                ${drawEventTable(eventDetails, followCounts)}
                <p class="text-muted mb-3">Confirming your registration now will lock in the current roster as your team's official roster for this event and secure your slot.</p>
                <p class="text-muted mb-2">The following players are in the current roster for this event:</p>
                <div class="border rounded p-3 text-start">
                    ${rosterHtmlGenerater(membersValue)}
                </div>
                <p class="text-primary mt-3 mb-0">Lock in this roster and confirm your registration now?</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm registration now',
            cancelButtonText: 'Wait, go back',
            confirmButtonColor: "#43A4D7",

        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }

            if (result.isDismissed) {
                showRegistrationSteps();
            }
        });
    }
    
    
    function showNormalRegistration() {
        Swal.fire({
            html: `
                <h5 class="my-4"> Confirm your registration? </h5>
                ${drawEventTable(eventDetails, followCounts)}
                <p class="text-muted mb-3">Confirming your registration now will lock in the current roster as your team's official roster for this event.</p>
                <p class="text-muted mb-2">The following players are in the current roster for this event:</p>
                <div class="border rounded p-3 text-start">
                    ${rosterHtmlGenerater(membersValue)}
                </div>
                <p class="text-primary mt-3 mb-0">Lock in this roster and confirm your registration now?</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm registration now',
            cancelButtonText: 'Wait, go back',
            confirmButtonColor: "#43A4D7",

        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }

            if (result.isDismissed) {
                showRegistrationSteps();
            }
        });
    }

}

let csrfToken5 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let registrationPaymentModalMap = {}; 

function updateInput(input) {
    let ogTotal = Number(input.dataset.totalAmount);
    let pending = Number(input.dataset.pendingAmount);
    let modalId = input.dataset.modalId;
    let minimumRMValue = getData('paymentLower');

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
    if (newValue > Number(minimumRMValue)) {
        let paymentProceedButton = document.getElementById('paymentProceedButton');
        if (paymentProceedButton.classList.contains('btn-secondary')) {
            paymentProceedButton.classList.remove('btn-secondary');
            paymentProceedButton.classList.add('btn-primary');
        }
    }

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
    let paymentProceedButton = document.getElementById('paymentProceedButton');
    if (paymentProceedButton.classList.contains('btn-primary')) {
        paymentProceedButton.classList.remove('btn-primary');
        paymentProceedButton.classList.add('btn-secondary');
    }

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

    let savedId = localStorage.getItem('scroll');
    if (!savedId) savedId = getData('scroll') ;
    const swal = localStorage.getItem("swal");
    let successMessage = '', errorMessage = '';

    if (swal) {
        actionResponse[swal]?.(savedId);
    } else {
        console.log({savedId});
        console.log({savedId});
        console.log({savedId});
        successMessage = getData('successMessage') || null;
        errorMessage = getData('errorMessage') || null;
        if (successMessage) {
            confirmSuccess(successMessage, savedId);
        } else if (errorMessage) {
            Swal.fire({
                icon: 'error',
                text: errorMessage,
                timer: 1500,
                showConfirmButton: false
            });
        }

    }

   
    
    // localStorage.clear();
   

    function initializeRosterTourGuide() {

        const rosterItems = document.querySelectorAll('.members-hover');
        let currentIndex = 0;
        let isAutoPlaying = false;
        let autoPlayInterval;
      
        const playButton = document.createElement('button');
        playButton.id = 'playButton';
        playButton.classList.add('btn', 'btn-primary', 'text-light', 'px-2', 'py-2', 'btn-sm', 'position-fixed', 'top-50', 'end-0', 'translate-middle-y', 'me-0', 'shadow-sm');        
        playButton.innerHTML = 'Roster Tour';
        playButton.style.zIndex = '1043';
        document.body.appendChild(playButton);
      
        function highlightMember(index) {
        
            console.log(index)
            rosterItems.forEach(item => {
                if (item.classList.contains('d-none-until-hover-parent')) {
                    item.classList.remove('d-none-until-hover-parent');
                    item.classList.add('d-none-until-hover-parent2');
                }

                item.style.border = 'none';
                item.style.borderRadius = '';
                item.style.transition = '';
                item.style.paddingLeft = '5px';
                item.style.paddingRight = '5px';
                item.style.paddingTop = '2px';
                item.style.paddingBottom = '2px';
            });
      
          const item = rosterItems[index];
          if (!item) return;
          item.style.zIndex = '1050';
          item.style.position = 'relative';
          item.style.backgroundColor = 'white';
          item.style.border = '2px solid #43A4D7';
          item.style.borderRadius = '4px';
          item.style.transition = 'all 0.3s ease';
      
          item.scrollIntoView({ behavior: 'smooth', block: 'center' });
          
          if (index == 0) return;
          const prevItem = rosterItems[index-1];
          prevItem.style.backgroundColor = 'transparent';
          prevItem.style.zIndex = '1040';
          prevItem.style.position = 'relative';
          prevItem.style.border = '2px solid none';

        }
      
        function clearHighlight() {
          rosterItems.forEach(item => {
            item.style.border = 'none';
            item.style.borderRadius = '';
            if (item.classList.contains('d-none-until-hover-parent2')) {
                item.classList.add('d-none-until-hover-parent');
            }
          });

          removeBackdrop();
        }
      
        function startAutoPlay() {
          if (isAutoPlaying) return;
          
          isAutoPlaying = true;
          playButton.innerHTML = ' Stop Tour';
          playButton.classList.replace('btn-primary', 'btn-danger');
          createBackdrop();

          highlightMember(currentIndex);
          
          autoPlayInterval = setInterval(() => {
            currentIndex = currentIndex + 1;
            if (currentIndex > rosterItems.length) {
                stopAutoPlay();
                return;
            }

            highlightMember(currentIndex);
          }, 2000);
        }

        function createBackdrop() {
            let backdrop = document.createElement('div');
            backdrop.id = 'highlight-backdrop';
            backdrop.style.position = 'fixed';
            backdrop.style.top = '0';
            backdrop.style.left = '0';
            backdrop.style.width = '100%';
            backdrop.style.height = '100%';
            backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            backdrop.style.zIndex = '1040';  // Bootstrap modal backdrop z-index
            backdrop.style.transition = 'opacity 0.3s ease';
            document.body.appendChild(backdrop);
        }

        function removeBackdrop() {
            const backdrop = document.getElementById('highlight-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        
            rosterItems.forEach(item => {
                item.style.backgroundColor = 'transparent';
                item.style.zIndex = '1040';
                item.style.opacity = "1";
                item.style.position = 'relative';
                item.style.border = 'none';  
            });
        }
      
        function stopAutoPlay() {
          if (!isAutoPlaying) return;
          
          isAutoPlaying = false;
          playButton.innerHTML = 'Roster Management Tour';
          playButton.classList.replace('btn-danger', 'btn-primary');
          clearInterval(autoPlayInterval);
          clearHighlight();
          removeBackdrop();
          currentIndex = 0;
        }
      
        playButton.addEventListener('click', () => {
        console.log(isAutoPlaying);
        console.log(isAutoPlaying);
          if (isAutoPlaying) {
            stopAutoPlay();
          } else {
            console.log("hitt2");

            console.log("hitt2");
            startAutoPlay();
          }
        });
      }

      
      initializeRosterTourGuide();

      document.querySelectorAll('.tutorial-button').forEach(button3 => {
        button3.addEventListener('click', () => {
          document.getElementById('playButton').click();
        });
      });

});

function scrollSwal(savedId) {
    if (savedId) {
        const memberDataContainer = document.getElementById('reg-member-id-' + savedId);
        console.log(savedId, memberDataContainer);
        console.log(savedId, memberDataContainer);
        if (memberDataContainer) {
            memberDataContainer.scrollIntoView({ 
                behavior: 'smooth',
                block: 'center'
            });
            
        }
    }

    localStorage.clear();
}

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

let captainMainFormHtml = `<p class='text-center'>
        The captain will be the main form of all  
        event-related communication. 
    </p>
    `;

function voteForEvent(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    let {rosterId, voteToQuit, joinEventId} = element.dataset;
    const url = getUrl('vote');
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    Swal.fire({
        html: `
            <h5> Have you confirmed your decision to ${voteToQuit ? 'leave': 'stay' }? </h5>
            ${drawEventTable(eventDetails, followCounts)}
        `,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        confirmButtonColor: "#43A4D7",
    }).then((result) => {
        if (result.isConfirmed) {
            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        localStorage.setItem('swal', voteToQuit ? 
                            ROSTER_STATUS_ENUMS.VOTE_QUIT
                            : ROSTER_STATUS_ENUMS.VOTE_STAY
                        );
                        localStorage.setItem('message', responseData.message);
                        localStorage.setItem('scroll', joinEventId);
        
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

        } else if (result.isDenied) {
        }
    })
    
}

function approveMemberAction(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    let loggedUserId = getData('userId');
    let {joinEventId,  userId} = element.dataset;
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, 
        membersValue: membersValueJSON, 
        followCounts,
        rosterCaptainId
    } = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    let membersValue = JSON.parse(membersValueJSON);

    const url = getUrl('approve');
    
    Swal.fire({
        html: `
            <h5 class="my-4"> 
                ${
                    userId == loggedUserId ? 
                    'Join the roster for this event?'   
                    : 'Add this member to the roster for this event? '
                }
                
            </h5>
            ${drawEventTable(eventDetails, followCounts)}
            ${rosterCountCaptainHtmlGenerater(membersValue, rosterCaptainId)}
            <small class="text-center mt-4 mb-3 d-block text-red"> You can freely leave and join a roster as long as registration has not been confirmed. </small>
            <small class="text-center text-red d-block "> Once registration is confirmed, the roster is locked in and no changes can be made to the roster. </small>
        `,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: userId == loggedUserId ? 
        'Yes, join the roster!'   
        : 'Yes, add this member!',
        denyButtonText: 'No',
        confirmButtonColor: "#43A4D7",
    }).then((result) => {
        if (result.isConfirmed) {
            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        localStorage.setItem('swal', ROSTER_STATUS_ENUMS.ROSTER_APPROVE);
                        localStorage.setItem('scroll', joinEventId);
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
                        'event_id': eventDetails.id,
                        'team_id': getData('teamId'),
                    })
                }
            );

        } else if (result.isDenied) {
        }
    })
   
}

async function disapproveMemberAction(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    let loggedUserId = getData('userId');
    const url = getUrl('disapprove');
    let {teamId, joinEventId, userId} = element.dataset;
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, 
        membersValue: membersValueJSON, 
        followCounts,
        rosterCaptainId
    } = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    let membersValue = JSON.parse(membersValueJSON);

    Swal.fire({
        html: `
            
            <h5 class="my-4"> 
            ${
                userId == loggedUserId ? 
                'Leave the roster for this event?'   
                : 'Remove this member from the roster for this event? '
            }
            </h5>
            ${drawEventTable(eventDetails, followCounts)}
            ${rosterCountCaptainHtmlGenerater(membersValue, rosterCaptainId)}
            <small class="text-center mt-4 mb-3 d-block text-red"> You can freely leave and join a roster as long as registration has not been confirmed. </small>
            <small class="text-center text-red d-block "> Once registration is confirmed, the roster is locked in and no changes can be made to the roster. </small>
        `,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 
            userId == loggedUserId ? 
            'Yes, leave the roster!'   
            : 'Yes, remove this member!'
        ,
        denyButtonText: 'No',
        confirmButtonColor: "#43A4D7",
    }).then((result) => {
        if (result.isConfirmed) {

        fetchData(url,
            function(responseData) {
                if (responseData.success) {
                    localStorage.setItem('swal', ROSTER_STATUS_ENUMS.ROSTER_DISAPPROVE);
                    localStorage.setItem('message', responseData.message);
                    localStorage.setItem('scroll', joinEventId);
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
    } else if (result.isDenied) {
    }
})
}

async function capatainMemberAction(event) {
    let element = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();
    const url = getUrl('rostercaptain');
    let loggedUserId = getData('userId');
    let {joinEventId, rosterCaptainId, rosterUserBanner, rosterUserId, rosterUserName} = element.dataset;
    const memberDataContainer = document.getElementById('reg-member-id-' + joinEventId);
    const {eventDetails: eventDetailsJSON, followCounts} = memberDataContainer.dataset;
    let eventDetails = JSON.parse(eventDetailsJSON);
    let heading = '', body = '';
    if (rosterCaptainId != 0) {
        
            heading = '<h5 class="text-center my-4">Appoint the captain for this event?</h5>';
            body = `<p class='text-center text-primary mb-2'> Appoint ${rosterUserId == loggedUserId ? 'yourself': 'this user'} as the captain? </p> 
                <div class='text-center my-3'> 
                    <img  
                        width='30'
                        height='30'
                        src='${rosterUserBanner ? '/storage/' + rosterUserBanner: '/assets/images/404.png' }
                        class="rounded-circle border-${eventDetails.tier.eventTier} me-1 object-fit-cover"
                    >
                    <span> ${rosterUserName}</span>
                </div>` ;
        } else {
            heading = '<h5 class="text-center my-4">Remove captain status from this event?</h5>';
            body = ` 
            <div class='text-center my-3'> 
                A captain must be appointed before your team can confirm your registration.
            </div>` ;
        } 
         
    
    
    Swal.fire({
        html: `
            ${heading}    
            ${drawEventTable(eventDetails, followCounts)}
            ${captainMainFormHtml}
            ${body}
        `,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        confirmButtonColor: "#43A4D7",
    }).then((result) => {
        if (result.isConfirmed) {
            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        localStorage.setItem('swal', 
                            rosterCaptainId == 0 ? 
                            
                                ROSTER_STATUS_ENUMS.CAPTAIN_REMOVE:
                                ROSTER_STATUS_ENUMS.CAPTAIN_APPROVE 
                        );
                        localStorage.setItem('message', responseData.message);
                        localStorage.setItem('scroll', joinEventId);
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

        } else if (result.isDenied) {
        }
    })
   
}

function drawEventTable(eventDetails, followCounts) {
    let innerHTML = ``;

    innerHTML+=`
        <div class="mx-3 py-2 px-3 text-start w-75 mx-auto mt-3 mb-3 border-${eventDetails.tier.eventTier} " >
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

    let rosterCount = 0, pendingMemberCount = 0;   
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
                    pendingMemberCount++;
        
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
                                    
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                                onclick="approveMemberAction(event);"

                                data-join-event-id="${ joinEventId }"
                                data-user-id="${member.user.id}"
                                class="rounded-circle cursor-pointer z-99 border-green" viewBox="0 0 16 16" 
                                viewBox="0 0 16 16">
                            <path stroke="green" stroke-width="2" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                          
                        </td>
                    </tr>
                `}).join('')}
                
            </tbody>
        </table>
               
    `;

    let teamDisplay = '';
    if (!rosterCount ) {
        teamDisplay = `<p class='text-center mt-3 pt-3'> No team members remaining to be added. </p>`; 
    } else if (!pendingMemberCount) {
        teamDisplay = `<p class='text-center mt-3 pt-3'> No roster members remaining to be added. </p>`; 
    }

    modalBody.innerHTML += teamDisplay;
    
    modalInstance.show();
}

