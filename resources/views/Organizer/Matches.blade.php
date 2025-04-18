<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    @include('googletagmanager::head')
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Matches</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/viewEvent.css') }}">
    @vite([ 'resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/bracket.js'])
    @include('includes.HeadIcon')

</head>

@php
    $userId = isset($user) ? $user->id : null; 
@endphp

<body>
    @include('googletagmanager::body')
        @include('includes.__Navbar.NavbarGoToSearchPage')
 
    <main id="Bracket" @vue:mounted="init" v-scope="BracketData()" class="position-relative">
        <input type="hidden" id="eventId" value="{{$event->id}}">
        <input type="hidden" id="previousValues" value="{{json_encode($previousValues)}}">
        <input type="hidden" id="joinEventTeamId" value="{{$existingJoint?->team_id }}">
        <input type="hidden" id="userLevelEnums" value="{{json_encode($USER_ACCESS)}}">
        <input type="hidden" id="hidden_user_id" value="{{ $userId }}">
         <div >
            <u>
                <h3 class="ps-3 mt-4">
                    Manage your event bracket
                </h3>
            </u>
        </div> <br>
        <div class="px-4 py-4">
            <div  class="d-flex justify-content-center">
                <div style="width:min(600px, 80vw);" class="border bg-white d-inline-block border-primary shadow-xl px-5 mx-auto text-start  py-3">
                    <div class="d-flex justify-content-start align-items-center">
                        <img {!! trustedBladeHandleImageFailureBanner() !!}
                            src="{{ '/storage' . '/'.  $event->eventBanner }}"
                            class="object-fit-cover float-left border border-primary rounded-circle me-1" width="30" height="30"
                        >
                        <div>
                            <p class="py-0 my-0 ms-2 mb-2"> {{ $event->eventName }} </p>
                            <small class="py-0 my-0 ms-2">
                                Description: {{ $event->eventDescription }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex mt-3 mb-2 align-items-center justify-content-start">
                        <img {!! trustedBladeHandleImageFailureBanner() !!} 
                            src="{{ '/storage' . '/'. $event?->user?->userBanner }}" width="30"
                            height="30" class="me-1 border border-warning rounded-circle object-fit-cover "
                        >
                        <div class="ms-2">
                            <small class="d-block py-0 my-0">
                                {{ $event?->user?->name ?? 'Name Pending' }}
                            </small>
                                <small class="d-block py-0 my-0">
                                Joined: {{ $event?->user?->createdAtDiffForHumans() ?? 'Recently' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @include('includes.__ManageEvent.BracketUpdateList')
        </div>
        
        <script src="{{ asset('/assets/js/participant/ViewEvent.js') }}"></script>
        <script> 
            document.addEventListener("DOMContentLoaded", function() {
                window.showLoading();
            });
        </script>
    </main>
</body>
</html>
