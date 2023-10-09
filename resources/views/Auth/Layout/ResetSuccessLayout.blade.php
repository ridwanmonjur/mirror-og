@include('Auth.Layout.HeadTag')

@section('signUpbody')

<img src="{{ asset('/assets/images/auth/logo.png') }}">
<br>
<header><u>Success Notification</u></header>
<br><br> <br>

<p> Your password has been successfully reset. You can now login to your account.</p>

<br> <br><br>

@endsection

@include('Auth.Layout.SignUpBodyTag')
