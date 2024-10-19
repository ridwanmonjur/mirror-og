@php
    $perpage = 4;
    $notificationList = $user->notifications()?->cursorPaginate($perpage);
    $nextCursor = $notificationList?->nextCursor();
    $countUnread = $user->unreadNotifications->count();
@endphp

<div class="ms-2 dropdown" data-reference="parent"  data-bs-offset="-80,-80">
    <a href="#" role="button" class="btn position-relative" id="dropdownMenuLinkNotification" data-bs-auto-close="outside" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true">
        <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-bell.png') }}" alt="">
        @if ($countUnread!=0)
            <span style="top: -20px;" id="countUnread" data-notification-count="{{$countUnread}}" class="badge text-light bg-primary">{{$countUnread}}</span>
        @endif
    </a>

    @if (isset($notificationList[0]))
        <div class="dropdown-menu border rounded  fs-7 py-0" data-bs-auto-close="outside" style="position: absolute; left: -300px; width: 400px; max-height: 60vh; overflow-y: scroll;" aria-labelledby="dropdownMenuLinkNotification">
            <div class="position-relative">
                <div class="pt-2 pb-1 d-flex justify-content-between">
                    <a href="" class="btn btn-link"> </a>
                    <a role="button" onclick="setAllNotificationsRead(event);" class="btn btn-link">
                        <u>
                            {{-- All checks --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-all" viewBox="0 0 16 16">
                            <path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486z"/>
                            </svg>
                            <span> Mark all as read </span>
                        </u>
                    </a>
                </div> 
                @php
                    
                @endphp
                <div class="notifications-list-container ">
                    @include('__CommonPartials.__Navbar.Notifications')
                </div>
                @if ($nextCursor)
                    <div class="d-flex pagination justify-content-center mx-auto">
                        <button class="btn btn-link text-light btn-sm" id="load-more" 
                            data-url="{{ route('user.notifications.more', ['id' => $user->id]) }}" 
                            data-cursor="{{ $nextCursor ? $nextCursor->encode() : '' }}"
                        > <u>Load More </u></button>
                    </div>
                    <br>
                @else
                    <br>
                @endif
            </div> 
        </div>
    @else
        <div class="dropdown-menu  border rounded  text-center py-2" style="position: absolute; left: -300px; width: 300px;" data-bs-auto-close="outside" aria-labelledby="dropdownMenuLinkNotification">
            <p class="pt-3 align-middle me-4" style="font-weight: 400;" >
            You have no notifications!
            </p>
        </div>
    @endif
</div>

<div class="dropdown" data-reference="parent" data-bs-auto-close="outside" data-bs-offset="-80,-80">
    <a href="#" role="button" class="btn" id="dropdownMenuLinkSignedIn" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true">
        <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
    </a>
    <div class="dropdown-menu border rounded  fs-7   py-0" style="position: absolute; left: -250px; width: 280px;"
        aria-labelledby="dropdownMenuLinkSignedIn">
        <div class="border-dark border-2 border-bottom text-center px-2">
            <a class="py-0" href="{{ route( strtolower($user->role) .'.profile.view') }}">
                <div class="py-3 d-flex justify-content-start">
                    @if($user->userBanner)
                        <img
                            class="object-fit-cover rounded-circle me-2 border border-primary" 
                            src="{{'/storage' . '/' . $user->userBanner}}" width="45" height="45">
                    @else 
                        <span style="display: inline-block; height: 45px; min-width: 45px; max-width: 45px;"
                            class="bg-dark d-flex justify-content-center align-items-center text-light rounded-circle">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    @endif
                        <span style="text-overflow: ellipsis; overflow: hidden;" 
                            class="text-start  align ms-2">
                            <small> {{ $user->name }}</small> <br>
                            <small> {{ $user->email }}</small>
                            {{-- <small> N__Edit put the profile link </small> --}}
                        </span>
                </div>
            </a>
        </div>
        @if ($user->role == 'ORGANIZER' || $user->role == 'ADMIN')
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{ route('organizer.profile.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-circle me-3" viewBox="0 0 16 16">
  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
</svg>
                Profile
            </a>
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{ route('event.create') }}">
               <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                    class="bi bi-person-fill-gear me-3" viewBox="0 0 16 16">
                    <path
                        d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0" />
                </svg>Create
            </a>
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{ route('event.index') }}">
               <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-kanban me-3" viewBox="0 0 16 16">
  <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
  <path d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1z"/>
</svg>
                Manage
            </a>
        @endif
        @if ($user->role == 'PARTICIPANT' || $user->role == 'ADMIN')
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{ route('participant.profile.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-circle me-3" viewBox="0 0 16 16">
  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
</svg>
                Profile 
            </a>
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{  url('/participant/team/create/') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-fill-add me-3" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
</svg>
                Create a Team
            </a>
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{  url('/participant/team/list/') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-plus me-3" viewBox="0 0 16 16">
  <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
  <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
</svg>
                Team List
            </a>
            <a class="dropdown-item py-3  ps-4 align-middle " href="{{ url('/participant/request/') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-file-plus-fill me-3" viewBox="0 0 16 16">
  <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M8.5 6v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 1 0"/>
</svg>
                Requests
            </a>
        @endif
        <a class="dropdown-item py-3  ps-4 align-middle pb-3" href="{{ route('logout.action') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-box-arrow-right me-3" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
</svg>
            Logout
        </a>
    </div>
</div>
