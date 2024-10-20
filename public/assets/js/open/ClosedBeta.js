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
            window.toastSuccess(data.message);
        })
        .catch(error => {
            messageDiv.textContent = error.data.message || error.message;
            console.error('Error:', error);
        });
        
        document.getElementById('emailInput').value = '';
    });
}