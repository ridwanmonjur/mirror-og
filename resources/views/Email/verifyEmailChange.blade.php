@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Email Change' }}
@endsection

@push('head')
@endpush
@section('emailTitle')
    {{ 'Email Change' }}
@endsection
@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0px; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Hi,</p>
                        <p>You recently requested for changing your primary email. Click the button below to confirm
                            it.</p>
                        <p style="text-align: center;">
                            <a href="{{ route('user.verify.action', $token) }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white !important; background-color: {{$secondaryColor}}; text-decoration: none; border-radius: 5px;">
                                Change email
                            </a>
                        </p>
                        <p>If you did not request your primary email with us, please ignore this email or contact support if you have
                            questions.</p>
                        <p>Thanks,<br>Driftwood</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 10px 0; color: #999999;">
                        <p>If you're having trouble with the button above, copy and paste the URL below into your web
                            browser:</p>
                        <p><a href="{{ route('user.verify.action', $token) }}"
                                style="color: {{$secondaryColor}};">{{ route('user.verify.action', $token) }}</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
@section('footer')
    @include('Email.Layout.Footer')
@endsection