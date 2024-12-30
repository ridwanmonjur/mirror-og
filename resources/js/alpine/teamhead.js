import Alpine from "alpinejs";
import { DateTime } from "luxon";
import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import { alpineProfileData, openModal } from "../custom/followers";

const myOffcanvas = document.getElementById('profileDrawer');
myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    resetBg(teamData?.profile ?? null);
})

initOffCanvasListeners();


Alpine.data('alpineDataComponent', () => ({
    select2: null,
    isEditMode: false,
    ...teamData,
    country: teamData?.country,
    errorMessage: '',
    isCountriesFetched: false,
    countries:
        [
            {
                name: { en: 'No country' },
                emoji_flag: ''
            }
        ],
    errorMessage: errorInput?.value,
    changeFlagEmoji() {
        let countryX = Alpine.raw(this.countries || []).find(elem => elem.id == this.country);
        this.country_name = countryX?.name.en;
        this.country_flag = countryX?.emoji_flag;
    },
    async fetchCountries() {
        if (this.isCountriesFetched) return;
        try {
            const data = await storeFetchDataInLocalStorage('/countries');

            if (data?.data) {
                this.isCountriesFetched = true;
                this.countries = data.data;

                const choices2 = document.getElementById('select2-country3');
                let countriesHtml = "<option value=''>Choose a country</option>";

                data?.data.forEach((value) => {
                    countriesHtml += `
                        <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                    `;
                });
                if (choices2) {
                    choices2.innerHTML = countriesHtml;
                    let option = choices2.querySelector(`option[value='${this.country}']`);
                    option.selected = true;

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
            event.preventDefault();
            const url = event.target.dataset.url;
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
                    country_name: this.country_name
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
                window.location.replace(currentUrl);
            } else {
                this.errorMessage = data.message;
            }
        } catch (error) {
            this.errorMessage = error.message;
            console.error({ error });
        }
    },
    reset() {
        Object.assign(this, teamData);
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
})
);


window.formatDateLuxon = (date) => {
    if (!date) return 'N/A';
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

let role = "TEAM";
const storage = document.querySelector('.team-head-storage');
const loggedUserId = storage.dataset.loggedUserId;
Alpine.data('profileData', alpineProfileData(teamData.id, loggedUserId, false, role));
window.openModal = openModal;
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

Alpine.start();
