import { reactive } from "petite-vue";
import { DateTime } from "luxon";

let notificationColors = {
    social: '#8ccd39',
    teams: '#43A4D7',
    event: 'red'
};

const importantUrlsDiv = document.getElementById('importantUrls');
let {
    socialCount,
    teamsCount,
    eventCount, 
} = importantUrlsDiv.dataset;


function  createVisibleCircles(counter) {
    const circles = [];
    if (counter.socialCount > 0) {
        circles.push({ color: notificationColors.social, position: makePosition(0, counter.countsGreaterThanZero) })
    };

    if (counter.teamsCount > 0) {
        circles.push({ color: notificationColors.teams, position: makePosition(1, counter.countsGreaterThanZero) })
    };

    if (counter.eventCount > 0) {
        circles.push({ color: notificationColors.event, position: makePosition(2, counter.countsGreaterThanZero) })
    };

    return circles;
}

function makePosition(index, totalCircles) {
    if (totalCircles === 1) return 10;
    if (totalCircles === 2) return [2, 8, 14][index];
    return [2, 8, 14][index];
}

function createCounterAndCircles() {
    let counter = {
        socialCount,
        teamsCount,
        eventCount,
        countsGreaterThanZero: (socialCount > 0) +  (teamsCount > 0) + (eventCount > 0)
    }
    return {
        counter,
        items: {
            social: [],
            teams: [],
            event: []
        },
        page: {
            social: 0,
            teams: 0,
            event: 0
        },
        hasMore: {
            social: false,
            teams: false,
            event: false
        },
        visibleCircles: createVisibleCircles(counter),
    }
}

let allNotificationStore = reactive({
    ...createCounterAndCircles(),

    async markNotificationRead(id, currentTab) {
        let url = `/api/notifications/${id}`;
        const response = await fetch( url,  {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        if (!data.success) {
            window.toastError("Notifications have failed!")
            return;
        }

        this.items = Object.keys(this.items).reduce((acc, key) => {
            // Only update the array for the currentTab, keep others as is
            acc[key] = key === currentTab 
                ? this.items[key].map((value) => {
                    return value.id === id 
                        ? { ...value, is_read: true }
                        : value;
                  })
                : this.items[key];
            return acc;
        }, {});

        let newCounterTabName = currentTab + 'Count';
        let newCount = this.counter[newCounterTabName] - 1;
        let countsGreaterThanZero = this.counter['countsGreaterThanZero'];
        
        this.counter = {
            ...this.counter,
            [newCounterTabName]: newCount,
            countsGreaterThanZero
        }
        
        if (newCount <= 0) {
            countsGreaterThanZero -= 1;
            this.visibleCircles = [...createVisibleCircles({
                ...this.counter
            })];
        } 
    },

    async loadFirstPage (tab) {
        if (this.page[tab] <= 0) {
            this.loadPage(tab, 1);
        }
    },

    async loadOtherPagePage (tab) {
        let page = this.page[tab] + 1;
        this.loadPage(tab, page);
    },
    
    async loadPage(tab, page) {
        this.page = {
            ...this.page,
            [tab]: page
        }

        try {
            let url = `/api/notifications?type=${tab}&page=${page}`;
            const response = await fetch( url );
            const data = await response.json();

            if (!data.success) {
                this.page = {
                    ...this.page,
                    [tab]: page-1
                }
                
                return;
            }

            this.items = Object.keys(this.items).reduce((acc, key) => {
                acc[key] = key === tab 
                    ? [
                        ...this.items[key],
                        ...data.data[key]
                    ]
                    : this.items[key];
                return acc;
            }, {});

            this.hasMore = {
                ...this.hasMore,
                [tab]: data.hasMore
            }

          
        } catch (error) {
            this.page = {
                ...this.page,
                [tab]: page-1
            }
            console.error('Failed to load page:', error);
        }
    },
});

const iconStore = {
    // Team related icons
    confirm: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
    
    vote: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#17a2b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>`,
    
    quit: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>`,
    
    stay: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 2h-4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4"></path>
        <path d="M7 22H3a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h4"></path>
        <path d="M7 11V3a2 2 0 0 1 2-2h2"></path>
    </svg>`,

    signup: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>`,
    
    trophy: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>`,
    
    // Social related icons
    friend: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#6f42c1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>`,
    
    follow: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#20c997" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>`,
    
    message: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fd7e14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>`,
    
    // Event related icons
    calendar: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#e83e8c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>`,
    
    live: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"></path><rect x="3" y="5" width="14" height="14" rx="2" ry="2"></rect></svg>`
};

const tabStore = reactive({
    currentTab: 'social',
  
    changeNotificationTab(tabName) {
        this.currentTab = tabName;
    },
});



   
function PageNotificationComponent () {
    return {
        notificationColors,

        async loadFirstPage(){
            await allNotificationStore.loadFirstPage(this.currentTab);
        },

        changeNotificationTab(tabName) {
            this.currentTab = tabName;
        },
        
        get visibleCircles() {
            return allNotificationStore.visibleCircles
        },

        get counter() {
            return allNotificationStore.counter;
        },

        get hasMore() {
            return allNotificationStore.hasMore[tabStore.currentTab]
        },

        get notificationList () {
            return allNotificationStore.items[tabStore.currentTab];
        },

        async loadNextPage() {
            await allNotificationStore.loadOtherPagePage(tabStore.currentTab);
        },

        init() {
            this.loadFirstPage();
        },
       

        get currentTab () {
            return tabStore.currentTab;
        },

        async fetchMoreNotifications () {

        },
        
        async markNotificationRead(event, id, link) {
            await allNotificationStore.markNotificationRead(id, tabStore.currentTab);
            let target = event.target;
            if (target.tagName === 'A') {
                window.open(
                    target.dataset.href,
                    'noopener,noreferrer',
                ); 
                return; 
            }

            window.open(
                link,
                'noopener,noreferrer',
            );        
        },

        async changeNotificationTab(tabName) {
            try {
                tabStore.changeNotificationTab(tabName);
                this.loadFirstPage();
            } catch (error) {
                console.error(error);
                toastError('Failed to load notifications');
            }
        },
       
        getIconSvg(icon_type) {
            return iconStore[icon_type] || '';
        },
        formatTime(date) {
            return  DateTime
                .fromISO(date)
                .toRelative();
        }
    }
}

export {
    PageNotificationComponent,
};




