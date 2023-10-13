@include('Email.Layout.HeadTag')
@section('title') {{'Reset Password Link'}} @endsection

<body>
<div class="wrapper">
    <h1>Password Reset Mail</h1> 
    <span> 
        Please reset your password with below link: 
        <a href="{{ route('user.reset.view',$token) }}">Reset Password</a> 
    </span>
</div> 
</body>