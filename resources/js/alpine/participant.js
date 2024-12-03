import Alpine from "alpinejs";
import { DateTime } from "luxon";

let userData = JSON.parse(document.getElementById('initialUserData').value);
let participantData = JSON.parse(document.getElementById('initialParticipantData').value);

const {
    userProfileId: userId,
    backgroundStyles,
    fontStyles,
   
} = document.querySelector('.laravel-data-storage').dataset;

Alpine.data('profileDataComponent', () => {
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
            let countryX = Alpine.raw(this.countries || []).find(elem => elem.id == this.participant.region);
            this.participant.region_name = countryX?.name.en;
            this.participant.region_flag = countryX?.emoji_flag;
        },
        reset() {
            this.user = userData;
            this.participant = participantData;
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
            try {
                event.preventDefault();
                const url = event.target.dataset.url;
                this.participant.age = Number(this.participant.age);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        credentials: 'include',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        participant: Alpine.raw(this.participant),
                        user: Alpine.raw(this.user)
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

        init() {
            var banner = document.getElementById('backgroundBanner');
            banner.style.cssText += `${backgroundStyles} ${fontStyles}`;


            this.$watch('participant.birthday', value => {
                const today = new Date();
                const birthDate = new Date(value);
                this.participant.age = today.getFullYear() - birthDate.getFullYear();
                const monthDifference = today.getMonth() - birthDate.getMonth();

                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                    this.participant.age--;
                }
            });

            this.fetchCountries();

        }

    }
});

function createActivityLogsData(userId, duration) {
    return () => ({  
        items: [],
        hasMore: true,
        page: 1,
        userId,
        duration,

        async init() {
            await this.loadMore();
        },

        async loadMore() {
            try {
                const response = await fetch(
                    `/api/activity-logs?userId=${this.userId}&duration=${this.duration}&page=${this.page}`
                );
                const data = await response.json();
                
                this.items = [...this.items, ...data.items];
                this.hasMore = data.hasMore;
                this.page++;
            } catch (error) {
                console.error('Error loading activity logs:', error);
            }
        },

        formatDate(date) {
            return  DateTime
                .fromISO(date)
                .toRelative();
        }
    });
}

const activityTypes = JSON.parse(document.getElementById('activity_input')?.value || "[]");

activityTypes.forEach(type => {
    Alpine.data(`${type}Activities`, createActivityLogsData(userId, type));
});

function alpineProfileData(userId) {
    return () => ({
        role: "PARTICIPANT",
        userId: userId,
        profile: {},
        connections: {},
        count: {},
        page: {},
        next_page: {},
        currentTab: 'followers',
        
        async init() {
            await this.loadInitialData();
        },
        
        async loadInitialData() {
            try {
                const response = await fetch(`/api/user/${this.userId}/connections?type=all&role=${this.role}`);
                const data = await response.json();
                this.count = data.count;
            } catch (error) {
                console.error('Failed to load profile data:', error);
            }
        },
        
        async openModal(type) {
            this.currentTab = type;

            if (!this.connections[type]) {
                await this.loadPage(1);
            } 
            
            let modalElement = document.getElementById("connectionModal");
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
        },

        async loadNextPage(){
            if (this.next_page[this.currentTab]) {
                await this.loadPage(this.page[this.currentTab] + 1);
            }
        },

        async loadPage(page) {
            try {
                let tab = this.currentTab;
                const response = await fetch(
                    `/api/user/${this.userId}/connections?type=${tab}&page=${page}&role=${this.role}`
                );
                const data = await response.json();
                if (tab in data.connections)  {
                    if (this.page[tab]) {
                        let newTab = this.page[tab];
                        newTab++;
                        this.page = {
                            ...this.page,
                            [tab]: newTab
                        };

                        this.connections = {
                            ...this.connections,
                            [tab]: [
                                ...this.connections[tab],
                                ...data.connections[tab].data
                            ]
                        } ;
                       
                       
                    } else {
                        this.page = {
                            ...this.page,
                            [tab]: 1
                        };

                        this.connections = {
                            ...this.connections,
                            [tab]: data.connections[tab].data
                        } ;
                       
                    }
                  

                    this.next_page = {
                        ...this.next_page,
                        [tab]:  data.connections[tab]?.next_page_url != null ?
                        true: false
                    }; 

               
                }
            } catch (error) {
                console.error('Failed to load page:', error);
            }
        },
        formatDate(date) {
            return  DateTime
                .fromISO(date)
                .toRelative();
        }

    });
}

Alpine.data('profileStatsData', alpineProfileData(userId));


Alpine.start();