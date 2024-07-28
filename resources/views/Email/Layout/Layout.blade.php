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
    <link rel="stylesheet" href="./default.css">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" /> -->
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <!--[if mso]>
    <noscript>
        <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        table,
        td,
        div,
        h1,
        p {
            font-family: Arial, sans-serif;
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
                    style="width:min(90vw, 602px);border-collapse:collapse;border:1px solid {{$primaryColor}};border-spacing:0;text-align:left;">
                    <tr style="border-bottom: 0px solid {{$primaryColor}}; ">
                        <td align="start" style="padding: 0 0 10px 0;">
                            <img src="{{$message->embed(public_path('assets/images/logo-default.png'))}}" alt="" width="300"
                                    style="height:auto;display:block;" />
                            {{-- <img src="{{ asset('assets/images/logo-default.png') }}" alt="" width="300"
                                style="height:auto;display:block;" /> --}}
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <img src="{{$message->embed(public_path('assets/images/auth/email-bg.png'))}}" alt="" width="100%" height="100px"
                                    style="display:block;" >
                            {{-- <img src="{{ asset('assets/images/auth/email-bg.png') }}" alt="" width="100%" height="100px"
                                style="display:block;" > --}}
                        </td>
                    </tr>
                    <tr >
                    {{-- <tr style="background-image: url({{ asset('assets/images/auth/email-bg.png') }});"> --}}
                        <td style="padding: 0px 0px 0 0px; width: 100%; color: black;">
                            <h1 style="vertical-align: bottom; margin-top: -10px; padding: 0px 0 0px 40px; z-index: 99;">@yield('title')
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
                    <tr>
                        <td style="padding:30px;background:{{$primaryColor}};">
                            <table role="presentation"
                                style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
                                <tr>
                                    <td style="padding:0;width:50%;" align="left">
                                        <p
                                            style="margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;">
                                            &reg; Kuala Lumpur, Malaysia 2024<br /><a href="http://www.example.com"
                                                style="color:#ffffff;text-decoration:underline;">Unsubscribe</a>
                                        </p>
                                    </td>
                                    <td style="padding:0;width:50%;" align="right">
                                        <table role="presentation"
                                            style="border-collapse:collapse;border:0;border-spacing:0;">
                                            <tr>
                                                <td style="padding:0 0 0 10px;width:38px;">
                                                    <a href="https://twitter.com/oceansgamingmy">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-twitter" viewBox="0 0 16 16">
                                                            <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/>
                                                        </svg>
                                                    <a>
                                                </td>
                                                <td style="padding:0 0 0 10px;width:38px;">
                                                    <a href="https://www.facebook.com/oceansgamingmy/">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-facebook" viewBox="0 0 16 16">
                                                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                                                        </svg>
                                                    <a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
    </table>
</body>

</html>
