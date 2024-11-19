var startDateInput = document.getElementById('startDate');
var endDateInput = document.getElementById('endDate');
var startTimeInput = document.getElementById('startTime');
var endTimeInput = document.getElementById('endTime');
var daterangeDisplay = document.getElementById('daterange-display');
var timerangeDisplay = document.getElementById('timerange-display');

function formatTimeAMPM(time) {
    if (!time) return 'hh:mm';
    
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    
    return `${displayHour}:${minutes} ${period}`;
  }
  

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
    let outputEventTierImgValue = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTierImg');
  

    setInnerHTMLFromLocalStorage('eventTierPerson', outputEventTierPerson);
    setInnerHTMLFromLocalStorage('eventTierPrize', outputEventTierPrize);
    setInnerHTMLFromLocalStorage('eventTierEntry', outputEventTierEntry);
    setInnerHTMLFromLocalStorage('eventTierTitle', outputEventTierTitle);
}

function checkValidTime() {
  
    const startDateInputValue = startDateInput.value;
    
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

        returnl
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

        return;
    }

}

function setTimeRangeDisplay () {
    timerangeDisplay.value = `${formatTimeAMPM(startTimeInput.value)} - ${formatTimeAMPM(endTimeInput.value)}`;
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

const dropZone = document.querySelector('.banner-upload');
const fileInput = document.getElementById('eventBanner');
const previewImage = document.getElementById('previewImage');
const previewWarning = document.getElementById('preview-image-warning');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border', 'border-primary');
}

function unhighlight(e) {
    dropZone.classList.remove('border', 'border-primary');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;

    if (files.length) {
        fileInput.files = files;
        handleFile('eventBanner', 'previewImage');
    }
}

document.querySelector('.upload-button').addEventListener('click', function(e) {
    e.preventDefault();
    fileInput.click();
});

window.addEventListener('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
}, false);

window.addEventListener('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
}, false);

function clearLocalStorage() {
    localStorage.clear();
}

window.onload = function() {
    /* beautify preserve:start */
   const container = document.getElementById('eventContainer');

   let $event = null;
   let tier = null;
   let type = null;
   let game = null;
   let assetKeyWord = null;
    if (container) {
        $event = JSON.parse(container.dataset.event);
        tier = JSON.parse(container.dataset.tier);
        type = JSON.parse(container.dataset.type);
        game = JSON.parse(container.dataset.game);
        assetKeyWord = container.dataset.assetKeyWord;
    } 
  
    
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


    function formatDisplayDate(date) {
        if (!date) return 'dd/mm/yy';
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: '2-digit'
        });
    }

    function formatTime (time)  {
        if (!time) return 'hh:mm';
        return time.split(':').slice(0, 2).join(':');
      };

    

    window.createLitepicker({
        element: daterangeDisplay,
        singleMode: false,
        numberOfMonths: 2,
        numberOfColumns: 2,
        startDate: $event?.startDate ? new Date($event.startDate) : null,
        endDate: $event?.endDate ? new Date($event.endDate) : null,
        format: 'DD/MM/YY',
        showTooltip: true,
        autoApply: true,
        showWeekNumbers: true,
        
        setup: (picker) => {
            if ($event?.startDate && $event?.endDate) {
                const startDate = new Date($event.startDate);
                const endDate = new Date($event.endDate);
                daterangeDisplay.value = `${formatDisplayDate(startDate)} - ${formatDisplayDate(endDate)}`;
            } else {
                daterangeDisplay.value = 'dd/mm/yy - dd/mm/yy';
            }

            picker.on('selected', (startDate, endDate) => {
                if (startDate && endDate) {
                    startDateInput.value = startDate.format('YYYY-MM-DD');
                    endDateInput.value = endDate.format('YYYY-MM-DD');
                    
                    if (!startTimeInput.value) startTimeInput.value = '00:00';
                    if (!endTimeInput.value) endTimeInput.value = '23:59';
                    
                    daterangeDisplay.value = `${startDate.format('DD/MM/YY')} - ${endDate.format('DD/MM/YY')}`;
                    
                    startDateInput.dispatchEvent(new Event('change'));
                    endDateInput.dispatchEvent(new Event('change'));
                } else {
                    daterangeDisplay.value = 'dd/mm/yy - dd/mm/yy';
                }
            });
        },
    });

    daterangeDisplay.style.cursor = 'pointer';

    let startTime = $event?.startTime;
    let endTime =  $event?.endTime;

    timerangeDisplay.value = `${formatTimeAMPM(startTime)} - ${formatTimeAMPM(endTime)}`;

    const timeConfig = {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: false,
        minuteIncrement: 1
    };

    const startPicker = window.createFlatpickr("#startTime", {
        ...timeConfig,
        onChange: function(selectedDates, dateStr) {
            checkValidTime();
            setTimeRangeDisplay();
        }
    });

    const endPicker = window.createFlatpickr("#endTime", {
        ...timeConfig,
        onChange: function(selectedDates, dateStr) {
            checkValidTime();
            setTimeRangeDisplay();
        }
    });

}

function closeDropDown() {
    const dropdown = bootstrap.Dropdown.getInstance(timerangeDisplay);
    if (dropdown) {
        dropdown.hide();
    }
}

document.addEventListener("keydown", function(event) {
    var target = event.target;

    if (event.key === "Enter" && target.tagName.toLowerCase() !== "textarea" && target.tagName.toLowerCase() === "input") {
        event.preventDefault();
    }
});


// Create a file named character-counter.js in your public/js directory

document.addEventListener('DOMContentLoaded', function() {
    const MAX_EVENT_NAME_CHARS = 60;
    const MAX_EVENT_DESCRIPTION_CHARS = 3000;

    const eventNameInput = document.getElementById('eventName');
    const eventDescriptionTextarea = document.getElementById('eventDescription');
    const eventNameCounter = document.querySelector('.character-count-eventName');
    const eventDescriptionCounter = document.querySelector('.character-count-eventDescription');

    function updateCharacterCount(element, counter, maxChars) {
        const remainingChars = maxChars - element.value.length;
        counter.textContent = remainingChars;
        
        if (remainingChars < 1) {
                element.value = element.value.substring(0, maxChars);
                window.Swal.fire({
                    icon: 'error',
                    title: 'Character Limit Exceeded',
                    text: `You cannot exceed ${maxChars} characters.`,
                    confirmButtonColor: '#43a4d7',
                    confirmButtonText: 'OK'
                });
                
                counter.textContent = '0';
                counter.parentElement.classList.add('text-danger');
                return;
        } else {
            counter.parentElement.classList.remove('text-danger');
        }
    }

    if (eventNameInput) {
        eventNameInput.addEventListener('input', function() {
            updateCharacterCount(this, eventNameCounter, MAX_EVENT_NAME_CHARS);
        });
        updateCharacterCount(eventNameInput, eventNameCounter, MAX_EVENT_NAME_CHARS);
    }

    if (eventDescriptionTextarea) {
        eventDescriptionTextarea.addEventListener('input', function() {
            updateCharacterCount(this, eventDescriptionCounter, MAX_EVENT_DESCRIPTION_CHARS);
        });

        updateCharacterCount(eventDescriptionTextarea, eventDescriptionCounter, MAX_EVENT_DESCRIPTION_CHARS);
    }
});

