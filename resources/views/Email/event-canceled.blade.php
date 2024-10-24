@php
    $primaryColor = '#43A4D7';
    $secondaryColor = 'red';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Team Canceling Event' }}
@endsection

@push('head')
@endpush
@section('emailTitle')
    {{ 'Team Canceling Event' }}
@endsection
@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Hello and greetings.</p>
                        <p style="display: flex; justify-content: start; align-items: center;">
                            <a href="/view/team/{{$team['id']}}" style="color: white !important;">
                                <img src="{{$message->embed($bannerPath)}}"
                                    width="45" height="45"
                                    style="object-fit:cover; border-radius: 50%; margin-right: 10px;"
                                    alt="Team banner">
                            </a>
                            <span>{!! $text !!}</span>
                        </p> 
                        <p style="text-align: center;">
                            <a href="{{ $actionUrl }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white; background-color: {{$secondaryColor}}; text-decoration: none; border-radius: 5px;">
                                {{$actionName}}
                            </a>
                        </p>
                        <p>If you did not do this action, please ignore this email or contact support if you have
                            questions.</p>
                        <p>Thanks,<br>Driftwood</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 10px 0; color: #999999;">
                        <p>If you're having trouble with the button above, copy and paste the URL below into your web
                            browser: <a href="{{ $actionUrl }}"
                            >{{ $actionUrl }}</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
@section('footer')
    @include('Email.Layout.Footer')
@endsection
