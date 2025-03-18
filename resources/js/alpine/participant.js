import { DateTime } from "luxon";
import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import { ProfileData, openModal, ReportFormData } from "../custom/followers";
import { createApp } from "petite-vue";

let userData = JSON.parse(document.getElementById('initialUserData')?.value);
let participantData = JSON.parse(document.getElementById('initialParticipantData')?.value);
const imageUpload = document.getElementById("image-upload");

const myOffcanvas = document.getElementById('profileDrawer');
myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    resetBg(userData?.profile ?? null);
})

initOffCanvasListeners();

const {
    userProfileId: userId,
    backgroundStyles,
    fontStyles,
    loggedUserId,
} = document.querySelector('.laravel-data-storage').dataset;

console.log({loggedUserId});

function ParticipantData ()  {
    return {
        select2: null,
        isEditMode: false,
        countries:
            [
                {
                    name: { en: 'No country' },
                    emoji_flag: ''
                }
            ],
        user: { ...userData },
        participant: { ...participantData },
        errorMessage: errorInput?.value,
        isCountriesFetched: false,
        changeFlagEmoji() {
            let countryX = this.countries?.find(elem => elem.id == this.participant.region);
            if (countryX) {
                this.participant.region_name = countryX.name.en;
                this.participant.region_flag = countryX.emoji_flag;
            } else {
                this.participant.region_name = null;
                this.participant.region_flag = null;
            }
        },
       
        restoreAfterEditMode() {
            this.isEditMode = false;
            this.reset();
            console.log({userData});
            document.querySelectorAll('.uploaded-image').forEach((element) => {
                element.style.backgroundImage = `url(/storage/${userData.userBanner})`;
            })
        },
        reset() {
            this.user = {...userData};
            this.participant = {...participantData};
          
        },
        async fetchCountries() {
            if (this.isCountriesFetched) return;
            try {
                const data = await storeFetchDataInLocalStorage('/countries');
                if (data?.data) {
                    this.isCountriesFetched = true;
                    this.countries = data.data;
                    const choices2 = document.getElementById('select2-country3');

                    let countriesHtml = "<option value=''>Do not display</option>";
                    data?.data.forEach((value) => {
                        countriesHtml += `
                        <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                    `;
                    });
                    if (choices2) {
                        choices2.innerHTML = countriesHtml;
                        choices2.selected = this.participant.region;
                    }
                } else {
                    this.errorMessage = "Failed to get data!";
                }
            } catch (error) {
                console.error('Error fetching countries:', error);
            }
        },
      
        async submitEditProfile(event) {
            event.preventDefault();
            const url = event.target.dataset.url;
            this.participant.age = Number(this.participant.age);

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

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        credentials: 'include',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        participant: this.participant,
                        user: this.user,
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
                    window.location.reload();

                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                let errorMessage = error.response?.data?.message || error.message || 'Failed to process your request. Please try again later.';

                this.errorMessage = errorMessage;

            }
        },

        init() {
            var banner = document.getElementById('backgroundBanner');
            banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
            this.fetchCountries();
        }

    }
}

function ActivityLogs(userId, duration) {
    return {  
        items: [],
        hasMore: true,
        page: 1,
        userId,
        duration,

        async init() {
            await this.loadData();
        },

        async loadData () {
            try {
                const response = await fetch(
                    `/api/activity-logs?userId=${userId}&duration=${this.duration}&page=${this.page}`
                );
                const data = await response.json();
                
                this.items = [...this.items, ...data.items];
                this.hasMore = data.hasMore;
                this.page++;
            } catch (error) {
                console.error('Error loading activity logs:', error);
            }
        },

        async loadMore(event) {
            let button = event.currentTarget;
            if (button.disabled) return;
            button.setAttribute("disabled", true);
            try {
                await this.loadData();
            } catch (error) {
                console.error('Error loading activity logs:', error);
            } finally {
                setTimeout(() => {
                    button.removeAttribute("disabled");
                }, 2000);            
            }
            
        },

        formatDate(date) {
            return  DateTime
                .fromISO(date)
                .toRelative();
        }
    }
}


function ProfileCount(userId, role) {
    return {
        count: {},
        loading: true,
        
        async init() {
            await this.loadInitialData();
        },
        
        async loadInitialData() {
            try {
                const response = await fetch(`/api/user/${userId}/connections?type=all&role=${role}`);
                const data = await response.json();
                this.count = data.count;
                this.loading = false;
            } catch (error) {
                console.error('Failed to load profile data:', error);
            }
        },
        
        openModal(type) {
            openModal(type);
        },
    };
}

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        ParticipantData,
        ProfileData,
        ReportFormData,
        ProfileCount,
        ActivityLogs
    }).mount('#app');

});

async function readFileAsBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = function (event) {
            const base64Content = event.target.result.split(';base64,')[1];
            resolve(base64Content);
        };

        reader.onerror = function (error) {
            reject(error);
        };

        reader.readAsDataURL(file);
    });
}


