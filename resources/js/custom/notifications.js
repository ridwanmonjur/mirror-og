import { reactive } from "petite-vue";
import { DateTime } from "luxon";

let notificationColors = {
    social: '#8ccd39',
    teams: '#43A4D7',
    event: 'red'
};

let allNotificationStore = reactive({
    items: {
        social: [
            {
                id: 1,
                iconType: 'friend',
                html: '<span class="text-primary">serenity</span> sent you a friend request.',
                createdAt: '2024-02-08T09:42:00',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                isRead: false
            },
            {
                id: 2,
                imageSrc: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                html: 'The Royal Prince of Saudi is now following you.',
                createdAt: '2024-02-08T09:58:00',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                isRead: true
            },
            {
                id: 3,
                iconType: 'message',
                html: 'You have new messages from <span class="text-primary">serenity</span>',
                createdAt: '2024-02-05T10:00:00',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                isRead: false
            },
            {
                id: 4,
                iconType: 'follow',
                html: '<span class="text-primary">serenity</span> is now following you.',
                createdAt: '2024-02-05T09:00:00',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                isRead: true
            }
        ],
        teams: [
            {
                id: 5,
                iconType: 'confirm',
                html: 'Awesome Team has confirmed registration for <span class="text-primary">The Super Duper Dota League</span>.',
                createdAt: '2024-02-08T10:00:00',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                isRead: true
            },
            {
                id: 6,
                iconType: 'vote',
                html: 'Awesome Team has voted to STAY in <span class="text-primary">The Super Duper Dota League</span>.',
                createdAt: '2024-02-08T09:00:00',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                isRead: true
            },
            {
                id: 7,
                iconType: 'quit',
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                html: 'A vote to quit <span class="text-primary">The Super Duper Dota League</span> has been called for Awesome Team.',
                createdAt: '2024-02-07T22:00:00',
                isRead: false
            },
            {
                id: 111,
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                imageSrc: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                html: 'The Royal Prince of Saudi has followed your team.',
                createdAt: '2024-02-08T09:58:00',
                isRead: true
            },
        ],
        event: [
            {
                id: 8,
                iconType: 'calendar',
                html: '<span class="text-primary">The Super Duper Dota League</span> has been rescheduled.',
                createdAt: '2024-02-08T09:00:00',
                isRead: false,
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
            },
            {
                id: 9,
                iconType: 'live',
                html: '<span class="text-primary">The Great CNY Dota Bash</span> has gone live!',
                createdAt: '2024-02-07T23:00:00',
                isRead: true,
                link: 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
            }
        ]
    },
    async markNotificationRead(id, currentTab) {
        this.items = Object.keys(this.items).reduce((acc, key) => {
            // Only update the array for the currentTab, keep others as is
            acc[key] = key === currentTab 
                ? this.items[key].map((value) => {
                    return value.id === id 
                        ? { ...value, isRead: true }
                        : value;
                  })
                : this.items[key];
            return acc;
        }, {});
    }
});

const iconStore = {
    // Team related icons
    confirm: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
    
    vote: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#17a2b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>`,
    
    quit: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>`,
    
    signup: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>`,
    
    trophy: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>`,
    
    // Social related icons
    friend: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6f42c1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>`,
    
    follow: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#20c997" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>`,
    
    message: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fd7e14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>`,
    
    // Event related icons
    calendar: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e83e8c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>`,
    
    live: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"></path><rect x="3" y="5" width="14" height="14" rx="2" ry="2"></rect></svg>`
};

const tabStore = reactive({
    currentTab: 'social',
    changeNotificationTab(tabName) {
        this.currentTab = tabName;
    },
});


const notificationStore = reactive({
    get notificationList() {
        return allNotificationStore.items[tabStore.currentTab];
    },
    notificationPage: 0,

    async fetchMoreNotifications () {

    },
    async markNotificationRead(id) {
        allNotificationStore.markNotificationRead(id, tabStore.currentTab);
    },
});


   
function PageNotificationComponent () {
    return {
        notificationColors,
        get notificationList () {
            return notificationStore.notificationList;
        },

        get currentTab () {
            return tabStore.currentTab;
        },

        async fetchMoreNotifications () {

        },
        
        async markNotificationRead(id, link) {
            await notificationStore.markNotificationRead(id);
            window.open(link, '_blank');
        },
        async changeNotificationTab(tabName) {
            try {
                tabStore.changeNotificationTab(tabName);
            } catch (error) {
                toastError('Failed to update email');
            }
        },
        init() {
            console.log({notificationList: this.notificationList});
            console.log({notificationList: this.notificationList});
            console.log({notificationList: this.notificationList});
        },
        getIconSvg(iconType) {
            return iconStore[iconType] || '';
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




