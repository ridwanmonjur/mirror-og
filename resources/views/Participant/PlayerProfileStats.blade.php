<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
@php
    use Carbon\Carbon;
@endphp

<body>
    @include('CommonPartials.NavbarGoToSearchPage')

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
    @livewireScripts
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    <script src="{{ asset('/assets/js/tab/tab.js') }}"></script>
