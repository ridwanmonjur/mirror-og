@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Event Has Ended' }}
@endsection

@push('head')
@endpush
@section('emailTitle')
    {{ 'Event Has Ended' }}
@endsection
@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Event has finished.</p>
                        <p style="display: flex; justify-content: start; align-items: center;">
                            <span>{!! $text !!}</span>
                        </p> 
                        <p style="text-align: center;">
                            <a href="{{ $actionUrl }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white; background-color: {{$secondaryColor}}; text-decoration: none; border-radius: 5px;">
                                {{$actionName}}
                            </a>
                        </p>
                        <p>If you're having trouble with the button above, copy and paste the URL below into your web
                            questions.</p>
                        <p>Thanks,<br>Driftwood</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 10px 0; color: #999999;">
                        <p>If you're having trouble with the button above, please check you have signed in first. Next, copy and paste the URL below into your web
                            browser: <a href="{{ $actionUrl }}"
                                style="color: {{$secondaryColor}};">{{ $actionUrl }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
@section('footer')
    @include('Email.Layout.Footer')
@endsection