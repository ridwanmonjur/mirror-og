document.addEventListener("DOMContentLoaded", function() {
    const uploadButton = document.getElementById("upload-button");
    const imageUpload = document.getElementById("image-upload");
    const uploadedImage = document.getElementById("uploaded-image");

    uploadButton.addEventListener("click", function() {
        imageUpload.click();
    });

    imageUpload.addEventListener("change", function(e) {
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(readerEvent) {
                uploadedImage.style.backgroundImage = url("https://www.creativefabrica.com/wp-content/uploads/2022/07/10/tiger-logo-design-Graphics-33936667-1-580x387.jpg");
            };

            reader.readAsDataURL(file);
        }
    });
});

function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });

    // Show the selected tab content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }
}

// Show the default tab content (Overview) on page load
document.addEventListener("DOMContentLoaded", function() {
    showTab('Overview');
});

// Update the slideEvents function to toggle visibility of events dynamically
function slideEvents(direction) {
    const eventBoxes = document.querySelectorAll('.event-box');

    // Find the currently visible events
    const visibleEvents = Array.from(eventBoxes).filter(eventBox => eventBox.style.display !== 'none');

    // Hide all events
    eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

    let startIndex = 0;

    if (visibleEvents.length > 0) {
        // If there are visible events, calculate the starting index based on the direction
        startIndex = (Array.from(eventBoxes).indexOf(visibleEvents[0]) + direction + eventBoxes.length) % eventBoxes.length;
    }

    // Show at most 2 events based on the starting index
    for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
        const index = (startIndex + i + eventBoxes.length) % eventBoxes.length;
        eventBoxes[index].style.display = 'block';
    }
}

function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });

    // Show the selected tab content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = 'block';

        // Show the form if the "Active Rosters" tab is selected
        if (tabName === 'Active Rosters') {
            const activeRostersForm = document.getElementById('activeRostersForm');
            activeRostersForm.style.display = 'block';
        }
    }
}


// -----------------------------------------JS FOR CROWN 👑---------------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    // Get all username elements
    const usernames = document.querySelectorAll('.username');

    // Loop through each username element
    usernames.forEach(username => {
        // Add event listener for mouseenter event
        username.addEventListener('mouseenter', function() {
            // Show the crown emoji for the hovered username
            const crownEmoji = this.nextElementSibling;
            crownEmoji.style.display = 'inline-block';
        });

        // Add event listener for mouseleave event
        username.addEventListener('mouseleave', function() {
            // Hide the crown emoji when mouse leaves the username
            const crownEmoji = this.nextElementSibling;
            crownEmoji.style.display = 'none';
        });
    });

    // Get all crown emoji elements
    const crownEmojis = document.querySelectorAll('.crown-emoji');

    // Loop through each crown emoji element
    crownEmojis.forEach(crownEmoji => {
        // Add event listener for click event
        crownEmoji.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent the click event from bubbling up to the username element
            const userId = this.previousElementSibling.getAttribute('data-user-id');
            
            console.log('User ID:', userId);
            
        });
    });
});