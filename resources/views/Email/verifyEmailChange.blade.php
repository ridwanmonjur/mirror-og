@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Change your email address for Driftwood' }}
@endsection

@section('emailTitle')
    {{ 'Change your email address for Driftwood' }}
@endsection

@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0px; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Hi.</p>
                        <p>You recently requested to change the email address connected with your Driftwood account from
                            {{$user->email}} to {{$newEmail}}.</p>
                        <br>
                        <p>Click the button below to confirm this change.</p>
                        <p style="text-align: center;">
                            <a href="{{ route('user.changeEmail.action', ['token' => $token, 'newEmail' => $newEmail]) }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white !important; background-color: {{$secondaryColor}}; text-decoration: none; border-radius: 5px;">
                                Change email {{$user->email}}
                            </a>
                        </p>
                        <p>If you didn't perform this action, please ignore this email and reach out to our customer support at supportmain@driftwood.gg.</p>
                        <p>Sincerely,<br>The Driftwood Team</p>
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