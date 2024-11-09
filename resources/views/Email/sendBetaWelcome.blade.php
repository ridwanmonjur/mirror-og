@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#81bc1a';
@endphp
@extends('Email.Layout.Layout')
@section('title')
    {{ 'Welcome to Driftwood\'s Closed Beta!' }}
@endsection

@section('content')
    <tr>
        <td style="padding: 0; text-align: center;">
            <table width="600" border="0" cellspacing="0" cellpadding="0"
                style="background-color: white; margin: 0 auto; padding: 0px; border-radius: 10px;">
                <tr>
                    <td style="padding: 0 0px; text-align: left; color: #333333;">
                        <p>Ahoy from Driftwood! We're excited to invite you to Driftwood as a closed beta user!</p>
                        <p>Thank you for your interest in Driftwood's closed beta! Just confirm your email address and you're all good.
                        </p>
                        <p>
                            <span>You can sign into your account using the following temporary credentials:</span>
                            <br>
                            <span> Email address: <a style="color: black !important;">{{$email}} </a> </span> <br>
                            <span> Password: {{$password}} </span> <br>
                            <span> Username: {{$username}} </span> <br>
                        </p>
                        <p> 
                            <span> Sign into Driftwood here: </span>
                            <a href="{{ route('participant.signin.view') }}"
                                style="display: inline-block; padding: 10px; color: white !important;background-color: transparent; color: {{$primaryColor}} !important; text-decoration: none; border-radius: 5px;">
                                Sign in link
                            </a>
                        </p>
                        
                        <p>Note that the given username and password are only temporary. You can change them by signing into your account and clicking on your profile icon to go to your settings. From there, you can select a new username and password under "Account Details and Security".</p>

                        <p>We're still refining many of our features, so thank you for being patient. If you need any support, ping us at <a href="mailto:supportmain@driftwood.gg" style="color: {{$primaryColor}};">supportmain@driftwood.gg </a> and we'll come to your aid.</p>

                        <p>Sincerely,<br>Leigh<br>The Driftwood Team<br><a style="color: {{$primaryColor}};"> driftwood.gg </a></p>
                    </td>
                </tr>
               
            </table>
        </td>
    </tr>
@endsection
@section('footer')
@endsection