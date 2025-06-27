import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import intlTelInput from 'intl-tel-input';
import utilsScript from "intl-tel-input/utils";
import { ProfileData, openModal, ReportFormData } from "../custom/followers";
import { createApp } from "petite-vue";


const storage = document.querySelector('.profile-storage');
const styles = {
    backgroundStyles: storage.dataset.backgroundStyles,
    fontStyles: storage.dataset.fontStyles,
};

const imageUpload = document.getElementById("image-upload");
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

initialUserProfile.fontColor = initialUserProfile?.profile?.fontColor ?? '#2e4b59';
initialUserProfile.backgroundColor = initialUserProfile?.profile?.backgroundColor ?? '#f4f2f2';


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
            
            document.querySelectorAll('.uploaded-image').forEach((element) => {
                element.style.backgroundImage = `url(/storage/${initialUserProfile.userBanner})`;
            })
        },
        async submitEditProfile(event) {
            try {
                window.showLoading();
                this.errorMessage = null;
                event.preventDefault();
                this.userProfile.mobile_no = iti.getNumber();

                if (!iti.isValidNumber()) {
                    if (document.getElementById("phone").value.trim() == "") {
                        this.userProfile.mobile_no = null;
                    } else {
                        window.closeLoading();
                        this.errorMessage = 'Valid phone number with country code is not chosen!'
                        return;
                    }
                }

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
                        organizer: this.organizer,
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
                    window.location.replace(currentUrl);
                    window.closeLoading();
                } else {
                    throw new Error(data.message);
                }

            } catch (error) {
                window.closeLoading();
                let errorMessage = error.response?.data?.message || error.message || 'Failed to process your request. Please try again later.';

                this.errorMessage = errorMessage;
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