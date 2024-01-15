<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Management</title>
    <!-- Existing CSS links -->
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="... (the integrity hash) ..." crossorigin="anonymous">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')

    
    <main>

        <div class="team-section">
            <div class="upload-container">
                <label for="image-upload" class="upload-label">
                    <div class="circle-container">
                        <div id="uploaded-image" class="uploaded-image"></div>
                        <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                    </div>
                </label>
                <input type="file" id="image-upload" accept="image/*" style="display: none;">
            </div>
            @foreach ($teamManage as $manage)
            <div class="team-names">
                <div class="team-info">
                    <h3 class="team-name" id="team-name">{{ $manage->teamName }}</h3>
                    <button class="gear-icon-btn">
                        <a href="/participant/registration-manage/{{ $manage['id'] }}">
                          <i class="fas fa-cog"></i>
                        </a>
                      </button>
                </div>
               
            </div>
            
            <p>We are an awesome team with awesome members! Come be awesome together! Play some games and win some prizes GGEZ!</p>
            @endforeach
        </div>

        <div class="tab-content" id="Overview">
            <div><b>Outstanding Registration</b></div>
            <br> <br> <br>
            
           
          
                          
                           

    </main>
    <script>
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
    </script>
</body>