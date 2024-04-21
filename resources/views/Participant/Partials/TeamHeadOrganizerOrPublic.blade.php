<main class="main1">
    <div class="team-section">
        <div class="upload-container">
            <label for="image-upload" class="upload-label">
                <div class="circle-container">
                    <div id="uploaded-image" class="uploaded-image"
                        style="background-image: url({{ '/storage' . '/'. $selectTeam->teamBanner }} );"
                    ></div>
                </div>
            </label>
        </div>
        <div class="team-names">
            <div class="team-info">
                <h3 class="team-name" id="team-name">{{ $selectTeam->teamName }}</h3>
            </div>
        </div>
        <div>
            {{ $selectTeam->teamDescription ? $selectTeam->teamDescription : 'Please add a description by editing your team...' }}
        </div>
    </div>
</main>