@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')

@section('emailTitle')
    {{ 'Your event didn\'t get enough signups to start' }}
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
                        <p>Hi {{ $organizerName }},</p>
                        <p>It looks like your <strong>{{ $eventTier }}</strong>-tier event, <strong>{{ $eventName }}</strong>, didn't get enough signups. As a result, the event has automatically been cancelled and removed from the homepage.</p>
                        <p>But don't worry, here's a coupon for your next <strong>{{ $eventTier }}</strong>-tier event. This coupon has no expiry date, so you can use it whenever you want!</p>
                        <p>To use this coupon, just enter the code <strong><code style="background-color: #f5f5f5; padding: 2px 4px; border-radius: 3px; font-family: monospace;">{{ $couponCode }}</code></strong> when you're at the payment page.</p>
                        <p>If you need any support, ping us at supportmain@driftwood.gg and we'll come to your aid.</p>
                        <p>Thanks for using Driftwood, and good luck for your future events!</p>
                        
                        <p>From the Driftwood team</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
@endsection
@section('footer')
    @include('Email.Layout.Footer')
@endsection