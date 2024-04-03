<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
</head>

<body>
    @include('CommonLayout.NavbarGoToParticipant')
    <main>
        <div class="wrapper_notify">

            <div class="first_notify">
                <header><u>All members of Farming Enjoyers have been notified</u></header>
            </div>
            <br>
            <br>

            <div class="midbar">
                <p>Registration has NOT been confirmed. Registration can only be confirmed after enough team members
                    have accepted and the entry fee has been paid.
                </p>

                <p>You can check the registration status on your team's page.</p>

                <div class="text-center">
                    <input type="submit" class="choose-payment-method" value="Done">
                </div>

                <div class="text-center">
                    <button class="oceans-gaming-another-button"> See Registration Status </button>
                </div>

            </div>
        </div>
        @include('CommonLayout.BootstrapV5Js')
    </main>
</body>

</html>
