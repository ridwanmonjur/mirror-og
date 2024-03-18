<main class="main1">
    <div class="team-section">
        <div class="upload-container">
            <label for="image-upload" class="upload-label">
                <div class="circle-container">
                    <div id="uploaded-image" class="uploaded-image"
                        style="background-image: url({{ $selectTeam->teamBanner ? '/storage' . '/'. $selectTeam->teamBanner: '/assets/images/fnatic.jpg' }} );"
                    ></div>
                    <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                </div>
            </label>
            <input type="file" id="image-upload" accept="image/*" style="display: none;">
        </div>
        <div class="team-names">
            <div class="team-info">
                <h3 class="team-name" id="team-name">{{ $selectTeam->teamName }}</h3>
                <button class="gear-icon-btn me-2">
                    <a href="/participant/team/{{ $selectTeam->id }}/register">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                        </svg>
                    </a>
                </button>
                <button class="gear-icon-btn">
                    <a href="/participant/team/{{ $selectTeam->id }}/edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                    </a>
                </button>
            </div>

        </div>

        <p>{{ $selectTeam->teamDescription ? $selectTeam->teamDescription : 'Please add a description by editing your team...' }}</p>
    </div>
</main>

<script>
   const uploadButton = document.getElementById("upload-button");
        const imageUpload = document.getElementById("image-upload");
        const uploadedImage = document.getElementById("uploaded-image");

        uploadButton.addEventListener("click", function() {
            imageUpload.click();
        });

        imageUpload.addEventListener("change", async function(e) {
            const file = e.target.files[0];

            if (file) {
                const url = "{{ route('participant.banner.action', ['id' => $selectTeam->id] ) }}";
                const formData = new FormData();
                formData.append('file', file);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData,
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        uploadedImage.style.backgroundImage = `url(${data.data.fileName})`;
                    } else {
                        console.error('Error updating member status:', data.message);
                    }
                } catch (error) {
                    console.error('Error approving member:', error);
                }
            }
        });

</script>