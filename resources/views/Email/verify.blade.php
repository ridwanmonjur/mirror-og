@include('Auth.Layout.HeadTag')
<body>
<div class="wrapper">
<h1>Email Verification Mail</h1>
  
Please verify your email with below link: 
<a href="{{ route('user.verify.action', $token) }}">Verify Email</a>
</div> 
</body>