// Change event card colors based on event type
const events = document.querySelectorAll('.event, .event_1, .event_2');
events.forEach(event => {
    const status = event.querySelector('.event_status');
    if (status) {
        const statusText = status.textContent.trim();
        if (statusText === 'ENDED') {
            event.style.borderColor = 'rgb(82, 159, 23)';
        } else if (statusText === 'ONGOING') {
            event.style.borderColor = 'rgb(90, 150, 230)';
        } else if (statusText === 'UPCOMING') {
            event.style.borderColor = 'rgb(16, 22, 68)';
        }
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Animate the featured events on scroll
const animateEvents = () => {
    const windowHeight = window.innerHeight;
    events.forEach(event => {
        const eventTop = event.getBoundingClientRect().top;
        if (eventTop < windowHeight * 0.8) {
            event.classList.add('animate');
        } else {
            event.classList.remove('animate');
        }
    });
};


document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const navItems = document.querySelector('.nav__items');

    if (hamburgerMenu && navItems) {
        hamburgerMenu.addEventListener('click', function() {
            navItems.classList.toggle('active');
        });
    }
});


window.addEventListener('scroll', animateEvents);
window.addEventListener('resize', animateEvents);