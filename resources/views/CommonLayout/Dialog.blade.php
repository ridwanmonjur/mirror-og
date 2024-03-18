<script>
    const dialogOpen = (title, resultConfirmedCb, resultDeniedCb) => Swal.fire({
        title,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        cancelButtonColor: "#8CCD39",
        dangerButtonColor: "#DD6B55",
        confirmButtonColor: "#43A4D7",
        customClass: {
            actions: 'my-actions',
            cancelButton: 'order-1 right-gap',
            confirmButton: 'order-2',
            denyButton: 'order-3',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            resultConfirmedCb()
        } else if (result.isDenied) {
            resultDeniedCb()
        }
    })
</script>
