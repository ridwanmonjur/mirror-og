@include('Auth.Layout.HeadTag')

@section('signUpbody')
<a href="{{route('public.landing.view')}}">
    <img  class="my-0 motion-logo mb-2" src="{{ asset('/assets/images/dw_logo.webp') }}">
</a>
<br>
<h5><u>Success Notification</u></h5>
<br><br> 

<p> Your password has been successfully reset. You can now login to your account.</p>

<br> <br><br>

@endsection

@include('Auth.Layout.SignUpBodyTag')
