 <nav class="navbar">
     <div class="logo">
         <img width="160px" height="60px" src="{{ asset('/assets/images/logo-default.png') }}" alt="">
     </div>
     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
         class="feather feather-menu menu-toggle" onclick="toggleNavbar()">
         <line x1="3" y1="12" x2="21" y2="12"></line>
         <line x1="3" y1="6" x2="21" y2="6"></line>
         <line x1="3" y1="18" x2="21" y2="18"></line>
     </svg>
     <div class="search-bar d-none-at-mobile">
         <input type="text" name="search" id="search" placeholder="Search for events">
         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="feather feather-search">
             <circle cx="11" cy="11" r="8"></circle>
             <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
         </svg>
     </div>
     <div class="nav-buttons">
         <button class="oceans-gaming-default-button oceans-gaming-gray-button"> Where is moop? </button>
         <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
         <img width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
     </div>
 </nav>
 <nav class="mobile-navbar d-centered-at-mobile d-none">
     <div class="search-bar search-bar-mobile ">
         <input type="text" name="search" id="search" placeholder="Search for events">
         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="feather feather-search" style="left: 40px;">
             <circle cx="11" cy="11" r="8"></circle>
             <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
         </svg>
     </div>
     <div class="nav-buttons search-bar-mobile d-centered-at-mobile">
         <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
         <img width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
     </div>
 </nav>
