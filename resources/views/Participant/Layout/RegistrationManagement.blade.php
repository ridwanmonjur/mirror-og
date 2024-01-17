<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Management</title>
    <!-- Existing CSS links -->
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/registrationManagement.css') }}">
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
            <div class="first">
                <div id="activeRostersForm" style="display: center; text-align: center;">
                    
                    <div class="event">
                        <div style="text-align: left; height: 200px; position: relative;">
                            <div class="top-middle-box" style="display: flex; align-items: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40" height="40" fill="white" style="margin-right: 10px;">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-12h2v6h-2zm1 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2- .9 2-2 2z"/>
                                </svg>
                                <div>
                                    <div>29 Dec 2023</div>
                                    <a href="" style="color: white; text-decoration: underline;">See Bracket</a>
                                </div>
                            </div>
                            <br><br>
                            @foreach($joinEvents as $joinEvent)
                            @foreach($eventsByTeam as $teamId => $users)
                            <div class="player-info">
                                @foreach($users as $user)
                                <div class="player-image" style="background-image: url('/assets/images/dota.png')"></div>
                                <span>{{ $user['user']->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                        @endforeach
                        <div class="frame1">
                            <div class="container">
                                <div class="left-col">
                                    <p><img src="css/images/dota.png" class="logo2">
                                        <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota Challenge League Season 1</p>
                                    </p>
                                </div>
                                <div class="right-col">
                                    <p><img src="css/images/dota.png" class="logo2">
                                        <p style="font-size: 12px; text-align: left;">Media Prima</p>
                                        <br>
                                        <p style="font-size: 12px; text-align: left;">1K Followers</p>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="second">
                <div class="center">
                    <div class="flex-wrapper">
                        <div class="single-chart">
                            <svg viewBox="0 0 36 36" class="circular-chart orange">
                            <path class="circle-bg"
                              d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"
                            />
                            <path class="circle"
                              stroke-dasharray="50, 100"
                              d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"
                            />
                            <text x="18" y="20.35" class="percentage">50%</text>
                          </svg>
                        </div>
                    </div>
                    <p>Total Entry Fee: RM 200</p>
                    <small>Paid: <a href="" style="color: green;">RM 100</a></small>&nbsp;&nbsp;&nbsp;<small>Pending: <a href="" style="color: red;">RM 100</a></small> <br>
                    <input type="submit" onclick="" value="Contribute"><br> 
                    <button onclick="" class="oceans-gaming-default-button oceans-gaming-transparent-button"> Confirm Registration </button>
                </div>
            </div>
        </div>

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