// addOnLoad(()=> {
//     window.showLoading();
// });

// document.addEventListener('alpine:init', () => {
//     window.Swal.close();
// }, { once: true });

let previousValues = JSON.parse(document.getElementById('previousValues')?.value);

var bracketItemList = document.querySelectorAll('.codeCANcode.tournament-bracket__item');
bracketItemList.forEach(item => {
    item.classList.add('special-item-right');
});

const eventId = document.getElementById('eventId').value;

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
    let parentWithDataset = document.querySelector(`.tournament-bracket__match.${team1_id}.${team2_id}`);

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
    inputs.forEach(input => {
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
    let modal = bootstrap.Modal.getInstance(modalElement);

    if (modal) {
        modal.show();
    } else {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    };

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
    let modal = bootstrap.Modal.getInstance(modalElement);
    window.closeLoading();
    if (modal) {
        modal.show();
    } else {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    };
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
                let currentMatch = document.querySelector(`.${match.team1_position}.${match.team2_position}`);
                
                if (match.stage_name == "upperBracket" && match.inner_stage_name == "eliminator1") {
                    isUpperBracketFirstRound = true;
                }

                if (match.stage_name == "finals" && match.inner_stage_name == "finals") {
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

                        img.className = 'popover-button position-absolute d-none-when-hover object-fit-cover me-2';
                        img.alt = 'Team View';
                        img.style.zIndex = '99';
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
                    if (currentTitle) currentTitle.innerText = team?.teamName ?? 'N/A';

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

