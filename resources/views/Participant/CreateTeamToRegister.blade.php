<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <div class="text-center" id="step-0">
            <div class="">
                <div class="time-line-box mx-auto" id="timeline-box">
                    <div class="swiper-container ps-5 text-center">
                        <div class="swiper-wrapper ps-5">
                            <div class="swiper-slide swiper-slide__left" id="timeline-1">
                                <div class="timestamp" onclick="window.toastError('Current tab selected!');"><span
                                        class="cat text-primary">Select Team</span></div>
                                <div class="status__left" onclick="window.toastError('Current tab selected!');">
                                    <span><small class="bg-primary"></small></span></div>
                            </div>
                            <div class="swiper-slide" id="timeline-2">
                                <div class="timestamp" onclick="window.toastError('Please select a team first!');">
                                <span>Manage Members</span></div>
                                <div class="status" onclick="window.toastError('Please select a team first!');">
                                    <span><small></small></span></div>
                            </div>
                            <div class="swiper-slide" id="timeline-launch">
                                <div class="timestamp" onclick="window.toastError('Please select a team first!');"><span
                                        class="date">Manage Roster</span></div>
                                <div class="status" onclick="window.toastError('Please select a team first!');">
                                    <span><small></small></span></div>
                            </div>
                            <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                                <div class="timestamp"
                                    onclick="window.toastError('Please select a team first!');">
                                    <span>Manage Registration</span></div>
                                <div class="status__right"
                                    onclick="window.toastError('Please select a team first!');">
                                    <span><small></small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="breadcrumb-top">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-primary" onclick="window.toastError('Current tab selected!');">Select Team</a></li>
                            <li class="breadcrumb-item"><a onclick="window.toastError('Please select a team first!');">Manage Members</a></li>
                            <li class="breadcrumb-item"><a onclick="window.toastError('Please select a team first!');">Manage Roster</a></li>
                            <li class="breadcrumb-item"><a onclick="window.toastError('Please select a team first!');">Manage Registration</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="text-center" id="step-0">
                    <div class="">
                        <u>
                            <h5>Create & Register Your Team</h5>
                        </u>
                        <br>
                        <p class="create-online-esports">
                            What will your team be called?
                        </p>
                        <br>
                        
                        <form id="formSubmit"
                            action="{{ route('participant.createTeamToJoinEvent.action', ['id' => $id]) }}"
                            method="POST">

                            @csrf
                            @if ($errors->any())
                                <div class="text-red">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('errorMessage'))
                                <div class="text-red">
                                    {{ session()->get('errorMessage') }}
                                </div>
                            @endif

                             <div class="d-flex flex-column align-items-center justify-content-center">
                                <input type="text" value="" name="teamName" id="teamName"
                                    placeholder="Team Name" onclick="clearPlaceholder(this)"
                                    onblur="restorePlaceholder(this)">
                                <input type="text" style="height: 100px;" value="" name="teamDescription"
                                    id="teamDescription" placeholder="Write your team description..."
                                    onclick="clearPlaceholder(this)" onblur="restorePlaceholder(this)">
                                <br> <br>
                                <input type="submit" onclick="" value="Create & Regjster">

                            </div>
                        </form>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
        <div class="d-flex box-width back-next">
            <button onclick="goToCancelButton()" type="button"
                class="btn border-dark rounded-pill py-2 px-4"> Back </button>
            <button form="formSubmit" type="submit" 
                class="btn btn-primary text-light rounded-pill py-2 px-4"
                onclick=""> Next > </button>
        </div>
    </main>
    <script>
        function goToCancelButton() {
            let url = "{{ route('participant.event.view', $id) }}";
            window.location.href = url;
        }
    </script>
    

</body>

</html>
