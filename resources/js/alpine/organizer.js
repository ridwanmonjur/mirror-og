import Alpine from "alpinejs";
import { DateTime } from "luxon";
import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import { alpineProfileData, openModal, reportFormData } from "../custom/followers";

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

const myOffcanvas = document.getElementById('profileDrawer');

myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    resetBg(initialUserProfile?.profile ?? null);
})

initOffCanvasListeners();



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
                    headers: {
                        credentials: 'include',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
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

        },

    })
})

const {loggedUserId, loggedUserRole} = document.querySelector('#routeContainer').dataset;

Alpine.data('profileData', alpineProfileData(initialUserProfile.id, loggedUserId, false, "ORGANIZER", loggedUserRole));
Alpine.data('reportData', reportFormData());


window.openModal = openModal;
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

Alpine.start();