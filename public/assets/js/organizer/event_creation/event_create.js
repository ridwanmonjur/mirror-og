function openDropDown(element) {
    element.classList.toggle("dropbtn-open");
    element.nextElementSibling.classList.toggle("d-none");
}

function toggleRadio(_input, message) {
    const elements = document.querySelectorAll(`.radio-indent-hidden`);
    
    elements.forEach(element => {
        if (element.classList.contains(message)) {
            element.classList.remove("d-none");
        } else element.classList.add("d-none");
    })
}

function launchScheduleDefaultSelected(className) {
    let element1 = document.querySelector(`input[type='radio'].${className}`);
    
    if (element1){
        element1.checked = true;
    }
}

function addEvent(button) {
    button.parentElement.parentElement.classList.add("d-none");
    const feedbackElement = document.querySelector("#feedback");
    const headingElement = document.querySelector("#heading");
    const notificationElement = document.querySelector("#notification");
    const descriptionElement = document.querySelector("#description");
    feedbackElement.classList.remove("d-none");
    headingElement.innerHTML = "All done";
    notificationElement.innerHTML = "Your event has been launched to the world!";
    descriptionElement.innerHTML = "You will be notified as players join your event.";
}

function chooseEventType(button) {
    const eventTypes = document.querySelectorAll(".event-type");
    
    eventTypes.forEach(eventType => {
        if (eventType.classList.contains("d-none")) {
            eventType.classList.remove("d-none");
        } else eventType.classList.add("d-none");
    })
    
    button.parentElement.classList.add("d-none");
}

function updateLaunchButton(type) {
    var launchButton = document.getElementById('launch-button');

    switch (type) {
        case 'launch':
            launchButton.innerText = 'Launch';
            launchButton.removeEventListener('click', goToPaymentPage);
            launchButton.addEventListener('click', goToLaunch2ndPage);
            break;

        case 'schedule':
            launchButton.innerText = 'Step 4';
            launchButton.removeEventListener('click', goToLaunch2ndPage);
            launchButton.addEventListener('click', goToPaymentPage);
            break;

        case 'draft':
            launchButton.innerText = 'Step 4';
            launchButton.removeEventListener('click', goToLaunch2ndPage);
            launchButton.addEventListener('click', goToPaymentPage);
            break;
    }
}

const choices2 = document.getElementById('select2-country2');
async function fetchCountries () {
    try {
        const data = await storeFetchDataInLocalStorage('/countries');
        if (data?.data) {
            countries = data.data;
            let regionsHtml = "";
            let countriesHtml = "";
            let countriesOptionsHtml = "";

        countriesHtml += "<option value=''>No country</option>";

        countries.forEach((value) => {
            if (value.type === 'region') {
                regionsHtml += `
                    <option value='${value.id}' class="emoji-text">${value.emoji_flag} ${value.name}</option>
                `;
            } else if (value.type === 'country') {
                countriesOptionsHtml += `
                    <option value='${value.id}' class="emoji-text">${value.emoji_flag} ${value.name}</option>
                `;
            }
        });

            // Add regions optgroup if there are regions
            if (regionsHtml) {
                countriesHtml += "<optgroup label='Regions'>";
                countriesHtml += regionsHtml;
                countriesHtml += "</optgroup>";
            }

            // Add countries optgroup if there are countries
            if (countriesOptionsHtml) {
                countriesHtml += "<optgroup label='Countries'>";
                countriesHtml += countriesOptionsHtml;
                countriesHtml += "</optgroup>";
            }

            if (choices2) {
                choices2.innerHTML = countriesHtml;
            }

            choices2.innerHTML = countriesHtml;
        } else {
            errorMessage = "Failed to get data!";
        }
    } catch (error) {
        console.error('Error fetching countries:', error);
    }
}

if (choices2) {
    fetchCountries();

}