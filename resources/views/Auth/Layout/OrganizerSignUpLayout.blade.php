@include('Auth.Layout.HeadTag')
@section('signUpbody')

<a href="{{route('public.landing.view')}}">
    <img class=" mb-1 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
</a>

<h5 class=" mb-0">Create an <span class="text-primary">organizer account</span></h5>

<form 
    autocomplete="off" 
    readonly 
    novalidate
    name="organizer-signup-form" 
    id="organizer-signup-form" 
    method="post" 
    action="{{route('organizer.signup.action')}}"
>
    @csrf
    <div class="flash-message">
        @include('includes.Flash')
    </div>
    
    @include('Auth.Layout.__Signup')
    
    <input type="submit" class="mt-2" value="Register">

    <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#" role="button">Terms of Use</a>. Read our <a href="#" role="button">Privacy Policy</a>.</div>

    <div class="section-bottoms">
        <p class="py-0 my-0">Already have an account? <a href="{{ route('organizer.signin.view') }}">Sign in</a></p>
    </div>
    <div>
         <a  href="{{ route('participant.signup.view') }}" class="btn my-2 px-5 btn-secondary rounded-pill text-white btn-sm">Switch to participant
    </a>
    </div>

</form>

@endsection

@include('Auth.Layout.SignUpBodyTag')


