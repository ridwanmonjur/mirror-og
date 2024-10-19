const emailForm = document.getElementById('emailForm');

if (emailForm) {
    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('emailInput').value;
        
        console.log('Submitted email:', email);
        
        alert('Thank you for your interest! We\'ll be in touch soon.');
        
        document.getElementById('emailInput').value = '';
    });
}