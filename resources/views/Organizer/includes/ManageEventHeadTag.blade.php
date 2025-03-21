<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    @include('__CommonPartials.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/manageEvent.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js' ])    
    <link href="https://cdn.jsdelivr.net/npm/litepicker@2.0/dist/css/litepicker.min.css" rel="stylesheet">
</head>

