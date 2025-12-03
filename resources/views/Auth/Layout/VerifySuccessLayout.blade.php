@include('Auth.Layout.HeadTag')

@section('signUpbody')
<a href="{{route('public.landing.view')}}">
    <img class="mt-4  mb-2 motion-logo" src="{{ asset('/assets/images/DW_LOGO.png') }}">
</a>
<br>
<h5><u>Success Notification</u></h5>
<br><br> 

<p> Your account has been successfully verified. You can now login to your account.</p>

<br> <br><br>

@endsection

@include('Auth.Layout.SignUpBodyTag')
