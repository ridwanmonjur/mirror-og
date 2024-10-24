@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Confirm your Email for Driftwood\'s Closed Beta!' }}
@endsection

@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0px; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Hi,</p>
                        <p>Thank you for your interest in Driftwood's closed beta! Just confirm your email address and you're all good.
                        </p>
                        <p style="text-align: center;">
                            <a href="{{ route('interestedUser.verify.action', $token) }}"
                                style="display: inline-block; padding: 10px 20px; font-size: 18px; color: white !important; background-color: {{$primaryColor}}; text-decoration: none; border-radius: 5px;">
                                Confirm email address
                            </a>
                        </p>
                         
                        <p>Or you can use this link:</p>
                        <tr>
                            <td style="text-align: center; padding: 10px 0; color: {{$primaryColor}};">
                                <p>
                                    <a href="{{ route('interestedUser.verify.action', $token) }}"
                                            style="color: {{$primaryColor}};"
                                    >{{ route('interestedUser.verify.action', $token) }}
                                    </a>
                                </p>
                            </td>
                        </tr>
                        <p>Please note: Confirming your email does not automatically give you access to Driftwood's closed beta. Those invited will receive an additional email invitation.</p>

                        <p>If you need any support, ping us at <a style="color: {{$primaryColor}};">supportmain@driftwood.gg </a> and we'll come to your aid.</p>

                        <p>Sincerely,<br>The Driftwood Team</p>
                    </td>
                </tr>
               
            </table>
        </td>
    </tr>
@endsection
@section('footer')
@endsection