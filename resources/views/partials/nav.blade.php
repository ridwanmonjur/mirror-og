<link rel="stylesheet" href="{{ asset('css/algolia.css') }}">

<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <a class="navbar-brand" href="/">ACTIVE SHOP</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="/">Shop <span class="sr-only">(current)</span></a>
      </li>

          {{-- Main Nav code is here --}}
      

          @if (! (request()->is('checkout') || request()->is('guestCheckout')))
          @include('partials.menus.main-right')
          @endif

          {{-- Main Nav code end --}}
     

</ul>
  </div>
</nav>
