import { DateTime } from "luxon";
import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import { ProfileData, openModal, ReportFormData } from "../custom/followers";
import { createApp } from "petite-vue";

const myOffcanvas = document.getElementById('profileDrawer');
myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    resetBg(teamData?.profile ?? null);
})

initOffCanvasListeners();
teamData.fontColor = teamData?.profile?.fontColor ?? '#2e4b59';
teamData.backgroundColor = teamData?.profile?.backgroundColor ?? 'darkgray';

let imageUpload = document.getElementById("image-upload");

function TeamHead() {
    return {
        select2: null,
        isEditMode: false,
        ...teamData,
        country: teamData?.country,
        errorMessage: '',
        isCountriesFetched: false,
        countries:
            [
                {
                    name:  'No country' ,
                    emoji_flag: ''
                }
            ],
        errorMessage: errorInput?.value,
        changeFlagEmoji(event) {
            this.country = event.target.value;
            const countryX = this.countries?.find(c => c.id === this.country)
            this.country_name = countryX?.name || null
            this.country_flag = countryX?.emoji_flag || null
        },
        async fetchCountries() {
            if (this.isCountriesFetched) return;
            try {
                const data = await storeFetchDataInLocalStorage('/countries');
               
                if (data?.data) {
                    this.isCountriesFetched = true;
                    this.countries = data.data;
                    const choices2 = document.getElementById('select2-country3');
                    
                    let countriesHtml = "<option value=''>Choose a country/region</option>";

                    let regionsHtml = "";
                    let countriesOptionsHtml = "";

                    // Single loop through all data
                    data?.data.forEach((value) => {
                        if (value.type === 'region') {
                            regionsHtml += `
                                <option value='${value.id}'>${value.emoji_flag} ${value.name}</option>
                            `;
                        } else if (value.type === 'country') {
                            countriesOptionsHtml += `
                                <option value='${value.id}'>${value.emoji_flag} ${value.name}</option>
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
                        let option = choices2.querySelector(`option[value='${this.country}']`);
                        if (option) option.selected = true;
                    }
                } else {
                    this.errorMessage = "Failed to get data!";
                }
            } catch (error) {
                console.error('Error fetching countries:', error);
            }
        },
        async submitEditProfile(event) {
            try {
                window.showLoading();
                event.preventDefault();
                let file = imageUpload.files[0];
                let fileFetch = null;
                if (file) {
                    const fileContent = await readFileAsBase64(file);

                    fileFetch = {
                        filename: file.name,
                        type: file.type,
                        size: file.size  / (1024 * 1024),
                        content: fileContent
                    };
                }
                

                const url = event?.target?.dataset?.url;
                if (!url) {
                    console.error('No URL found');
                    console.log({url});
                    return;
                }
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken77,
                        'Content-type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        id: this.id,
                        teamName: this.teamName,
                        teamDescription: this.teamDescription,
                        country: this.country,
                        country_flag: this.country_flag,
                        country_name: this.country_name,
                        ...(fileFetch && { file: fileFetch })
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    let currentUrl = window.location.href;
                    if (currentUrl.includes('?')) {
                        currentUrl = currentUrl.split('?')[0];
                    }

                    localStorage.setItem('success', true);
                    localStorage.setItem('message', data.message);
                    window.closeLoading();
                    window.location.replace(currentUrl);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                let errorMessage = error.response?.data?.message || error.message || 'Failed to process your request. Please try again later.';

                this.errorMessage = errorMessage;
                window.closeLoading();
            }
        },
        reset() {
            Object.assign(this, teamData);
            document.querySelectorAll('.uploaded-image').forEach((element) => {
                element.style.backgroundImage = `url(/storage/${teamData.teamBanner})`;
            })
        },
        init() {
            this.fetchCountries();
            var backgroundStyles = styles.backgroundStyles;
            var fontStyles = styles.fontStyles;
            var banner = document.getElementById('backgroundBanner');
            banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
            banner.querySelectorAll('.form-control').forEach((element) => {
                element.style.cssText += fontStyles;
            });


            banner.querySelectorAll(".form-color").forEach((element) => {
                element.style.cssText += fontStyles;
                if (teamData?.profile?.borderColor) {
                    element.style.border = `1px solid ${teamData.profile.borderColor}`;
                }
            });
           
        }
    }
}


window.formatDateLuxon = (date) => {
    if (!date) return 'Not available';
    return  DateTime
        .fromISO(date)
        .toRelative();
}

window.formatDateMySqlLuxon = (mysqlDate, mysqlTime) => {
    const dateTime = DateTime.fromSQL(`${mysqlDate} ${mysqlTime}`);
    const formattedDate = dateTime.toFormat("d MMM yyyy");
    const formattedTime = dateTime.toFormat("h:mma").toUpperCase();
    
    return `${formattedDate} at ${formattedTime}`;
}




window.openModal = openModal;
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        TeamHead,
        ProfileData,
        ReportFormData,
    }).mount('.teamhead');
});

