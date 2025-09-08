@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')

@section('emailTitle')
    {{ 'Coupon for your failed event' }}
@endsection

@section('title')
@endsection

@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Dear {{ $organizerName }},</p>
                        <p>Your event, <strong>{{ $eventName }}</strong> has failed due to inadequate attendance: <strong>{{ $actualAttendance }}</strong> out of <strong>{{ $expectedAttendance }}</strong> actual teams have registered.</p>
                        <p> The platform has given you a new coupon worth <strong>RM {{ $tierPrize }}</strong> for you to re-use and re-launch another <b>{{$eventTier}}</b> event.
                        <p><strong>Your coupon code:</strong> <code style="background-color: #f5f5f5; padding: 2px 4px; border-radius: 3px; font-family: monospace;">{{ $couponCode }}</code></p>
                        <p>All the best wishes for your next event and thank you for organizing events with us!</p>
                        
                       
                        
                        
                        <p>Sincerely,<br>The Driftwood Team</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
@endsection
@section('footer')
    @include('Email.Layout.Footer')
@endsection