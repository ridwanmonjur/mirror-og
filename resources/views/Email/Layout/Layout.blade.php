@php
    $primaryColor = '#43A4D7';
    $secondaryColor = '#8CCD39';
@endphp
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="x-apple-disable-message-reformatting">
    <style>
        html, body {
            font-size: 16px;
        }
        h1, h2, h3, h4, h5, h6, p, table, span, table {
            text-align: justify;
            font-size: 1rem;
        }
        a { 
            color: #43A4D7;
            text-decoration: none !important;
        }

        @media (max-width: 1000px) {
            html, body {
                font-size: 18px;
            }
        }
    </style>
   
    @stack('head')
</head>


<body style="margin:0;padding:0;">
    <table role="presentation"
        style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
        <tr>
            <td align="center" style="padding:0;">
                <table role="presentation"
                    style="width:min(90vw, 602px);border-collapse:collapse;border:0;border-spacing:0;text-align:left;">
                    <tr style="border-bottom: 0;">
                        <td align="start" style="padding: 20px 0 10px 0;">
                            <img src="{{$message->embed(public_path('assets/images/driftwood logo.png'))}}" alt="" width="250"
                                    style="height:auto;display:block;" />
                            {{-- <img src="{{ asset('assets/images/driftwood logo.png') }}" alt="" width="300"
                                style="height:auto;display:block;" /> --}}
                        </td>

                    </tr>
                    <tr >
                    {{-- <tr style="background-image: url({{ asset('assets/images/auth/email-bg.png') }});"> --}}
                        <td style="padding: 0px 0px 0 0px; width: 100%; color: black;">
                            <h1 style="vertical-align: bottom; margin-top: -10px; padding: 0px 0 0px 20px; z-index: 99;">@yield('title')
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 30px 42px 20px;">
                            <table role="presentation"
                                style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                @yield('content')
                            </table>
                        </td>
                    </tr>
                    @yield('footer')
                </table>
            </td>
    </table>
</body>

</html>
