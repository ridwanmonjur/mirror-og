const eventData = document.getElementById('eventData');

const goToManageScreen = () => {
    window.location.href = eventData.dataset.manageUrl;
}

const copyUtil = () => {
    navigator.clipboard.writeText(eventData.dataset.copyUrl).then(function() {
        Toast.fire({
            icon: 'success',
            text: 'Event url copied to clipboard',
        });
    });
};

let shareUrl = document.querySelectorAll('.js-shareUrl');
for (let i = 0; i < shareUrl.length; i++) {
    shareUrl[i].addEventListener('click', copyUtil, false);
}