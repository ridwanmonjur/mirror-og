{{-- Page Title Goes Here  --}}

@section('title') {{'Sign In'}} @endsection

{{-- extended the Sign page --}}

@extends('Auth.Layout.ParticipantSignInLayout')

@if(session('token'))
    <script>
        let token = "{{session('token')}}";
        console.log({{token}})
        localStorage.setItem('token', token)
    </script>
@endif
