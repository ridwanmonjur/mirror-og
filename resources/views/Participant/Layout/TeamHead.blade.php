<main class="main1">
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
        <div class="team-names">
            <div class="team-info">
                <h3 class="team-name" id="team-name">{{ $selectTeam->teamName }}</h3>
                <button class="gear-icon-btn">
                    <a href="/participant/team/{{ $selectTeam->id }}/register">
                        <i class="fas fa-cog"></i>
                    </a>
                </button>
            </div>

        </div>

        <p>We are an awesome team with awesome members! Come be awesome together! Play some games and win some
            prizes GGEZ!</p>
    </div>
</main>
