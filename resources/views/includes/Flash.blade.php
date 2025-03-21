@if ($message = Session::get('success'))
<div>
    <strong style="color:#4bb543;">{{ $message }}</strong>
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
    <strong>

    <strong>
        Please check these fields: 
        @foreach($errors->all() as $error)
            {{ str_replace('The ', '', str_replace(' field is required.', '', $error)) }}{{ !$loop->last ? ($loop->index == count($errors->all()) - 2 ? ' and ' : ', ') : '.' }}
        @endforeach
    </strong>
</div>
@endif