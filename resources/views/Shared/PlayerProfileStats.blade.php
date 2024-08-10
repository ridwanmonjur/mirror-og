<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
    <style> 
        main {
            margin: 0;
            color: #2E4B59;
            min-height: 100vh; 
        }

        main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("/assets/images/home.png");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center; 
            opacity: 0.8; 
            z-index: -1; 
            border-radius: 5px;
        }
   
    </style>

</head>
@php
    use Carbon\Carbon;
@endphp

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')

    <main class="ps-5">
        <br>
        <div class="ms-5">
            <u>
                <h3>
                    User profile stats
                </h3>
            </u>
        </div>
            @livewire('profile.friends-follow-display', ['userId' => $userId]) 
        </div>
        <br>
        
        <br>
    </main>
    @livewireScripts
    @stack('script')
        
    <script>

        function redirectToProfilePage(userId) {
            window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            document.getElementById(`${type}Btn`)?.click();
        });
    </script>
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
