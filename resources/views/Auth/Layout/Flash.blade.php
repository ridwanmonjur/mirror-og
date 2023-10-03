@if ($message = Session::get('success'))
<div>
    <strong style="color:green;">{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('error'))
<div>
    <strong style="color:red;">{{ $message }}</strong>
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