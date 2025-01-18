<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Member Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>
@php
    $isRedirect = isset($redirect) && $redirect;
@endphp
<body
    @style(["min-height: 100vh;" => $isRedirect])
>
    @include('googletagmanager::body')

    <input type="hidden" id="publicParticipantViewUrl" value="{{ route('public.participant.view', ['id' => ':id']) }}">

    @include('__CommonPartials.NavbarGoToSearchPage')
    <main 
        class="main2"
    >
        <input type="hidden" id="participantMemberManageUrl" value="{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}">
        <input type="hidden" name="isRedirectInput" id="isRedirectInput" value="{{isset($redirect) && $redirect}}">
        <input type="hidden" id="participantMemberUpdateUrl" value="{{ route('participant.member.update', ['id' => ':id']) }}">
        <input type="hidden" id="participantMemberCaptainUrl" value="{{ route('participant.member.captain', ['id' => ':id', 'memberId' => ':memberId']) }}">
        <input type="hidden" id="participantMemberDeleteCaptainUrl" value="{{ route('participant.member.deleteCaptain', ['id' => ':id', 'memberId' => ':memberId']) }}">
        <input type="hidden" id="participantMemberDeleteInviteUrl" value="{{ route('participant.member.deleteInvite', ['id' => ':id']) }}">
        <input type="hidden" id="participantMemberInviteUrl" value="{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}">

        
        @include('Participant.__Partials.TeamHead') 
        <br>


        @include('Participant.__MemberManagementPartials.MemberManagement')
        @if ($isRedirect)
            <div class="d-flex box-width back-next mb-5">
                <button onclick="goBackScreens()" type="button"
                    class="btn border-dark rounded-pill py-2 px-4"> Back </button>
                <button onclick="goNextScreens()" type="button" 
                    class="btn btn-primary text-light rounded-pill py-2 px-4"
                    onclick=""> Next &gt; </button>
            </div>
        @else 
            <br><br><br><br><br><br>
        @endif
    </main>
    
    <script src="{{ asset('/assets/js/participant/MemberManagement.js') }}"></script>
</body>
