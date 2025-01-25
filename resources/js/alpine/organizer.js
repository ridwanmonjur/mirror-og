import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import intlTelInput from 'intl-tel-input';
import utilsScript from "intl-tel-input/utils";
import { ProfileData, openModal, ReportFormData } from "../custom/followers";
import { createApp } from "petite-vue";


const storage = document.querySelector('.profile-storage');
const styles = {
    backgroundStyles: storage.dataset.backgroundStyles,
    fontStyles: storage.dataset.fontStyles
};

let initialUserProfile = JSON.parse(document.getElementById('initialUserProfile').value);
let initialOrganizer = JSON.parse(document.getElementById('initialOrganizer').value);
let initialAddress = JSON.parse(document.getElementById('initialAddress').value);
console.log({number222: initialUserProfile.mobile_no})
const input = document.querySelector("#phone");
let iti = intlTelInput(input, { 
    loadUtils: () => import("intl-tel-input/utils"),
});
console.log({input, utilsScript, intlTelInput, iti})
input.addEventListener('input', () => {
    console.log({
        inputValue: input.value,
        itiValue: iti.getNumber(),
        isValid: iti.isValidNumber()
    });
});

if (initialUserProfile?.mobile_no) 
    iti.setNumber(initialUserProfile.mobile_no);

const myOffcanvas = document.getElementById('profileDrawer');

myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    resetBg(initialUserProfile?.profile ?? null);
})

initOffCanvasListeners();



function OrganizerData() {
    return {
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
                        address: this.address,
                        userProfile: this.userProfile,
                        organizer: this.organizer
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
                this.errorMessage = error.response?.data?.message || error.message || 'Failed to process your request. Please try again later.';
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

    }
}

window.openModal = openModal;
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        OrganizerData,
        ProfileData,
        ReportFormData
    }).mount('#app');

});