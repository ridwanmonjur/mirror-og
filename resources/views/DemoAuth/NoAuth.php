@include('DemoAuth.Layout.HeadTag')
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Authenticated</h1>
                <p>You are authenticated.</p>
                <p><a href="{{ url('logout') }}">Logout</a></p>
            </div>
        </div>
    </div>
</body>s