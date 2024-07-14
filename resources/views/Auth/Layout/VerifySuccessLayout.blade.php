@include('Auth.Layout.HeadTag')

@section('signUpbody')

<img src="{{ asset('/assets/images/auth/logo.png') }}">
<br>
<h5><u>Success Notification</u></h5>
<br><br> 

<p> Your account has been successfully verified. You can now login to your account.</p>

<br> <br><br>

@endsection

@include('Auth.Layout.SignUpBodyTag')
