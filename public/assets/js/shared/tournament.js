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
        toastError("Previous match results not updated");
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
        document.getElementById(id2).innerText = dataset[element];
    });

    ['result', 'status', 'team1_id', 'team2_id', 'winner_id'].forEach((selectName)=> {
        selectMap[selectName]?.updateSelectElement(dataset[selectName]);
    })

    let modal = bootstrap.Modal.getInstance(modalElement);

    if (modal) {
        modal.show();
    } else {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    };
}

function reportModalShow(event) {
    event.stopPropagation();
    event.preventDefault();
    event.stopPropagation();
    const button = event.currentTarget;
    let { position } = button.dataset;
    let triggerParentsPositionIds = previousValues[position];
    if (!triggerParentsPositionIds) {
        throw new Error("Positions missing");
    }

    let classNamesWithoutPrecedingDot = triggerParentsPositionIds.join(".");

    let parentWithDataset = document.querySelector(`.tournament-bracket__match.${classNamesWithoutPrecedingDot}`);
    if (
        parentWithDataset === null || 
        parentWithDataset.dataset === null || 
        parentWithDataset.dataset.bracket === null
    ) {
        toastError("Previous match results not updated");
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

    if (modal) {
        modal.show();
    } else {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    };
}

// addOnLoad( () => {
    
 
// });

let selectMap = {};
document.querySelectorAll('[data-dynamic-select]').forEach(select => {
    selectMap[select.name] = new DynamicSelect(select);
});

let updateFormElement = document.getElementById('matchForm');
const submitBtnElement = document.getElementById('submitBtn');
const closeBtnElement = document.getElementById('closeBtn');


submitBtnElement?.addEventListener('click', function(event) {
    event.preventDefault();

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
                let {team1, match, team2} = data.data;
                let currentMatchDiv = document.querySelector(`.${match.team1_position}.${match.team2_position}`);
                if (match.stage_name == "upperBracket" && match.inner_stage_name == "eliminator1") {
                    isUpperBracketFirstRound = true;
                }

                console.log({isUpperBracketFirstRound})
                console.log({isUpperBracketFirstRound})
                console.log({isUpperBracketFirstRound})

                let currentMatch = JSON.parse(currentMatchDiv.dataset.bracket);
                currentMatch.id = match.id;
                currentMatch.winner_id = match.winner_id;
                currentMatch.team1_id = match.team1_id;
                currentMatch.team2_id = match.team2_id;
                currentMatch.result= match.result;
                currentMatch.status= match.status;

                currentMatch.team1_teamBanner = team1.teamBanner;
                currentMatch.team2_teamBanner = team2.teamBanner;
                if (currentMatch.winner_id == team1_id) {
                    currentMatch.winner = team1;
                }

                if (currentMatch.winner_id == team2_id) {
                    currentMatch.winner = team2;
                }

                if (currentMatchDiv.dataset) {
                    currentMatchDiv.dataset.bracket = JSON.stringify(currentMatch);
                } else {
                    currentMatch.dataset = {
                        bracket :  JSON.stringify(currentMatch)
                    }
                }
                
                const parentElements = currentMatchDiv.querySelectorAll(".popover-parent");
                
                let imgs = null, smalls = null;
                if (isUpperBracketFirstRound) {
                    imgs = currentMatchDiv.querySelectorAll(`img.popover-button`);
                    smalls = currentMatchDiv.querySelectorAll(`.popover-button.replace_me_with_image`);
                } else {
                    imgs = currentMatchDiv.querySelectorAll(`.popover-button img`);
                    smalls = currentMatchDiv.querySelectorAll(`small.replace_me_with_image`);
                }
                 
                
                for (let index=0; index <2; index++) {
                    let banner = index ? team2.teamBanner : team1.teamBanner;

                    if (imgs[index] && 'src' in imgs[index]) {
                        imgs[index].src =  `/storage/${banner}`;
                    } else {
                        let small = smalls[index];
                         if (small) {
                            let img = document.createElement('img');
                            small.parentElement.replaceChild(img, small);
                            img.src = `/storage/${banner}`;
                            img.style.width = '100%';
                            img.height = '25';
                            img.onerror = function() {
                                this.src='/assets/images/404.png';
                            };

                            img.className = 'popover-button position-absolute d-none-when-hover object-fit-cover me-2';
                            img.alt = 'Team View';
                            img.style.zIndex = '99';
                        }


                    }
                }
               
                closeBtnElement.click();

                const bracketBoxList = currentMatchDiv.querySelectorAll(`.tournament-bracket__box`);
                let popoverImgs = currentMatchDiv.querySelectorAll('.popover-content-img');
                let index = 0;
                bracketBoxList.forEach(bracketBox => {
                    let banner = index ? team2.teamBanner : team1.teamBanner;
                    let roster = index ? team2.roster : team1.roster;
                    let currentImg = popoverImgs[index];
                    if (currentImg && 'src' in currentImg) currentImg.src = `/storage/${banner}`;

                    const rosterContainer = bracketBox.querySelector('.popover-box .col-12.col-lg-7');
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
                });

                if (isUpperBracketFirstRound) {
                    parentElements.forEach(parent => {
                        const contentElement = parent.querySelector(".popover-content");
                        const parentElement = parent.querySelector(".popover-button");
                        if (contentElement) {
                            window.addPopover(parentElement, contentElement, 'mouseenter');
                        }
                    });
                } else {
                    
                }
                window.Toast.fire({
                    icon: 'success',
                    text: data.message
                });

            } else {
                window.toastError('Error saving match: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.toastError('An error occurred while saving the match.');
        });
});

const uploadContainers = document.querySelectorAll('.upload-container');