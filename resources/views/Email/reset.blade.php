@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Reset Password Link' }}
@endsection

@push('head')
@endpush
@section('emailTitle')
    {{ 'Reset Password Link' }}
@endsection
@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Hi,</p>
                        <p>You recently requested to reset your password for your account. Click the button below to reset
                            it.</p>
                        <p style="text-align: center;">
                            <a href="{{ route('user.reset.view', $token) }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white !important; background-color: {{$secondaryColor}}; text-decoration: none; border-radius: 5px;">
                                Reset Password
                            </a>
                        </p>
                        <p>If you did not request a password reset, please ignore this email or contact support if you have
                            questions.</p>
                        <p>Thanks,<br>Driftwood</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 10px 0; color: #999999;">
                        <p>If you're having trouble with the button above, copy and paste the URL below into your web
                            browser:</p>
                        <p><a href="{{ route('user.reset.view', $token) }}"
                                style="color: {{$secondaryColor}};">{{ route('user.reset.view', $token) }}</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
