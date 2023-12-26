@if ($message = Session::get('success'))
<div>
    <strong style="color:#8CCD39;">{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('error'))
<div>
    <strong style="color:#EF4444;">{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('warning'))
<div>
	<strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('info'))
<div>
	<strong>{{ $message }}</strong>
</div>
@endif


@if ($errors->any())
<div>
    <strong>Please check the form below for errors </strong>
</div>
@endif