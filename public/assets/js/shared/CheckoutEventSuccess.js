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

const checkbox = document.getElementById('notifyCheckbox');
    
checkbox?.addEventListener('change', async function(e) {
    e.preventDefault();
    
    const newState = this.checked;
    const action = newState ? 'enable' : 'disable';
    
    const result = await Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} notifications?`,
        text: `Are you sure you want to ${action} notifications for new players?`,
        icon: 'question',
        confirmButtonColor: '#43a4d7',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    });
    
    if (result.isConfirmed) {
        try {
            let response = await fetch(`/api/organizer/event/${eventData.dataset.id}/notifications`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    notify: newState
                })
            });

            response = await response.json();
            
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: `Notifications ${action}d successfully`,
                    icon: 'success',
                    timer: 1500,
                    confirmButtonColor: '#43a4d7',
                });

                return;
            }
            
            Swal.fire({
                title: 'Failed!',
                text: 'Failed to update notification settings',
                icon: 'error',
                timer: 1500,
                confirmButtonColor: '#43a4d7',
            });        

            this.checked = newState;
           
        } catch (error) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update notification settings',
                icon: 'error',
                confirmButtonColor: '#43a4d7',
            });

            this.checked = newState;
        }
    } else {
        this.checked = newState;
    }
});