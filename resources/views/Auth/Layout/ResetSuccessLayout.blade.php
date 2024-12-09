@include('Auth.Layout.HeadTag')

@section('signUpbody')

<img  class="mt-4 motion-logo mb-2" src="{{ asset('/assets/images/driftwood logo.png') }}">
<br>
<h5><u>Success Notification</u></h5>
<br><br> 

<p> Your password has been successfully reset. You can now login to your account.</p>

<br> <br><br>

@endsection

@include('Auth.Layout.SignUpBodyTag')
