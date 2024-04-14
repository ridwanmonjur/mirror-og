
function addFormValues(keyList) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];
    
    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form cannot be found!'
        })
        return []
    }

    for (var key of keyList) {
        var formField = createEventForm.elements[key];
        
        if (formField) {
            var value = formField.value;
            allFormkeyList[key] = value;
        }
    }
    return allFormkeyList;
}

function validateFormValuesPresent(keyList) {
    let isFormValid = true, invalidKey = '';

    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form not found'
        })
        return [false, "form"];
    }

    for (var key of keyList) {
        var formField = createEventForm.elements[key];
        
        if (formField) {
            var value = formField.value.trim();
            if (value === '') {
                isFormValid = false;
                invalidKey = key;
            }
        }
        else {
            Toast.fire({
                icon: 'error',
                title: 'Form not found'
            })
            isFormValid = false;
            invalidKey = key;
        }
    }

    return isFormValid ? [isFormValid, null] : [isFormValid, invalidKey];
}


function setFormValues(values) {
    var createEventForm = document.forms['create-event-form'];

    if (createEventForm) {
        for (var key in values) {
            var formField = createEventForm.elements[key];
            
            if (formField && values[key]) {
                formField.value = values[key];
            } else {
                console.error(`Form field with key ${key} not found!`);
            }
        }
    }

}

function getFormValues(keyList = ["ALL_FORM_KEYS"]) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form not found'
        })
    }

    if (keyList[0] === "ALL_FORM_KEYS") {
        keyList = Object.keys(createEventForm.elements);
    }
    
    for (var key of keyList) {
        var formField = createEventForm.elements[key];

        if (formField) {
            var value = formField.value;
            allFormkeyList[key] = value;
        }
    }
    return allFormkeyList;
}

function previewSelectedImage(imageId, previewImageId) {
    const imageInput = document.getElementById(imageId);
    
    if (!imageInput) {
        throw new Error("Image input not found!")
    }

    const previewImage = document.getElementById(previewImageId);
    
    if (!previewImage) {
        throw new Error("Preview not found!")
    }
    
    const file = imageInput.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            var image = new Image();
            image.src = reader.result;
            var isError = false;
        
            image.onload = function() {
                
                if (image.width <= 1400 ) {
                    Toast.fire({
                        icon: 'error',
                        title: `Image width ${image.width}px is lesser than 1400px`
                    })
                    
                    isError = true;
                    return;
                }
    
                if (image.height <= 600) {
                    Toast.fire({
                        icon: 'error',
                        title: `Image height ${image.height} is lesser than 600px`
                    })
    
                    isError = true;
                    return;
                }

                if (!isError){
                    previewImage.classList.remove("d-none");
                    let element = document.getElementById('preview-image-warning');
                    if (element) element.classList.add('d-none');
                    previewImage.src = e.target.result;
                }
            };

        };
        reader.readAsDataURL(file);
    }
}

function previewSelectedImageByForm(imageId, previewImageId) {
    const imageInput = document.getElementById(imageId);
    if (!imageInput) {
        throw new Error("Image input not found!")
    }

    const previewImage = document.getElementById(previewImageId);
    if (!previewImage) {
        throw new Error("Preview not found!")
    }

    if (previewImage.classList.contains("d-none")) {
        previewImage.classList.remove("d-none");
    }

    previewImage.style.objectFit = "cover";
    const file = imageInput.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (e) {
            previewImage.src = e.target.result;
        }
    }
}

let inputKeyToInputNameMapping = {
    eventName: 'name of the event',
    eventTier: 'tier of the event',
    eventType: 'type of the event',
    gameTitle: 'title of the game',
    eventBanner: 'event image',
    startDate: 'the start date of the event',
    startTime: 'the start time of the event',
    endDate: 'the end date of the event',
    endTime: 'the end time of the event',
    eventDescription: 'the event description',
    launch_date: "launch date",
    launch_time: "launch time",
    launch_schedule: "launch schedule",
    launch_visible: "launch type (public/ private/ draft)",
    isPaymentDone: "payment"
}

let inputKeyToStepNameMapping = {
    eventTier: ['step-3', 'timeline-1'],
    eventType: ['step-2', 'timeline-1'],
    gameTitle: ['step-1', 'timeline-1'],
    eventName: ['step-6', 'timeline-2'],
    eventBanner: ['step-9', 'timeline-2'],
    startDate: ['step-5', 'timeline-2'],
    startTime: ['step-5', 'timeline-2'],
    endDate: ['step-5', 'timeline-2'],
    endTime: ['step-5', 'timeline-2'],
    eventDescription: ['step-7', 'timeline-2'],
    launch_visible: ['step-launch-1', 'timeline-launch'],
    launch_date: ['step-launch-1', 'timeline-launch'],
    launch_time: ['step-launch-1', 'timeline-launch'],
    launch_schedule: ['step-launch-1', 'timeline-launch'],
    isPaymentDone: ['step-payment', 'timeline-payment']
}

function closeDropDown(element, id, keyValues, key) {
    element.parentElement.previousElementSibling.classList.toggle("dropbtn-open");
    element.parentElement.classList.toggle("d-none");
    document.getElementById(id).innerHTML = `
        Selected (${keyValues[key]})
        <span class="dropbtn-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-down">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </span>
    `;
    setFormValues(keyValues)
}

function getElementByIdAndSetInnerHTML(id, innerHTML) {
    let documentElement = document.getElementById(id);
    
    if (documentElement) {
        documentElement.innerHTML = innerHTML;
    } else {
        console.error(`Element with id ${id} not found`);
    }
}

function numberToLocaleString(number) {
    return Number(number).toLocaleString()
}

function saveForLivePreview() {
    var createEventForm = document.forms['create-event-form'];
    setFormValues({ 'livePreview': 'true' });
    createEventForm.submit();
}

function saveEvent(willGoToNextPage = true) {
    let createEventForm = document.forms['create-event-form'];
    let launch_schedule = null;
    let launch_visible = null;
    let launch_date = null;
    let launch_time = null;
    let launch_schedule_form = getFormValues(['launch_visible',
        'launch_schedule',
        'launch_date_public',
        'launch_date_private',
        'launch_time_public',
        'launch_time_public'
    ])
    
    if ('launch_visible' in launch_schedule_form) {
        launch_visible = launch_schedule_form['launch_visible'];
        launch_schedule = launch_schedule_form['launch_schedule'];
    }
    
    if (launch_visible == "public") {
        launch_date = launch_schedule_form['launch_date_public'];
        launch_time = launch_schedule_form['launch_time_public'];
    } else if (launch_visible == "private") {
        launch_date = launch_schedule_form['launch_date_private'];
        launch_time = launch_schedule_form['launch_time_private'];
    }
   
    if (launch_visible == 'DRAFT') {
        createEventForm.submit();
        return;
    }
    
    let isFormValid = true, invalidKey = '', formValidation = null;
    
    formValidation = validateFormValuesPresent([
        'gameTitle', 'eventType', 'eventTier',
        'startDate', 'startTime', 'endDate', 'endTime', 'eventName', 'eventDescription',
    ]);

    if (launch_schedule != 'now' && (launch_date==null || launch_time==null)) {
        isFormValid = false;
        invalidKey = 'launch_date';
    }
    
    if (localStorage.getItem('eventBanner') == null && getFormValues(['eventBanner']) == null) {
        isFormValid = false;
        invalidKey = 'eventBanner';
    }
    
    if (!launch_schedule) {
        isFormValid = false;
        invalidKey = 'launch_schedule';
    }
    
    if (formValidation != null) {
        isFormValid = formValidation[0];
        invalidKey = formValidation[1];
    }
    
    if (!isFormValid) {
        
        Toast.fire({
            icon: 'error',
            text: `Didn't enter ${inputKeyToInputNameMapping[invalidKey] ?? ""}! It is a required field.`
        })
        
        let [nextId, nextTimeline] = inputKeyToStepNameMapping[invalidKey];
        
        goToNextScreen(nextId, nextTimeline);
        
        if (nextId == 'step-payment') {  
            fillStepPaymentValues();
        }

        return;
    } else if (launch_visible != "DRAFT" && willGoToNextPage && launch_schedule == 'now') {
        setFormValues({ 'launch_schedule': 'now' });
        goToNextScreen('step-launch-2', 'timeline-launch');
        return;
    } else {
        createEventForm.submit();
    }
}

function goToNextScreen(nextId, nextTimeline) {
    document.getElementsByClassName("navbar")[0].scrollIntoView({ behavior: 'smooth' });

    const allIDs = [
        'step-0',
        'step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6', 'step-7', 'step-8', 'step-9', 'step-payment', 'step-launch-1',
        'step-launch-2'];

    const allTimelines = ['timeline-1', 'timeline-2', 'timeline-payment', 'timeline-launch'];
    
    let currentId = 'step-0';

    allIDs.forEach(id => {
        const element = document.querySelector(`#${id}`);
        
        if (element && !element.classList.contains("d-none")) {
            currentId = id;
        }
    })

    allTimelines.forEach((timeline, _index) => {
        const paragraph = document.querySelector(`#${timeline} .timestamp span`);
        const cicle = document.querySelector(`#${timeline} small`);
        
        if (timeline == nextTimeline) {
            
            if (!paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.add("font-color-active-timeline");
            
            if (!cicle.classList.contains("background-active-timeline")) cicle.classList.add("background-active-timeline");
        } else {
            if (paragraph.classList.contains("font-color-active-timeline"))
                paragraph.classList.remove("font-color-active-timeline");
            
            if (cicle.classList.contains("background-active-timeline")) cicle.classList.remove("background-active-timeline");
        }
    })

    allIDs.forEach(id => {
        const element = document.querySelector(`#${id}`);
        
        if (id === nextId) {
            element.classList.remove("d-none");
        } else if (element && !element.classList.contains("d-none")) {
            element.classList.add("d-none");
        }
    })

    if (nextId == allIDs[4]) {
        let box = document.getElementById('event-tier-display');
        let eventTierTitle = localStorage.getItem('eventTierTitle') ?? null;
        
        if (eventTierTitle) {
            box.classList.remove("rounded-box-thickness", "rounded-box-turtle", "rounded-box-dolphin", "rounded-box-starfish");
            box.classList.add( 'rounded-box-' + eventTierTitle.toLowerCase());
            box.classList.add('rounded-box-thickness');
        }
    }
}
