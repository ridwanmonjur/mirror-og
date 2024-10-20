const emailForm = document.getElementById('emailForm');

if (emailForm) {
    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const emailInput = document.getElementById('emailInput').value;
        const submitButton = document.getElementById('submitButton');
        const url = submitButton.getAttribute('data-url');

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': emailForm.querySelector('input[name="_token"]').getAttribute('content'),
            },
            body: JSON.stringify({ email: emailInput})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Success...",
                    confirmButtonColor: "#43A4D7",
                    text: data.message,
                    timer: 6000
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error...",
                    confirmButtonColor: "#43A4D7",
                    text: data.message,
                    timer: 6000
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error...",
                confirmButtonColor: "#43A4D7",
                text: error.message,
                timer: 6000
              });
            console.error('Error:', error);
        });
        
        document.getElementById('emailInput').value = '';
    });
}