let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function cancelEvent(event) {
    let svgElement = event.target.closest('svg');
    if (!svgElement) return;
    let eventUrl = svgElement.dataset.url;

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#43A4D7",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancel Event",
        confirmButtonText: "Oops, no..."
    })
        .then((result) => {
            if (result.isDismissed) {
                fetch(eventUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Cancelled!',
                                text: 'Event has been cancelled.',
                                icon: 'success',
                                confirmButtonColor: "#43A4D7",
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to cancel the event.',
                                icon: 'error',
                                confirmButtonColor: "#43A4D7",
                            });
                        }
                    })
                    .catch((error) => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong!',
                            icon: 'error',
                            confirmButtonColor: "#43A4D7",
                        });
                    });
            }
        });
}