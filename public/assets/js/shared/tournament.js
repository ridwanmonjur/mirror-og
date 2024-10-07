let previousValues = JSON.parse(document.getElementById('previousValues')?.value);
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

function getParentByClassName(element, targetClassName) {
    let parent = element.parentElement;

    while (parent && !parent.classList.contains(targetClassName)) {
        parent = parent.parentElement;
    }

    return parent;
}

function updateModalShow(event) {
    event.stopPropagation();
    event.preventDefault();
    const button = event.currentTarget;
    let parentWithDataset = getParentByClassName(button, "tournament-bracket__match");
    
    let dataset = JSON.parse(parentWithDataset.dataset.bracket);
    const stageName = parentWithDataset.dataset.stage_name;
    const innerStageName = parentWithDataset.dataset.inner_stage_name;
    const order = parentWithDataset.dataset.order;
    dataset.stage_name = stageName;
    dataset.inner_stage_name = innerStageName;
    dataset.order = order;
    const modalElement = document.getElementById('updateModal');
    const inputs = modalElement.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        const inputName = input.getAttribute('name');
        if (dataset[inputName] !== undefined) {
            input.value = dataset[inputName];
        }
    });

    ['team1_position', 'team2_position', 'winner_next_position', 'loser_next_position'].forEach((element)=> {
        let id2 = `${element}_label`;
        console.log({id2});
        document.getElementById(id2).innerText = dataset[element];
    });

    ['result', 'status', 'team1_id', 'team2_id', 'winner_id'].forEach((selectName)=> {
        selectMap[selectName]?.updateSelectElement(dataset[selectName]);
    })

    let modal = bootstrap.Modal.getInstance(modalElement);

    if (modal) {
        modal.show();
    } else {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    };
}

function reportModalShow(event) {
    event.stopPropagation();
    event.preventDefault();
    const modalElement = document.getElementById('reportModal');
    let modal = bootstrap.Modal.getInstance(modalElement);
    console.log({hello: true, modalElement});

    if (modal) {
        modal.show();
    } else {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    };
}

window.onload = () => {
    const parentElements = document.querySelectorAll(".first-item .popover-parent");
    parentElements?.forEach(parent => {
        const contentElement = parent.querySelector(".popover-content");
        const parentElement = parent.querySelector(".popover-button");
        if (contentElement) {
            window.addPopover(parentElement, contentElement, 'mouseenter');
        }
    });

    const parentSecondElements = document.querySelectorAll(".middle-item");
    parentSecondElements?.forEach(parent => {
        const triggers = parent.querySelectorAll(".popover-button");
        triggers.forEach((trigger, index) =>{
            let triggerPositionId = trigger.dataset.position;
            let triggerParentsPositionIds = previousValues[triggerPositionId];
            
            if (triggerParentsPositionIds && Array.isArray(triggerParentsPositionIds)) {
                let triggerClassName = '.popover-middle-content.' + triggerParentsPositionIds.join(".");
                let contentElement = document.querySelector(triggerClassName);
               
                window.addPopover(trigger, contentElement, 'mouseenter');
            } 
       })
    });

    var myModal = new bootstrap.Modal(document.getElementById('disputeModal'), {});
    myModal.show();
};

let selectMap = {};
document.querySelectorAll('[data-dynamic-select]').forEach(select => {
    selectMap[select.name] = new DynamicSelect(select);
});

let updateFormElement = document.getElementById('matchForm');
const submitBtnElement = document.getElementById('submitBtn');
const closeBtnElement = document.getElementById('closeBtn');


submitBtnElement.addEventListener('click', function(event) {
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
                if (match.type==="upperBracket" && match.inner_stage_name === "eliminator1") {
                    isUpperBracketFirstRound = true;
                }
                let currentMatch = JSON.parse(currentMatchDiv.dataset.bracket);
                currentMatch.id = match.id;
                currentMatch.team1_score = match.team1_score;
                currentMatch.team2_score = match.team2_score;
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

                currentMatchDiv.dataset.bracket = JSON.stringify(currentMatch);
                
                const parentElements = currentMatchDiv.querySelectorAll(".popover-parent");
                console.log({parentElements});
                
                // can't update table so not done
                let imgs = currentMatchDiv.querySelectorAll(`.popover-button img`);
                console.log({imgs});
                let smalls = currentMatchDiv.querySelectorAll(`small.replace_me_with_image`);
                
                for (let index=0; index <2; index++) {
                    let banner = index ? team2.teamBanner : team1.teamBanner;

                    if (imgs[index] && 'src' in imgs[index]) {
                        imgs[index].src =  `/storage/${banner}`;
                        console.log({banner});
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
                            console.log({img})
                        }

                        console.log({banner2: banner});

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
                        console.log({contentElement, parentElement});
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

