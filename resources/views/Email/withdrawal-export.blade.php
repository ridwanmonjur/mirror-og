@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Your Withdrawal CSV Export is Ready' }}
@endsection

@push('head')
@endpush
@section('emailTitle')
    {{ 'Your Withdrawal CSV Export is Ready' }}
@endsection
@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Hi, {{$userName}}!</p>
                        <p>Your withdrawal CSV export has been generated and is ready for download.</p>
                        <br>
                        <p>Click the button below to securely download your export file.</p>
                        <p style="text-align: center;">
                            <a href="{{ $downloadLink }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white !important; background-color: {{$secondaryColor}}; text-decoration: none; border-radius: 5px;">
                                Download CSV Export
                            </a>
                        </p>
                        <p><strong>Important Security Notes:</strong></p>
                        <ul>
                            <li>This download link will expire in 24 hours</li>
                            <li>The file is password-protected for security</li>
                            <li>This link can only be used once</li>
                        </ul>
                        <p>If you didn't request this export, please ignore this email and contact our support team at supportmain@driftwood.gg.</p>
                        <p>Sincerely,<br>The OW Gaming Team</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 10px 0; color: #999999;">
                        <p>If you're having trouble with the button above, copy and paste the URL below into your web
                            browser:</p>
                        <p><a href="{{ $downloadLink }}"
                                style="color: {{$secondaryColor}};">{{ $downloadLink }}</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
@section('footer')
    @include('Email.Layout.Footer')
@endsection