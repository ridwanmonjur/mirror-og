<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="sticky-footer">
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="container">
   <div class="container">
   		<div class="row">
   			<div class="col-lg-4"></div>
   			<div class="col-lg-5">
   				 <h1>Thank you for <br> Your Order!</h1>
       			 <p>A confirmation email was sent</p>
       
           <a href="{{ url('/') }}" class="button">Home Page</a>
   			</div>
   		</div>  
   </div>
    </main>

</body>

</html>
