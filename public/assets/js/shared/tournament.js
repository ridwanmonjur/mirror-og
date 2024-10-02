let previousValues = JSON.parse(document.getElementById('previousValues')?.value ?? '[]');
console.log({element: document.getElementById('previousValues' )});   
var bracketItemList = document.querySelectorAll('.codeCANcode.tournament-bracket__item');
bracketItemList.forEach(item => {
    item.classList.add('special-item-right');
});


var bracketteamList = document.querySelectorAll('.codeCANcode.tournament-bracket__match');
bracketItemList.forEach(item => {
    console.log({
        hi: true
    });
    item.classList.add('special-item2');
    item.style.setProperty('--border-color', 'red');
});

var bracketBoxList = document.querySelectorAll('.codeCANcode .tournament-bracket__box.codeCANcode');
bracketBoxList.forEach(item => {
    console.log({
        hi: true
    });
    item.style.setProperty('--border2-color', 'red');
});

function getParentByClassName(element, targetClassName) {
    let parent = element.parentElement;

    while (parent && !parent.classList.contains(targetClassName)) {
        parent = parent.parentElement;
    }

    return parent;
}

function fillModalInputs(event) {
    event.stopPropagation();
    
    const button = event.currentTarget;
    let parentWithDataset = getParentByClassName(button, "tournament-bracket__match");
    
    let dataset = JSON.parse(parentWithDataset.dataset.bracket);
    const stageName = parentWithDataset.dataset.stage_name;
    const innerStageName = parentWithDataset.dataset.inner_stage_name;
    const order = parentWithDataset.dataset.order;
    dataset.stage_name = stageName;
    dataset.inner_stage_name = innerStageName;
    dataset.order = order;
    const modalElement = document.getElementById('firstMatchModal');

    const inputs = modalElement.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        const inputName = input.getAttribute('name');
        if (dataset[inputName] !== undefined) {
            input.value = dataset[inputName];
        }
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
    console.log({parentSecondElements});
    parentSecondElements?.forEach(parent => {
        const triggers = parent.querySelectorAll(".popover-button");
        triggers.forEach((trigger) =>{
            let triggerPositionId = trigger.dataset.position;
            let triggerParentsPositionIds = previousValues[triggerPositionId];
            if (triggerParentsPositionIds && Array.isArray(triggerParentsPositionIds)) {
                let triggerClassName = '.popover-middle-content.' + triggerParentsPositionIds.join(".");
                let contentElement = document.querySelector(triggerClassName);
                console.log({triggerParentsPositionIds, triggerClassName, contentElement});
                window.addPopover(trigger, contentElement, 'mouseenter');
            } 
       })
    });
};

let selectMap = {};
document.querySelectorAll('[data-dynamic-select]').forEach(select => {
    selectMap[select.name] = new DynamicSelect(select);
});
