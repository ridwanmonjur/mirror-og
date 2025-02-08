
function reddirectToLoginWithIntened(route) {
    route = encodeURIComponent(route);
    let url = document.getElementById('signin_url')?.value;
    url += `?url=${route}`;
    window.location.href = url;
}

function goToCreateScreen() {
    let url = document.getElementById('create_url')?.value;
    window.location.href = url;
}

function goToEditScreen() {
   let url = document.getElementById('edit_url')?.value;
    window.location.href = url;
}

async function submitLikesForm() {
    event.preventDefault();
    let likesCount = document.getElementById('likesCount');
    let likesButton = document.getElementById('likesButton');
    let count = Number(likesCount.dataset.count);
    let likesForm = document.getElementById('likesForm');
    let formData = new FormData(likesForm);
    likesButton.style.setProperty('pointer-events', 'none');

    try {
        let jsonObject = {}
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }
        let jsonString = JSON.stringify(jsonObject);
        let user_id = formData.get('user_id');
        let response = await fetch(likesForm.action, {
            method: likesForm.method,
            body: jsonString,
            headers: {
                'credentials': 'include',
                'Accept': 'application/json',
                "Content-Type": "application/json",
            }
        });

        let data = await response.json();

        if (data.isLiked) {
            count++;
            likesButton.innerHTML = `<svg 
                onclick="submitLikesForm()"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#43A4D7" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
            </svg>`;
            likesCount.classList.add('text-primary');
        } else {
            count--;
            likesButton.innerHTML = `<svg 
                onclick="submitLikesForm()"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" 
                class="bi bi-hand-thumbs-up svg-hover cursor-pointer" viewBox="0 0 16 16" stroke-width="3">
                <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
            </svg>`;
            likesCount.classList.remove('text-primary');
        }

        likesButton.style.setProperty('pointer-events', 'auto');
        if (count == 1) {
            likesCount.innerHTML = '1';
        } else if (count == 0) {
            likesCount.innerHTML = `0`;
        } else {
            likesCount.innerHTML = `${count}`;
        }
        likesCount.dataset.count = count;
    } catch (error) {
        likesButton.style.setProperty('pointer-events', 'auto');
        toastError('Error occured.', error);
    }
}

document.getElementById('followForm')?.addEventListener('submit', async function(event) {
    event.preventDefault();
    let followCount = document.getElementById('followCount');
    let followButton = document.getElementById('followButton');
    let count = Number(followCount.dataset.count);
    let form = this;
    let formData = new FormData(form);
    followButton.style.setProperty('pointer-events', 'none');

    try {
        let jsonObject = {}
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }
        let jsonString = JSON.stringify(jsonObject);
        let user_id = formData.get('user_id');
        let response = await fetch(form.action, {
            method: form.method,
            body: jsonString,
            headers: {
                'credentials': 'include',
                'Accept': 'application/json',
                "Content-Type": "application/json",
            }
        });

        let data = await response.json();
        let followButton = document.getElementById('followButton');
        followButton.style.setProperty('pointer-events', 'none')

        if (data.isFollowing) {
            count++;
            followButton.innerText = 'Following';
            followButton.style.backgroundColor = '#8CCD39';
            followButton.style.color = 'black';
        } else {
            count--;
            followButton.innerText = 'Follow';
            followButton.style.backgroundColor = '#43A4D7';
            followButton.style.color = 'white';
        }

        followButton.style.setProperty('pointer-events', 'auto');
        if (count == 1) {
            followCount.innerHTML = '<i> 1 follower </i>';
        } else if (count == 0) {
            followCount.innerHTML = `<i> 0 followers </i>`;
        } else {
            followCount.innerHTML = `<i> ${count} followers </i>`;
        }
        followCount.dataset.count = count;
    } catch (error) {
        followButton.style.setProperty('pointer-events', 'auto');
        toastError('Error occured.', error);
    }
});

// addOnLoad(()=> {
//     window.showLoading();
// });

// document.addEventListener('alpine:init', () => {
//     window.Swal.close();
// }, { once: true });

let previousValues = JSON.parse(document.getElementById('previousValues')?.value ?? '[]');
const eventId = document.getElementById('eventId')?.value;

var bracketItemList = document.querySelectorAll('.codeCANcode.tournament-bracket__item');
bracketItemList.forEach(item => {
    item.classList.add('special-item-right');
});


var bracketteamList = document.querySelectorAll('.codeCANcode.tournament-bracket__match');
bracketItemList.forEach(item => {
    item.classList.add('special-item2');
    item.style.setProperty('--border-color', 'red');
});

var bracketBoxList = document.querySelectorAll('.codeCANcode .tournament-bracket__box.codeCANcode');
bracketBoxList.forEach(item => {
    item.style.setProperty('--border2-color', 'red');
});

function updateModalShow(event) {
    window.showLoading();
    event.stopPropagation();
    event.preventDefault();
    const button = event.currentTarget;
    let { team1_id, team2_id } = button.dataset;
    let team2VisibleContainer = document.querySelector('.team2-toggle');
    let parentWithDataset  = null;
    if (team2_id) {
        team2VisibleContainer.classList.remove('d-none');
        parentWithDataset = document.querySelector(`.tournament-bracket__match.${team1_id}.${team2_id}`);
    } else {
        team2VisibleContainer.classList.add('d-none');
        parentWithDataset = document.querySelector(`.tournament-bracket__match.${team1_id}.finals`);
    }

    if (
        parentWithDataset === null || 
        parentWithDataset.dataset === null || 
        parentWithDataset.dataset.bracket === null
    ) {
        window.closeLoading();
        toastError("Dataset match results not updated");
        return;
    }

    let dataset = JSON.parse(parentWithDataset.dataset.bracket);

    dataset.inner_stage_name = parentWithDataset.dataset.inner_stage_name;
    dataset.stage_name = parentWithDataset.dataset.stage_name;
    dataset.event_details_id = eventId;
    dataset.order = parentWithDataset.dataset.order;

    const modalElement = document.getElementById('updateModal');
    const inputs = modalElement.querySelectorAll('input, select, textarea');
    inputs?.forEach(input => {
        const inputName = input.getAttribute('name');
        input.value = dataset[inputName];
    });

    ['team1_position', 'team2_position', 'winner_next_position', 'loser_next_position'].forEach((element)=> {
        let id2 = `${element}_label`;
        let domElement = document.getElementById(id2)
        if (domElement) 
            domElement.innerText = dataset[element] ?? '-';
    });

    ['team1_id', 'team2_id',].forEach((selectName)=> {
        selectMap[selectName]?.updateSelectElement(dataset[selectName]);
    })

    window.closeLoading();
    
   

    try {
        // First, check if element exists and has correct structure
        if (!modalElement || !modalElement.classList) {
            console.error('Invalid modal element:', modalElement);
            return;
        }
    
        // Check if a modal instance already exists
        let existingModal = bootstrap.Modal.getInstance(modalElement);
        if (existingModal) {
            console.log('Using existing modal instance');
            existingModal.show();
            return;
        }
    
        // If no existing instance, create new one with explicit config
        let modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        console.log('New modal instance created:', modal);
        modal.show();
    
    } catch (error) {
        console.error('Modal error:', error);
        console.log('Modal element details:', {
            element: modalElement,
            hasClassList: Boolean(modalElement?.classList),
            id: modalElement?.id,
            classes: modalElement?.className
        });
    }
    

}

function reportModalShow(event) {
    window.showLoading();
    event.preventDefault();
    const button = event.currentTarget;
    let { position } = button.dataset;
    let triggerParentsPositionIds = previousValues[position];
    if (!triggerParentsPositionIds) {
        console.error("Positions missing");
        console.error("Positions missing");
        window.closeLoading();
        return;
    }

    let classNamesWithoutPrecedingDot = triggerParentsPositionIds.join(".");

    let parentWithDataset = document.querySelector(`.tournament-bracket__match.${classNamesWithoutPrecedingDot}`);
    if (
        parentWithDataset === null || 
        parentWithDataset.dataset === null || 
        parentWithDataset.dataset.bracket === null
    ) {
        window.closeLoading();
        return;
    }

    let dataset = JSON.parse(parentWithDataset.dataset.bracket);

    const alpineEvent = new CustomEvent("currentReportChange", {
        detail: {
            classNamesWithoutPrecedingDot,
            // team1
            team1_position: dataset.team1_position,
            team1_id: dataset.team1_id,
            team1_teamBanner: dataset.team1_teamBanner,
            team1_teamName: dataset.team1_teamName,
            user_level: dataset.user_level,
            // team2
            team2_position: dataset.team2_position,
            team2_id: dataset.team2_id,
            team2_teamBanner: dataset.team2_teamBanner,
            team2_teamName:  dataset.team2_teamName,
            position: position

        }
    });
    window.dispatchEvent(alpineEvent);
    const modalElement = document.getElementById('reportModal');
    let modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    window.closeLoading();
    if (modal) {
        modal.show();
    } 
}


let selectMap = {};
document.querySelectorAll('[data-dynamic-select]').forEach(select => {
    selectMap[select.name] = new DynamicSelect(select);
});

let updateFormElement = document.getElementById('matchForm');
const submitBtnElement = document.getElementById('submitBtn');
const closeBtnElement = document.getElementById('closeBtn');


submitBtnElement?.addEventListener('click', function(event) {
    event.preventDefault();
    window.showLoading();
    const updateFormElementData = new FormData(updateFormElement);
    let jsonObject = {}
    for (let [key, value] of updateFormElementData.entries()) {
        jsonObject[key] = value;
    }

    fetch(updateFormElement.action, {
        method: 'POST',
        body: JSON.stringify(jsonObject),
        headers: {
            'Content-type': 'application/json',  
            'X-CSRF-TOKEN': updateFormElement.querySelector('input[name="_token"]').value
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
              
                let isUpperBracketFirstRound = false;
                let isFinalBracket = false;
                let {team1, match, team2} = data.data;
                let currentMatch = null;
                if (match.team2_position) {
                    currentMatch = document.querySelector(`.${match.team1_position}.${match.team2_position}`);
                } else {
                    currentMatch = document.querySelector(`.${match.team1_position}.finals`);
                }
                
                if (match.stage_name == "upperBracket" && match.inner_stage_name == "eliminator1") {
                    isUpperBracketFirstRound = true;
                }

                if (match.stage_name == "finals" ) {
                    isFinalBracket = true;
                }

                let currentDataset = JSON.parse(currentMatch.dataset.bracket);
            

                currentDataset.id = match.id;
                currentDataset.winner_id = match.winner_id;
                currentDataset.team1_id = match.team1_id;
                currentDataset.team2_id = match.team2_id;
                currentDataset.result= match.result;
                currentDataset.status= match.status;

                if (team1) {
                    currentDataset.team1_teamName = team1.teamName;
                    currentDataset.team1_teamBanner = team1.teamBanner;
                } 

                if (team2) {
                    currentDataset.team2_teamName = team2.teamName;
                    currentDataset.team2_teamBanner = team2.teamBanner;
                }

                if (currentDataset.winner_id == team1_id) {
                    currentDataset.winner = team1;
                }

                if (currentDataset.winner_id == team2_id) {
                    currentDataset.winner = team2;
                }

                if (currentMatch.dataset) {
                    currentMatch.dataset.bracket = JSON.stringify(currentDataset);
                } else {
                    currentMatch.dataset = {
                        bracket :  JSON.stringify(currentDataset)
                    }
                }

                const parentElements = currentMatch.querySelectorAll(".popover-parent");
                
                let imgs = null, smalls = null;
                if (isUpperBracketFirstRound) {
                    imgs = currentMatch.querySelectorAll(`img.popover-button`);
                    smalls = currentMatch.querySelectorAll(`.popover-button.replace_me_with_image`);
                } else {
                    imgs = currentMatch.querySelectorAll(`.popover-button img`);
                    smalls = currentMatch.querySelectorAll(`small.replace_me_with_image`);
                }


                let imgsMap = {}, smallsMap = {};
                imgs.forEach((img, index)=> {
                    imgsMap[img.dataset.position] = index;
                });

                smalls.forEach((img, index)=> {
                    smallsMap[img.dataset.position] = index;
                });

                for (let index=0; index <2; index++) {
                    let team = null;
                    if (index) {
                        team = team2;
                        position = currentDataset.team2_position;
                    } else {
                        team = team1;
                        position = currentDataset.team1_position;
                    }

                    let img = imgs[imgsMap[position]];
                    let small = smalls[smallsMap[position]];
                    if (!team) {
                        if (img) {
                            let newSmall = document.createElement('small');
                            img.parentElement.replaceChild(newSmall, img);
                            newSmall.innerText = position;
                            newSmall.className = 'popover-button ms-1 position-absolute  replace_me_with_image ';
                            newSmall.style.zIndex = '99';
                            newSmall.dataset.position = position;
                            newSmall.addEventListener('click', reportModalShow);

                        }

                        continue;
                    }

                    let banner = team?.teamBanner;
                    if (img && 'src' in img) {
                        if (img.dataset.position == position) {
                            img.src =  `/storage/${banner}`;
                        }
                    } 
                        
                    if (small) {
                        let img = document.createElement('img');
                        small.parentElement.replaceChild(img, small);
                        img.src = `/storage/${banner}`;
                        img.style.width = '100%';
                        img.height = '25';
                        img.dataset.position = position;
                        img.onerror = function() {
                            this.src='/assets/images/404.png';
                        };

                        img.className = 'popover-button position-absolute w-100 h-100 d-none-when-hover object-fit-cover me-2';
                        img.alt = 'Team View';
                        img.style.zIndex = '99';
                        img.addEventListener('click', reportModalShow);
                    }


                }
               
                closeBtnElement.click();

                const bracketBoxList = currentMatch.querySelectorAll(`.tournament-bracket__box`);
                let popoverImgs = currentMatch.querySelectorAll('.popover-img');
                let popoverTitles = currentMatch.querySelectorAll('.popover-title');
                let index = 0, isEven = false;
                while (index < popoverImgs.length) {
                    let team = isEven? team2: team1;
                    let banner = team?.teamBanner;
                    let roster = team?.roster;
                    let currentImg = popoverImgs[index];
                    let currentTitle = popoverTitles[index];
                    if (currentImg) currentImg.src = `/storage/${banner}`;
                    if (currentTitle) currentTitle.innerText = team?.teamName ?? 'Not available';

                    const rosterContainer = bracketBoxList[index]?.querySelector('.popover-box .roster-container');
                    if (rosterContainer && roster) {
                        if (Array.isArray(roster) && roster[0] !== undefined) {
                            let rosterHtml = '<ul class="d-block ms-0 ps-0">';
                            roster.forEach(rosterItem => {
                                rosterHtml += `
                                    <li class="d-inline">
                                        <img width="25" height="25" onerror="this.src='/assets/images/404.png';"
                                            src="/storage/${rosterItem.user.userBanner}" alt="User Banner"
                                            class="mb-2 rounded-circle object-fit-cover me-3">
                                        ${rosterItem.user.name}
                                    </li>
                                    <br>
                                `;
                            });
                            rosterHtml += '</ul>';
                            rosterContainer.innerHTML = rosterHtml;
                        } else {
                            rosterContainer.innerHTML = '<p class="text-muted">The team roster is empty.</p>';
                        }
                    }

                    index++;
                    isEven = !isEven;
                }

                if (isUpperBracketFirstRound) {
                    parentElements.forEach(parent => {
                        const contentElement = parent.querySelector(".popover-content");
                        const parentElement = parent.querySelector(".popover-button");
                        if (contentElement) {
                            window.addPopover(parentElement, contentElement, 'mouseenter');
                        }
                    });
                }
                
                const parentWinner = document.querySelectorAll(`.tournament-bracket__match.middle-item.${currentDataset.winner_next_position}`);
                const loserWinner = document.querySelectorAll(`.tournament-bracket__match.middle-item.${currentDataset.loser_next_position}`);
                [...parentWinner, ...loserWinner]?.forEach(parent => {
                    const triggers = parent.querySelectorAll(".popover-button");
                    triggers.forEach((trigger) => {
                        let triggerPositionId = trigger.dataset.position;
                        let triggerParentsPositionIds = previousValues[triggerPositionId];
                        if (triggerParentsPositionIds && Array.isArray(triggerParentsPositionIds)) {
                            let triggerClassName = '.popover-middle-content.' + triggerParentsPositionIds.join(".");
                            let contentElement = document.querySelector(triggerClassName);
                            window.addPopover(trigger, contentElement, 'mouseenter');
                        }
                    })
                });

                console.log()
                if (match.team2_position && team2 && team1) {
                    window.updateReportDispute(`${match.team1_position}.${match.team2_position}`, team1.id, team2.id);
                }
                
                window.Toast.fire({
                    icon: 'success',
                    text: data.message
                });

                window.closeLoading();

            } else {
                window.toastError('Error saving match: ' + data.message);
            }
        })
        .catch(error => {
            window.closeLoading();
            console.error('Error:', error);
            window.toastError('An error occurred while saving the match.');
        });
});

const uploadContainers = document.querySelectorAll('.upload-container');


function redirectToTeamPage(teamId) {
    window.location.href = `/view/team/${teamId}`;
}

