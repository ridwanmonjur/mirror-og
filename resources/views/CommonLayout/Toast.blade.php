<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-right',
        width: 'fit-content',
        padding: '0.7rem',
        showConfirmButton: false,
        timer: 6000,
        timerProgressBar: true
    })

    function toastError(message, error = null) {
        console.error(error)
        Toast.fire({
            icon: 'error',
            text: message
        });
    }

    function toastWarningAboutRole(button, message) {
        toastError(message);
        button.style.cursor = 'not-allowed';
    }
</script>
