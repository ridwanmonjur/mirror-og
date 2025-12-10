<body style="overflow-y: auto !important;">
    @include('googletagmanager::body')

    <!-- Decorative clouds -->
    <div class="clouds-container">
        <svg class="cloud cloud-1" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M170,20c0-11-9-20-20-20c-4.5,0-8.6,1.5-12,4C134.5,1.5,130.5,0,126,0c-11,0-20,9-20,20c0,0.3,0,0.7,0,1 c-11.4,1.2-20,10.8-20,22c0,12.1,9.9,22,22,22h62c11,0,20-9,20-20C190,33.8,181.2,24,170,20z" fill="white" opacity="0.9"/>
        </svg>

        <svg class="cloud cloud-2" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M170,20c0-11-9-20-20-20c-4.5,0-8.6,1.5-12,4C134.5,1.5,130.5,0,126,0c-11,0-20,9-20,20c0,0.3,0,0.7,0,1 c-11.4,1.2-20,10.8-20,22c0,12.1,9.9,22,22,22h62c11,0,20-9,20-20C190,33.8,181.2,24,170,20z" fill="white" opacity="0.8"/>
        </svg>

        <svg class="cloud cloud-3" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M170,20c0-11-9-20-20-20c-4.5,0-8.6,1.5-12,4C134.5,1.5,130.5,0,126,0c-11,0-20,9-20,20c0,0.3,0,0.7,0,1 c-11.4,1.2-20,10.8-20,22c0,12.1,9.9,22,22,22h62c11,0,20-9,20-20C190,33.8,181.2,24,170,20z" fill="white" opacity="0.75"/>
        </svg>

        <svg class="cloud cloud-4" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M170,20c0-11-9-20-20-20c-4.5,0-8.6,1.5-12,4C134.5,1.5,130.5,0,126,0c-11,0-20,9-20,20c0,0.3,0,0.7,0,1 c-11.4,1.2-20,10.8-20,22c0,12.1,9.9,22,22,22h62c11,0,20-9,20-20C190,33.8,181.2,24,170,20z" fill="white" opacity="0.85"/>
        </svg>

        <svg class="cloud cloud-5" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M170,20c0-11-9-20-20-20c-4.5,0-8.6,1.5-12,4C134.5,1.5,130.5,0,126,0c-11,0-20,9-20,20c0,0.3,0,0.7,0,1 c-11.4,1.2-20,10.8-20,22c0,12.1,9.9,22,22,22h62c11,0,20-9,20-20C190,33.8,181.2,24,170,20z" fill="white" opacity="0.82"/>
        </svg>

        <svg class="cloud cloud-6" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M170,20c0-11-9-20-20-20c-4.5,0-8.6,1.5-12,4C134.5,1.5,130.5,0,126,0c-11,0-20,9-20,20c0,0.3,0,0.7,0,1 c-11.4,1.2-20,10.8-20,22c0,12.1,9.9,22,22,22h62c11,0,20-9,20-20C190,33.8,181.2,24,170,20z" fill="white" opacity="0.88"/>
        </svg>
    </div>

    <main>
        <div class="wrapper py-4">

            @yield('signInbody')

        </div>


        <script src="{{ asset('/assets/js/shared/authValidity.js') }}"></script>
    </main>

</body>
