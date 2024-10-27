function checkStringNullOrEmptyAndReturn(value) {
    if (value === null || value === undefined) return null;

    let _value = String(value).trim();
    return (_value === "") ? null : _value;
}

function checkStringNullOrEmptyAndReturnFromLocalStorage(key) {
    let item = localStorage.getItem(key);
    return checkStringNullOrEmptyAndReturn(item);
}

function setInnerHTMLFromLocalStorage(key, element) {
    let value = checkStringNullOrEmptyAndReturnFromLocalStorage(key);
    
    if (value) element.innerHTML = value;
    else console.error(`Item not in localStorage: ${key} ${value}`)
}

function setImageSrcFromLocalStorage(key, element) {
    let value = checkStringNullOrEmptyAndReturnFromLocalStorage(key);
    
    if (value && element) element.src = value;
    else console.error(`Can't set image for: ${key}, ${value} ${value}`)
}

function setLocalStorageFromEventObject(key, property) {
    let value = checkStringNullOrEmptyAndReturn(property);
    
    if (value) localStorage.setItem(key, value);
    else console.error(`Item not in localStorage: ${key} ${value}`)
}

function fillEventTags() {
    let eventTags = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTags');
    
    if (eventTags != null) {
        let eventTagsParsed = Object(JSON.parse(eventTags));
        
        var tagify = new Tagify(document.querySelector('#eventTags'),
            [],
        );
        tagify.addTags(eventTagsParsed)
    } else {
        new Tagify(document.querySelector('#eventTags'), []);
    }

}

function fillStepGameDetailsValues() {
    let formValues = getFormValues(['eventTier', 'eventType', 'gameTitle']);
    
    if (
        'eventTier' in formValues &&
        'gameTitle' in formValues &&
        'eventType' in formValues
    ) {
        let eventTier = formValues['eventTier'];
        let eventType = formValues['eventType'];
        let gameTitle = formValues['gameTitle'];

        // Game Title
        let outputGameTitleImg = document.querySelector('img#outputGameTitleImg');

        setImageSrcFromLocalStorage('gameTitleImg', outputGameTitleImg);
    }

    // Event Type
    let outputEventTypeTitle = document.getElementById('outputEventTypeTitle');
    let outputEventTypeDefinition = document.getElementById('outputEventTypeDefinition');
    setInnerHTMLFromLocalStorage('eventTypeTitle', outputEventTypeTitle);


    setInnerHTMLFromLocalStorage('eventTypeDefinition', outputEventTypeDefinition);

    // Event Tier
    let outputEventTierImg = document.querySelector(`img#outputEventTierImg`);
    let outputEventTierTitle = document.getElementById('outputEventTierTitle');
    let outputEventTierPerson = document.getElementById('outputEventTierPerson');
    let outputEventTierPrize = document.getElementById('outputEventTierPrize');
    let outputEventTierEntry = document.getElementById('outputEventTierEntry');

    setImageSrcFromLocalStorage('eventTierImg', outputEventTierImg);
    setInnerHTMLFromLocalStorage('eventTierPerson', outputEventTierPerson);
    setInnerHTMLFromLocalStorage('eventTierPrize', outputEventTierPrize);
    setInnerHTMLFromLocalStorage('eventTierEntry', outputEventTierEntry);
    setInnerHTMLFromLocalStorage('eventTierTitle', outputEventTierTitle);
}

function checkValidTime() {
    var startDateInput = document.getElementById('startDate');
    var endDateInput = document.getElementById('endDate');
    var startTimeInput = document.getElementById('startTime');
    var endTimeInput = document.getElementById('endTime');
    const startDateInputValue = startDateInput.value;
    const endDateInputValue = endDateInput.value;
    const startTimeInputValue = startTimeInput.value;
    const endTimeInputValue = endTimeInput.value;
    
    var now = new Date();
    var startDate = new Date(startDateInputValue + " " + startTimeInput.value);
    var endDate = new Date(endDateInput.value + " " + endTimeInput.value);
    
    if (startDate < now || endDate <= now) {
        Toast.fire({
            icon: 'error',
            text: "Start date or end date cannot be earlier than current time."
        });
        if (startDate < now) {
            startDateInput.value = ""
        } else if (endDate < now) {
            endDateInput.value = ""
        }
    }
    
    if (startTimeInput.value === "" || endTimeInput.value === "") {
        return;
    }
    
    if (endDate < startDate) {
        Toast.fire({
            icon: 'error',
            text: "End  and time cannot be earlier than start date and time."
        });
        startDateInput.value = "";
        startTimeInput.value = "";
    }
}

function handleFile(inputFileId, previewImageId) {
    var selectedFile = document.getElementById(inputFileId).files[0];

    const fileSize = selectedFile.size / 1024 / 1024; // in MiB
    
    if (fileSize > 8) {
        selectedFile.value = '';
        Toast.fire({
            icon: 'error',
            text: "File size exceeds 2 MiB."
        })

        return;
    }

    var allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];

    if (!allowedTypes.includes(selectedFile.type)) {
        selectedFile.value = '';
        Toast.fire({
            icon: 'error',
            text: "Invalid file type. Please upload a PNG, JPEG or JPG file."
        })

        return;
    }

    previewSelectedImage('eventBanner', 'previewImage');
}

function clearLocalStorage() {
    localStorage.clear();
}

window.onload = function() {
    /* beautify preserve:start */
   const container = document.getElementById('eventContainer');

    const $event = JSON.parse(container.dataset.event);
    const tier = JSON.parse(container.dataset.tier);
    const type = JSON.parse(container.dataset.type);
    const game = JSON.parse(container.dataset.game);
    const assetKeyWord = container.dataset.assetKeyWord;
    
    clearLocalStorage();
    
    if ($event) {

        // game
        setLocalStorageFromEventObject('gameTitleImg', assetKeyWord+ 'storage/' + game?.gameIcon);
        // event type
        setLocalStorageFromEventObject('eventTypeTitle', type?.eventType);
        setLocalStorageFromEventObject('eventTypeDefinition', type?.eventDefinitions);
        // tier
        setLocalStorageFromEventObject('eventTierImg', assetKeyWord+ 'storage/' + tier?.tierIcon);
        setLocalStorageFromEventObject('eventTierPerson', tier?.tierTeamSlot);
        setLocalStorageFromEventObject('eventTierPrize', tier?.tierPrizePool);
        setLocalStorageFromEventObject('eventTierEntry', tier?.tierEntryFee);
        setLocalStorageFromEventObject('eventBanner', $event?.eventBanner);
        // banner
        setLocalStorageFromEventObject('eventTierTitle', tier?.eventTier);
        setLocalStorageFromEventObject('eventTags', $event?.eventTags);
        if ($event?.eventTags != null) {
        } else {
            new Tagify(document.querySelector('#eventTags'), []);
        }
    }
    else{
        new Tagify(document.querySelector('#eventTags'), []);
    }
}

document.addEventListener("keydown", function(event) {
    var target = event.target;

    if (event.key === "Enter" && target.tagName.toLowerCase() !== "textarea" && target.tagName.toLowerCase() === "input") {
        event.preventDefault();
    }
});

