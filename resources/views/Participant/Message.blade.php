<html>
    <head>
        <link rel="stylesheet" href="{{ asset('/assets/css/chat/inpage-message.css') }}">
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    </head>
    <body>
        <main>
            @livewire('chat.in-page-message')
        </main>
    </body>
</html>