@php
    $teamMember = App\Models\TeamMember::where('team_id', $selectTeam->id)
        ->where('user_id', $user->id)->get();
    if (isset($teamMember[0])) {
        $status = $teamMember[0]->status;
        if ($status == 'rejected' && $teamMember->rejector == 'invitee') {
            $status = 'rejected_me';
        }
    } else {
        $status = null;
    }

    $statusMessage = [
        'accepted' => "You're a member of this team.",
        "invited" => "You've been invited to this team.",
        "rejected_me" => "You've declined to join this team.",
        "rejected" => "You've been rejected to this team.",
        "pending" => "You've requested to join this team."
    ];
@endphp
<main class="main1">
    <div class="team-section">
        <div class="upload-container">
            <label for="image-upload" class="upload-label">
                <div class="circle-container">
                    <div id="uploaded-image" class="uploaded-image"
                        style="background-image: url({{ $selectTeam->teamBanner }} );"
                    ></div>
                    @if ($selectTeam->creator_id == $user->id)
                        <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                    @endif
                </div>
            </label>
            @if ($selectTeam->creator_id == $user->id)
                <input type="file" id="image-upload" accept="image/*" style="display: none;">
            @endif
        </div>
        <div class="team-names">
            <div class="team-info">
                <h3 class="team-name" id="team-name">{{ $selectTeam->teamName }}</h3>
                @if ($user->role == "PARTICIPANT")
                <div class="dropdown">
                    <button class="gear-icon-btn me-2 position-relative" style="top: 10px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                            <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu" style="font-size: 14px;" aria-labelledby="dropdownMenuButton">
                        <div>
                            @if (is_null($status))
                                <form class="d-inline ms-2 w-100 h-100" method="POST" action="{{route('participant.member.pending', ['id' => $selectTeam->id]) }}">
                                    @csrf()
                                    <button style="font-size: 14px;" class="px-2 py-2 w-100 h-100 gear-icon-btn" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                                        <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
                                        </svg>
                                        <span class="ms-2"> Join </span>
                                    </button>
                                </form>
                            @else 
                                <button class="px-2 w-100 h-100 gear-icon-btn-2 fw-bolder pt-1 pb-2 border-1 border-dark border-bottom" type="button">
                                    <small style="font-size: 14px;" class="ms-2">  
                                        @if ($selectTeam->creator_id == $user->id)
                                            You've created this team
                                        @else
                                            {{$statusMessage[$status]}}
                                        @endif
                                    </small>
                                </button>
                            @endif
                        </div>
                        <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/manage">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                            <span class="ms-2"> Team Profile </span>
                        </a>
                        @if ($status == "accepted")
                            <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/register">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16">
                                <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z"/>
                                </svg>
                                <span class="ms-2"> Register Team </span>
                            </a>
                        @endif
                        @if ($selectTeam->creator_id == $user->id)
                            <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                                <span class="ms-2"> Edit Team </span>
                            </a>
                            <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/manage/member">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                                </svg>
                                <span class="ms-2"> Manage Members </span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div>
            {{ $selectTeam->teamDescription ? $selectTeam->teamDescription : 'Please add a description by editing your team...' }}
            
        </div>
        <div class="mx-auto text-center mt-1">
            @if (session('successJoin'))
                <span class="text-success">
                    {{ session('successJoin') }}
                </span> <br>
            @elseif (session('errorJoin'))
                <span class="text-danger">
                    {{ session('errorJoin') }}
                </span> <br>
            @endif
        </div>
    </div>
</main>

@if ($selectTeam->creator_id == $user->id)
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
@endif