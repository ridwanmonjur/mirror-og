@include('Auth.Layout.HeadTag')


@section('signUpbody')
    
    <a href="{{route('public.landing.view')}}">
        <img class=" mb-1 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
    </a>
    
    <h5 class="px-2">Create a <span class="text-primary">participant account</span></h5>
    
    <form novalidate autocomplete="off" readonly name="signup-form" id="signup-form" method="post"
        action="{{ route('participant.signup.action') }}"
    >
        @csrf
        <div class="flash-message">
            @include('includes.Flash')
        </div>
        
        @include('Auth.Layout.__Signup')
        
        <input type="submit" class="mt-2" value="Register">

        <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#" role="button">Terms of Use</a>. Read our <a
                href="#" role="button">Privacy Policy</a>.</div>

        <div class="section-bottoms">
            <p class="my-0 py-0">Already have an account? <a href="{{ route('participant.signin.view') }}">Sign in</a></p>
        </div>
        <div>
            <a  href="{{ route('organizer.signup.view') }}">
                <button type="button" class="btn my-2 px-5 btn-secondary rounded-pill text-white btn-sm">Switch to organizer</button>
            </a>
        </div>

    </form>
@endsection

@include('Auth.Layout.SignUpBodyTag')
