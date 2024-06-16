<div class="dropdown" data-reference="parent" data-bs-auto-close="outside" data-bs-offset="-80,-80">
    <a href="#" role="button" class="btn dropdown-toggle" id="dropdownMenuGuest" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true">
        <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
        Sign In
    </a>
    <div class="dropdown-menu fs-7   py-0" style="position: absolute; left: -60px; width: 200px;" aria-labelledby="dropdownMenuGuest">
        <a class="dropdown-item py-3  ps-4 align-middle " href="{{ route('organizer.signin.view') }}">
           <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-lines-fill me-3" viewBox="0 0 16 16">
  <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
</svg>
            Organizer
        </a>
        <a class="dropdown-item py-3  ps-4 align-middle " href="{{ route('participant.signin.view') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                class="bi bi-people-fill me-3" viewBox="0 0 16 16">
                <path
                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
            </svg>
            Participant
        </a>
    </div>
</div>
