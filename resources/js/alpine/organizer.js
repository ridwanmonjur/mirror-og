import Alpine from "alpinejs";
import { DateTime } from "luxon";

const input = document.querySelector("#phone");
window.intlTelInput(input, {
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@22.0.2/build/js/utils.js",
});
const iti = window.intlTelInput.getInstance(input);
const storage = document.querySelector('.profile-storage');
const styles = {
    backgroundStyles: storage.dataset.backgroundStyles,
    fontStyles: storage.dataset.fontStyles
};

let initialUserProfile = JSON.parse(document.getElementById('initialUserProfile').value);
let initialOrganizer = JSON.parse(document.getElementById('initialOrganizer').value);
let initialAddress = JSON.parse(document.getElementById('initialAddress').value);

if (initialUserProfile?.mobile_no) iti.setNumber(initialUserProfile.mobile_no);


function alpineProfileData(userId) {
    return () => ({
        role: "ORGANIZER",
        userId: userId,
        profile: {},
        connections: [],
        count: 0,
        page: 0,
        next_page: false,
        initialFetch: false,
        async openModal() {
            if (!this.connections[0] && !this.initialFetch) {
                await this.loadPage(1);
            }

            let modalElement = document.getElementById("connectionModal");
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            this.initialFetch = true;
        },

        async loadNextPage() {
            if (this.next_page) {
                await this.loadPage(this.page + 1);
            }
        },
        formatDate(date) {
            return DateTime
                .fromISO(date)
                .toRelative();
        },

        async loadPage(page) {
            try {
                const response = await fetch(
                    `/api/user/${this.userId}/connections?type=followers&page=${page}&role=${this.role}`
                );

                const data = await response.json();
                let { followers } = data?.connections;
                if (!followers) return;
                if (followers) {
                    if (this.page) {
                        this.page += 1;
                        this.connections = [
                            ...this.connections,
                            ...followers.data
                        ];

                    } else {
                        this.page = 1;
                        this.connections = [
                            ...followers.data
                        ];
                    }

                    this.next_page = followers?.next_page_url ?
                        followers?.next_page_url : false;
                }
            } catch (error) {
                console.error('Failed to load page:', error);
            }
        },

    });
}

Alpine.data('profileStatsData', alpineProfileData(initialUserProfile.id));

Alpine.data('alpineDataComponent', function () {
    return ({
        isEditMode: false,
        userProfile: { ...initialUserProfile },
        organizer: { ...initialOrganizer },
        address: { ...initialAddress },
        errorMessage: null,
        processUrl: (url) => {
            if (!url) return;
            url = url.replace(/\/+$/, '');
            url = url.replace(/^(https?:\/\/)?(www\.)?/i, '');
            return url;
        },
        reset() {
            this.userProfile = { ...initialUserProfile };
            this.organizer = { ...initialOrganizer };
            this.address = { ...initialAddress };
        },
        async submitEditProfile(event) {
            try {
                this.errorMessage = null;
                event.preventDefault();
                this.userProfile.mobile_no = iti.getNumber();
                console.log({ number: iti.getNumber() });

                if (!iti.isValidNumber()) {
                    if (document.getElementById("phone").value.trim() == "") {
                        this.userProfile.mobile_no = null;
                    } else {
                        this.errorMessage = 'Valid phone number with country code is not chosen!'
                        return;
                    }
                }

                const url = event.target.dataset.url;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: window.loadBearerCompleteHeader(),
                    body: JSON.stringify({
                        address: Alpine.raw(this.address),
                        userProfile: Alpine.raw(this.userProfile),
                        organizer: Alpine.raw(this.organizer)
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
            var backgroundStyles = styles.backgroundStyles;
            var fontStyles = styles.fontStyles;
            var banner = document.getElementById('backgroundBanner');
            banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
            banner.querySelectorAll('.form-control').forEach((element) => {
                element.style.cssText += fontStyles;
            });

            banner.querySelectorAll('.followCounts').forEach((element) => {
                element.style.cssText += fontStyles;
            });


        },

    })
})

Alpine.start();