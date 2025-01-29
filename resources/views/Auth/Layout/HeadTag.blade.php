<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/authLogin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    @include('__CommonPartials.HeadIcon')
</head>


